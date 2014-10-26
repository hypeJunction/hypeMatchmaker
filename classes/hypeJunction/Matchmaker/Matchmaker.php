<?php

namespace hypeJunction\Matchmaker;

use ElggEntity;
use ElggUser;
use InvalidArgumentException;

class Matchmaker {

	/**
	 * DB prefix
	 * @var string
	 */
	protected $dbprefix;

	/**
	 * Found matches
	 * @var array 
	 */
	protected $matches = array();

	/**
	 * User
	 * @var ElggUser 
	 */
	protected $user;

	/**
	 * Type of entities to match against
	 * @var string
	 */
	protected $entity_type;

	/**
	 * Subtype of type
	 * @var string
	 */
	protected $entity_subtype;

	/**
	 * Number of items per page
	 * @var int 
	 */
	protected $limit;

	/**
	 * Offset
	 * @var int 
	 */
	protected $offset;

	const SCORE = 'score';

	const ANNOTATION_NAME_SCORE = 'matchmaker_score';
	const ANNOTATION_NAME_INFO = 'matchmaker_info';
	
	const SHARED_CONNECTIONS = 'shared_connections';
	const SHARED_GROUPS = 'shared_groups';
	const SHARED_METADATA = 'metadata_ids';
	const DIRECT_RELATIONSHIPS = 'direct_relationships';
	const INDIRECT_RELATIONSHIPS = 'indirect_relationships';
	const SECOND_DEGREE_RELATIONSHIPS = 'second_degree';
	const SHARED_GROUP_RELATIONSHIPS = 'shared_group';
	const SHARED_METADATA_RELATIONSHIPS = 'metadata';
	const CONNECTION_RELATIONSHIP_NAMES = 'connection_relationship_names';
	const FRIENDSHIP_RELATIONSHIP_NAMES = 'friendship_relationship_names';
	const MEMBERSHIP_RELATIONSHIP_NAMES = 'membership_relationship_names';
	const METADATA_NAMES = 'metadata_names';
	const RELATIONSHIP_NAME_MUTE = 'matchmaker_mute';
	
	/**
	 * Construct a new object
	 * 
	 * @param int         $user_guid      GUID of the user
	 * @param string      $entity_type    Type of entity to match
	 * @param string|null $entity_subtype Subtype of entities to match
	 * @param int         $limit          Number of users to return
	 * @param int         $offset         Offset
	 * @return ElggEntity[]|false
	 */
	public static function getMatches($user_guid = 0, $entity_type = 'user', $entity_subtype = null, $limit = 10, $offset = 0) {

		try {

			switch ($entity_type) {
				default:
				default :
				case 'user' :
					return call_user_func_array(array(
						__NAMESPACE__ . '\\UserMatchmaker',
						'getMatches'
							), array(
						$user_guid,
						$entity_type,
						$entity_subtype,
						$limit,
						$offset
					));
			}
		} catch (InvalidArgumentException $ex) {
			elgg_log($ex->getMessage(), 'ERROR');
		}

		return false;
	}

	/**
	 * Construct a new Matchmaker object
	 * 
	 * @param int    $user_guid      GUID of the user whom suggestions are being offered
	 * @param string $entity_type    Type of entities to suggest
	 * @param string $entity_subtype Subtype of entities to suggest
	 * @param int         $limit          Number of users to return
	 * @param int         $offset         Offset
	 * @return Matchmaker
	 */
	protected function __construct($user_guid = 0, $entity_type = 'user', $entity_subtype = null, $limit = 10, $offset = 0) {

		$user = get_entity($user_guid);
		if (!elgg_instanceof($user, 'user')) {
			throw new InvalidArgumentException($user_guid . ' is not a valid user guid');
		}

		if (!in_array($entity_type, array('user', 'group', 'object', 'site'))) {
			throw new InvalidArgumentException($entity_type . ' is not a valid entity type');
		}

		if ($entity_subtype && !get_subtype_id($entity_type, $entity_subtype)) {
			throw new InvalidArgumentException($entity_subtype . ' is not a valid entity subtype');
		}

		$this->user = $user;
		$this->entity_type = $entity_type;
		$this->entity_subtype = $entity_subtype;
		$this->limit = $limit;
		$this->offset = $offset;
		$this->dbprefix = elgg_get_config('dbprefix');
	}

	/**
	 * Get weight of the direct relationship matches
	 * @return float
	 */
	public static  function getDirectRelationshipWeight() {
		return (float) elgg_get_plugin_setting(self::DIRECT_RELATIONSHIPS, MATCHMAKER_PLUGIN_ID);
	}

	/**
	 * Get weight of the indirect relationship matches
	 * @return float
	 */
	public static  function getIndirectRelationshipWeight() {
		return (float) elgg_get_plugin_setting(self::INDIRECT_RELATIONSHIPS, MATCHMAKER_PLUGIN_ID);
	}

	/**
	 * Get weight of the second degree connection matches
	 * @return float
	 */
	public static  function getSecondDegreeWeight() {
		return (float) elgg_get_plugin_setting(self::SECOND_DEGREE_RELATIONSHIPS, MATCHMAKER_PLUGIN_ID);
	}

	/**
	 * Get weight of the shared group member matches
	 * @return float
	 */
	public static  function getSharedGroupWeight() {
		return (float) elgg_get_plugin_setting(self::SHARED_GROUP_RELATIONSHIPS, MATCHMAKER_PLUGIN_ID);
	}

	/**
	 * Get weight of the share profile tags matches
	 * @return float
	 */
	public static  function getMetadataWeight() {
		return (float) elgg_get_plugin_setting(self::SHARED_METADATA_RELATIONSHIPS, MATCHMAKER_PLUGIN_ID);
	}

	/**
	 * Get relationship names treated as user to user connections
	 * @return array
	 */
	public static  function getConnectionRelationshipNames() {
		$names = elgg_get_plugin_setting(self::CONNECTION_RELATIONSHIP_NAMES, MATCHMAKER_PLUGIN_ID);
		if (is_string($names)) {
			$names = unserialize($names);
		}
		if (is_array($names) && count($names) > 0) {
			return array_unique($names);
		}
		return array('friend');
	}

	/**
	 * Get relationship names treated as user to user friendships
	 * @return array
	 */
	public static  function getFriendshipRelationshipNames() {
		$names = elgg_get_plugin_setting(self::FRIENDSHIP_RELATIONSHIP_NAMES, MATCHMAKER_PLUGIN_ID);
		if (is_string($names)) {
			$names = unserialize($names);
		}
		if (is_array($names) && count($names) > 0) {
			$names[] = RELATIONSHIP_NAME_MUTE;
			return array_unique($names);
		}
		return array('friend', 'matchmaker_ignored');
	}

	/**
	 * Get relationship names treated as group memberships
	 * @return array
	 */
	public static  function getMembershipRelationshipNames() {
		$names = elgg_get_plugin_setting(self::MEMBERSHIP_RELATIONSHIP_NAMES, MATCHMAKER_PLUGIN_ID);
		if (is_string($names)) {
			$names = unserialize($names);
		}
		if (is_array($names) && count($names) > 0) {
			return array_unique($names);
		}
		return array('member');
	}

	/**
	 * Get metadata names of profile fields to match
	 * @return array
	 */
	public static  function getMetadataNames() {
		$names = elgg_get_plugin_setting(self::METADATA_NAMES, MATCHMAKER_PLUGIN_ID);
		if (is_string($names)) {
			$names = unserialize($names);
		}
		if (is_array($names) && count($names) > 0) {
			return array_unique($names);
		}
		return array();
	}

	/**
	 * Prepare sql injection
	 * @return string|false
	 */
	protected function getInConnectionsClause() {
		$connections = $this->getConnectionRelationshipNames();
		return $this->formatInClause($connections);
	}

	/**
	 * Prepare sql injection
	 * @return string|false
	 */
	protected function getInFriendshipsClause() {
		$friendships = $this->getFriendshipRelationshipNames();
		return $this->formatInClause($friendships);
	}

	/**
	 * Prepare sql injection
	 * @return string|false
	 */
	protected function getInMembershipsClause() {
		$memberships = $this->getMembershipRelationshipNames();
		return $this->formatInClause($memberships);
	}

	/**
	 * Prepare sql injection
	 * @return string|false
	 */
	protected function getInMetadataClause() {
		$metadata_names = $this->getMetadataNames();
		// Get users with same metadata tags
		$metadata_name_ids = array();
		foreach ($metadata_names as $name) {
			$metadata_name_ids[] = elgg_get_metastring_id($name);
		}
		return implode(',', $metadata_name_ids);
	}

	/**
	 * Prepare sql injection
	 * @return string|false
	 */
	protected function formatInClause($names) {
		if (!is_array($names) || !count(array_filter($names))) {
			return false;
		}

		$sanitised_names = array();
		foreach ($names as $name) {
			$sanitised_names[] = '\'' . sanitise_string($name) . '\'';
		}

		return implode(',', $sanitised_names);
	}

	/**
	 * Sort matches
	 * 
	 * @param string $by Sort option
	 * @return void
	 */
	protected function sort($by = 'score') {
		switch ($by) {
			default :
			case 'score' :
				usort($this->matches, function($a, $b) {
					if ($a['score'] == $b['score']) {
						return 0;
					}
					return ($a['score'] < $b['score']) ? 1 : -1;
				});
				break;
		}
	}

	/**
	 * Cache match information in annotations on the matched user
	 * Annotation is owned by current user
	 */
	protected function cache() {

		$this->invalidateCache();

		if (!count($this->matches)) {
			return;
		}
		
		foreach ($this->matches as $guid => $info) {
			create_annotation($guid, self::ANNOTATION_NAME_SCORE, $info['score'], '', $this->user->guid, ACCESS_PUBLIC);
			create_annotation($guid, self::ANNOTATION_NAME_INFO, serialize($info), '', $this->user->guid, ACCESS_PRIVATE);
		}
	}

	/**
	 * Remove cached match information
	 */
	protected function invalidateCache() {
		return elgg_delete_annotations(array(
			'annotation_owner_guids' => $this->user->guid,
			'annotation_names' => array(self::ANNOTATION_NAME_INFO, self::ANNOTATION_NAME_SCORE),
			'limit' => 0,
		));
	}

}
