<?php

namespace hypeJunction\Matchmaker;

class Friendships {

	/**
	 * Perform operations when friendship status changes
	 * 
	 * @param string           $event        "create"
	 * @param string           $type         "relationship"
	 * @param ElggRelationship $relationship Relationship
	 * @return void
	 */
	public static function relationshipCreated($event, $type, $relationship) {

		$rels = Matchmaker::getFriendshipRelationshipNames();

		if (!in_array($relationship->relationship, $rels)) {
			return;
		}

		$ia = elgg_set_ignore_access(true);

		// clear suggestions cache
		Matchmaker::invalidateCache($relationship->guid_one);
		Matchmaker::invalidateCache($relationship->guid_two);

		// delete introductions
		$intros = elgg_get_entities_from_metadata([
			'types' => 'object',
			'subtypes' => 'suggested_friend',
			'container_guids' => (int) $relationship->guid_one,
			'metadata_name_value_pairs' => [
				'suggested_guid' => (int) $relationship->guid_two,
			],
			'limit' => 0,
		]);
		
		foreach ($intros as $intro) {
			$intro->delete();
		}

		remove_entity_relationship($relationship->guid_one, 'suggested_friend', $relationship->guid_two);

		elgg_set_ignore_access($ia);
	}

}
