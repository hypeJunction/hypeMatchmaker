<?php

namespace hypeJunction\Matchmaker;

/**
 * Setup menus on pagesetup event
 * @return void
 */
function pagesetup() {

	if (elgg_is_logged_in()) {
		$user = elgg_get_page_owner_entity();

		if ($user && $user->canEdit()) {
			elgg_register_menu_item('page', array(
				'name' => 'matchmaker',
				'text' => elgg_echo('matchmaker:suggestions:users'),
				'href' => implode('/', array(MATCHMAKER_PAGEHANDLER, 'users', $user->username)),
				'context' => array('friends', 'friendsof', 'collections', MATCHMAKER_PAGEHANDLER),
			));

			if ($user->guid == elgg_get_logged_in_user_guid()) {
				elgg_register_menu_item('title', array(
					'name' => 'matchmaker',
					'text' => elgg_echo('matchmaker:suggestions:refresh'),
					'href' => 'action/matchmaker/refresh',
					'is_action' => true,
					'context' => array(MATCHMAKER_PAGEHANDLER),
					'link_class' => 'elgg-button elgg-button-action',
				));
			}
		}
	}
}
