<?php

/**
 * Match users by existing relationships, second degree connections, group membership and profile information
 * 
 * Inspired by people_from_the_neighborhood and suggested_friends plugins
 * 
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @license GNU General Public License (GPL) version 2
 */

namespace hypeJunction\Matchmaker;

define('MATCHMAKER_PLUGIN_ID', basename(__DIR__));

$pagehandler = elgg_get_plugin_setting('pagehandler', MATCHMAKER_PLUGIN_ID);
define('MATCHMAKER_PAGEHANDLER', $pagehandler ? : 'suggestions');

require_once __DIR__ . '/vendors/autoload.php';
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/page_handlers.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');
elgg_register_event_handler('pagesetup', 'system', __NAMESPACE__ . '\\pagesetup');

/**
 * Initialize the plugin
 * @return void
 */
function init() {

	/**
	 * PAGE HANDLING
	 */
	elgg_register_page_handler(MATCHMAKER_PAGEHANDLER, __NAMESPACE__ . '\\page_handler');

	/**
	 * ADMIN
	 */
	elgg_extend_view('plugins/' . MATCHMAKER_PLUGIN_ID . '/settings', 'framework/plugins/matchmaker/settings');
	elgg_register_action(MATCHMAKER_PLUGIN_ID . '/settings/save', __DIR__ . '/actions/settings/save.php', 'admin');
	
	/**
	 * VIEWS
	 */
	elgg_extend_view('object/elements/summary', 'framework/matchmaker/match');
	
	/**
	 * ACTIONS
	 */
	elgg_register_action('matchmaker/refresh', __DIR__ . '/actions/matchmaker/refresh.php');
	elgg_register_action('matchmaker/mute', __DIR__ . '/actions/matchmaker/mute.php');
	
	/**
	 * HOOKS
	 */
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');
	elgg_register_plugin_hook_handler('view', 'group/default', __NAMESPACE__ . '\\default_group_view');
	
	/**
	 * TESTS
	 */
	//elgg_register_plugin_hook_handler('unit_test', 'system', __NAMESPACE__ . '\\unit_test');
}
