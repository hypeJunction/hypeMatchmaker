<?php

namespace hypeJunction\Matchmaker;

use ElggEntity;
use ElggUser;

class Match {

	/**
	 * An array of options that qualifies the match
	 * @var options
	 */
	protected $options = array();

	/**
	 * Construct a new match
	 * 
	 * @param type $options
	 */
	function __construct($options = array()) {
		$this->options = $options;
	}

	/**
	 * Get a user to whom this match was made to
	 * @return ElggUser
	 */
	public function getUser() {
		if (isset($this->options['user_guid'])) {
			return get_entity($this->options['user_guid']);
		}
		return elgg_get_page_owner_entity();
	}

	/**
	 * Get entity that this match refers to
	 * @return ElggEntity
	 */
	public function getMatchedEntity() {
		if (isset($this->options['match_guid'])) {
			return get_entity($this->options['match_guid']);
		}
		return false;
	}

	/**
	 * Get a list of direct relationships
	 * @return array|boolean
	 */
	public function getDirectRelationships() {
		if (isset($this->options[Matchmaker::DIRECT_RELATIONSHIPS])) {
			return $this->options[Matchmaker::DIRECT_RELATIONSHIPS];
		}
		return false;
	}

	/**
	 * Get a list of indirect relationships
	 * @return array|boolean
	 */
	public function getIndirectRelationships() {
		if (isset($this->options[Matchmaker::INDIRECT_RELATIONSHIPS])) {
			return $this->options[Matchmaker::INDIRECT_RELATIONSHIPS];
		}
		return false;
	}

	/**
	 * Get a list of shared connection guids
	 * @return array|boolean
	 */
	public function getSharedConnectionGuids() {
		if (isset($this->options[Matchmaker::SHARED_CONNECTIONS])) {
			return $this->options[Matchmaker::SHARED_CONNECTIONS];
		}
		return false;
	}

	/**
	 * Get a list of shared group guids
	 * @return array|boolean
	 */
	public function getSharedGroupGuids() {
		if (isset($this->options[Matchmaker::SHARED_GROUPS])) {
			return $this->options[Matchmaker::SHARED_GROUPS];
		}
		return false;
	}

	/**
	 * Get a list of shared tag name value pairs
	 * @return array|boolean
	 */
	public function getSharedMetadata() {
		if (isset($this->options[Matchmaker::SHARED_METADATA])) {
			$shared_metadata = array();
			$metadata = elgg_get_metadata(array(
				'metadata_ids' => $this->options[Matchmaker::SHARED_METADATA],
				'limit' => 100,
			));
			foreach ($metadata as $md) {
				$shared_metadata[$md->name][] = $md->value;
			}
			return $shared_metadata;
		}
		
		return false;
	}

}
