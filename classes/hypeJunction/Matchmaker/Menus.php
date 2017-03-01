<?php

namespace hypeJunction\Matchmaker;

use ElggMenuItem;
use ElggUser;

class Menus {

	/**
	 * Setup page menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:page"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		$user = elgg_get_page_owner_entity();
		if (!$user instanceof ElggUser) {
			return;
		}

		$return[] = ElggMenuItem::factory([
					'name' => 'matchmaker',
					'text' => elgg_echo('matchmaker:suggestions:users'),
					'href' => "friends/suggestions/$user->username",
					'context' => [
						'friends',
						'friendsof',
						'collections',
					],
		]);

		return $return;
	}

	/**
	 * Setup user hover menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:user_hover"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupUserHoverMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof ElggUser) {
			return;
		}
		
		$match = $entity->getVolatileData('matchmaker');
		if (!$match) {
			return;
		}

		$return[] = ElggMenuItem::factory(array(
					'name' => 'matchmaker:mute',
					'text' => elgg_echo('matchmaker:suggestions:mute'),
					'href' => 'action/matchmaker/mute?match_guid=' . $entity->guid,
					'is_action' => true,
					'section' => 'action',
		));

		return $return;
	}

}
