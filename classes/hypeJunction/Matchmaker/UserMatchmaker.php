<?php

namespace hypeJunction\Matchmaker;

use ElggBatch;
use InvalidArgumentException;

class UserMatchmaker extends Matchmaker {

	/**
	 * Get matches for the user
	 * 
	 * @param int         $user_guid      GUID of the user
	 * @param string      $entity_type    Type of entity
	 * @param string|null $entity_subtype Subtype of users to match
	 * @param int         $limit          Number of users to return
	 * @param int         $offset         Offset
	 * @return array|false An array suitable for passing to 'page/components/list' or false on exception
	 */
	public static function getMatches($user_guid = 0, $entity_type = 'user', $entity_subtype = null, $limit = 10, $offset = 0) {

		try {
			$obj = new UserMatchmaker($user_guid, $entity_type, $entity_subtype, $limit, $offset);
			return $obj->getMatchesFromCache();
		} catch (InvalidArgumentException $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
		}

		return false;
	}

	/**
	 * Construct a new object
	 * 
	 * @param int         $user_guid      GUID of the user
	 * @param string      $entity_type    Type of entity to match
	 * @param string|null $entity_subtype Subtype of entities to match
	 * @param int         $limit          Number of users to return
	 * @param int         $offset         Offset
	 */
	protected function __construct($user_guid = 0, $entity_type = 'user', $entity_subtype = null, $limit = 10, $offset = 0) {
		parent::__construct($user_guid, $entity_type, $entity_subtype, $limit, $offset);
	}

	/**
	 * Get matching users from store annotations
	 * @return array An array suitable for passing to 'page/components/list'
	 */
	protected function getMatchesFromCache() {

		$in_friendships = $this->getInFriendshipsClause();
		$options = array(
			'types' => 'user',
			'subtypes' => $this->subtype,
			'calculation' => 'MAX',
			'annotation_names' => self::ANNOTATION_NAME_SCORE,
			'annotation_owner_guids' => $this->user->guid,
			'count' => true,
			'limit' => $this->limit,
			'offset' => $this->offset,
			'wheres' => array(
				"(NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r
					WHERE r.guid_one = e.guid 
					AND r.guid_two = n_table.owner_guid 
					AND r.relationship IN ($in_friendships)))", // current user hasn't added the suggested person as friend
			)
		);

		$count = elgg_get_entities_from_annotation_calculation($options);

		if (!$count) {
			$this->getMatchesFromDatabase();
		}

		unset($options['count']);

		$items = elgg_get_entities_from_annotation_calculation($options);
		if ($items) {
			foreach ($items as $item) {
				$annotations = elgg_get_annotations(array(
					'guids' => $item->guid,
					'annotation_names' => self::ANNOTATION_NAME_INFO,
					'annotation_owner_guids' => $this->user->guid,
					'limit' => 1,
				));
				if (is_array($annotations) && count($annotations)) {
					$info = unserialize($annotations[0]->value);
					$info['user_guid'] = $this->user->guid;
					$info['match_guid'] = $item->guid;
					$item->setVolatileData('matchmaker', new Match($info));
				}
			}
		}
		return array(
			'count' => $count,
			'items' => $items,
			'limit' => $this->limit,
			'offset' => $this->offset,
			'no_results' => elgg_echo('matchmaker:no_results'),
			'item_view' => 'framework/matchmaker/match',
		);
	}

	/**
	 * Run sql queries and build an array of matches
	 * 
	 * @return void
	 */
	protected function getMatchesFromDatabase() {
		$this->getDirectRelationshipMatches();
		$this->getIndirectRelationshipMatches();
		$this->getSecondDegreeMatches();
		$this->getSharedGroupsMatches();
		$this->getMetadataMatches();
		$this->cache();
	}

	/**
	 * Get users that this user has a relationship with exluding users this user has friended
	 * @return void
	 */
	protected function getDirectRelationshipMatches() {

		$weight = $this->getDirectRelationshipWeight();
		$in_connections = $this->getInConnectionsClause();
		$in_friendships = $this->getInFriendshipsClause();

		if (!$in_connections || !$in_friendships || !$weight) {
			return;
		}

		$suggestions = new ElggBatch('elgg_get_entities_from_relationship', array(
			'types' => 'user',
			'subtypes' => $this->entity_subtype,
			'selects' => array('COUNT(r.relationship) as score', 'GROUP_CONCAT(r.relationship) as relationships'),
			'group_by' => 'r.guid_two',
			'relationship_guid' => $this->user->guid,
			'wheres' => array(
				"(r.relationship IN ($in_connections))",
				"NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r2 
					WHERE r2.guid_one = r.guid_one 
					AND r2.guid_two = r.guid_two
					AND r2.relationship IN ($in_friendships))", // current user hasn't added the suggested person as friend
			),
			'callback' => false,
			'limit' => 0,
		));

		foreach ($suggestions as $suggestion) {
			if (!isset($this->matches[$suggestion->guid])) {
				$this->matches[$suggestion->guid] = array();
			}
			$this->matches[$suggestion->guid][self::SCORE] += $suggestion->score * $weight;
			$this->matches[$suggestion->guid][self::DIRECT_RELATIONSHIPS] = explode(',', $suggestion->relationships);
		}
	}

	/**
	 * Get users that have a relationship with this user including users that have added this user as  friend but haven't been added back
	 * @return void
	 */
	protected function getIndirectRelationshipMatches() {

		$weight = $this->getIndirectRelationshipWeight();
		$in_connections = $this->getInConnectionsClause();
		$in_friendships = $this->getInFriendshipsClause();

		if (!$in_connections || !$in_friendships || !$weight) {
			return;
		}

		$suggestions = new ElggBatch('elgg_get_entities_from_relationship', array(
			'types' => 'user',
			'subtypes' => $this->entity_subtype,
			'selects' => array('COUNT(r.relationship) as score', 'GROUP_CONCAT(r.relationship) as relationships'),
			'group_by' => 'r.guid_one',
			'relationship_guid' => $this->user->guid,
			'inverse_relationship' => true,
			'wheres' => array(
				"(r.relationship IN ($in_connections))",
				"NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r2 
					WHERE r2.guid_one = r.guid_two 
					AND r2.guid_two = r.guid_one
					AND r2.relationship IN ($in_friendships))", // current user hasn't added the suggested person as friend
			),
			'callback' => false,
			'limit' => 0,
		));

		foreach ($suggestions as $suggestion) {
			if (!isset($this->matches[$suggestion->guid])) {
				$this->matches[$suggestion->guid] = array();
			}
			$this->matches[$suggestion->guid][self::SCORE] += $suggestion->score * $weight;
			$this->matches[$suggestion->guid][self::INDIRECT_RELATIONSHIPS] = explode(',', $suggestion->relationships);
		}
	}

	/**
	 * Get connections of connections
	 * @return void
	 */
	protected function getSecondDegreeMatches() {

		$weight = $this->getSecondDegreeWeight();
		$in_connections = $this->getInConnectionsClause();
		$in_friendships = $this->getInFriendshipsClause();

		if (!$in_connections || !$in_friendships || !$weight) {
			return;
		}

		$suggestions = new ElggBatch('elgg_get_entities', array(
			'types' => 'user',
			'subtypes' => $this->entity_subtype,
			'selects' => array('COUNT(r2.relationship) as score', 'GROUP_CONCAT(r2.guid_two) as shared_connections'),
			'group_by' => 'r1.guid_one',
			'joins' => array(
				"JOIN {$this->dbprefix}entity_relationships r1 ON e.guid = r1.guid_one",
				"JOIN {$this->dbprefix}entity_relationships r2 ON r1.guid_two = r2.guid_two",
			),
			'wheres' => array(
				"r1.relationship IN ($in_connections)", // bridging relationships
				"r2.relationship IN ($in_connections)", // bridging relationships
				"r1.guid_one != r2.guid_one", // exclude connections to self via bridging relationships
				"r2.guid_one = {$this->user->guid}", // connected to self via bridging relationships
				"NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r3 
					WHERE r3.guid_one = r2.guid_one 
					AND r3.guid_two = r1.guid_one
					AND r3.relationship IN ($in_friendships))", // no direct relationship with the suggested person
			),
			'callback' => false,
			'limit' => 0,
		));

		foreach ($suggestions as $suggestion) {
			if (!isset($this->matches[$suggestion->guid])) {
				$this->matches[$suggestion->guid] = array();
			}
			$this->matches[$suggestion->guid][self::SCORE] += $suggestion->score * $weight;
			$this->matches[$suggestion->guid][self::SHARED_CONNECTIONS] = explode(',', $suggestion->shared_connections);
		}
	}

	/**
	 * Get shared group members
	 * @return void
	 */
	protected function getSharedGroupsMatches() {

		$weight = $this->getSharedGroupWeight();
		$in_memberships = $this->getInMembershipsClause();
		$in_friendships = $this->getInFriendshipsClause();

		if (!$in_memberships || !$in_friendships || !$weight) {
			return;
		}

		// Get shared group members
		$suggestions = new ElggBatch('elgg_get_entities', array(
			'types' => 'user',
			'subtypes' => $this->entity_subtype,
			'selects' => array('COUNT(r2.relationship) as score', 'GROUP_CONCAT(r2.guid_two) as groups'),
			'group_by' => 'r1.guid_one',
			'joins' => array(
				"JOIN {$this->dbprefix}entity_relationships r1 ON e.guid = r1.guid_one",
				"JOIN {$this->dbprefix}entity_relationships r2 ON r1.guid_two = r2.guid_two",
				"JOIN {$this->dbprefix}groups_entity ge ON ge.guid = r2.guid_two",
			),
			'wheres' => array(
				"r1.relationship IN ($in_memberships)", // bridging relationships
				"r2.relationship IN ($in_memberships)", // bridging relationships
				"r1.guid_one != r2.guid_one", // exclude connections to self via bridging relationships
				"r2.guid_one = {$this->user->guid}", // connected to self via bridging relationships
				"NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r3 
					WHERE r3.guid_one = r2.guid_one 
					AND r3.guid_two = r1.guid_one
					AND r3.relationship IN ($in_friendships))", // no direct relationship with the suggested person
			),
			'callback' => false,
			'limit' => 0,
		));

		foreach ($suggestions as $suggestion) {
			if (!isset($this->matches[$suggestion->guid])) {
				$this->matches[$suggestion->guid] = array();
			}
			$this->matches[$suggestion->guid][self::SCORE] += $suggestion->score . $weight;
			$this->matches[$suggestion->guid][self::SHARED_GROUPS] = explode(',', $suggestion->groups);
		}
	}

	/**
	 * Match by profile fields
	 * @return void
	 */
	protected function getMetadataMatches() {

		$weight = $this->getSecondDegreeWeight();
		$in_metadata_names = $this->getInMetadataClause();
		$in_friendships = $this->getInFriendshipsClause();

		if (!$in_metadata_names || !$in_friendships || !$weight) {
			return;
		}

		$suggestions = new ElggBatch('elgg_get_entities', array(
			'types' => 'user',
			'subtypes' => $this->entity_subtype,
			'selects' => array('COUNT(md2.id) as score', "GROUP_CONCAT(md2.id) as metadata_ids"),
			'group_by' => 'md2.id',
			'joins' => array(
				"JOIN {$this->dbprefix}metadata md1 ON e.guid = md1.entity_guid",
				"JOIN {$this->dbprefix}metadata md2 ON md1.value_id = md2.value_id",
			),
			'wheres' => array(
				"md1.name_id IN ($in_metadata_names)",
				"md2.name_id IN ($in_metadata_names)",
				"md1.entity_guid != md2.entity_guid", // exclude connections to self via tags
				"md2.entity_guid = {$this->user->guid}",
				"NOT EXISTS(SELECT * 
					FROM {$this->dbprefix}entity_relationships r
					WHERE r.guid_one = md2.entity_guid
					AND r.guid_two = md1.entity_guid
					AND r.relationship IN ($in_friendships))", // no direct relationship with the suggested person
			),
			'callback' => false,
			'limit' => 0,
		));
					
		foreach ($suggestions as $suggestion) {
			if (!isset($this->matches[$suggestion->guid])) {
				$this->matches[$suggestion->guid] = array();
			}
			$this->matches[$suggestion->guid][self::SCORE] += $suggestion->score * $weight;
			$this->matches[$suggestion->guid][self::SHARED_METADATA] = explode(',', $suggestion->metadata_ids);
		}
	}

}
