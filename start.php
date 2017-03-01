<?php

/**
 * Match users by existing relationships, second degree connections, group membership and profile information
 * 
 * Inspired by people_from_the_neighborhood and suggested_friends plugins
 * 
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @license GNU General Public License (GPL) version 2
 */
require_once __DIR__ . '/autoloader.php';

use hypeJunction\Matchmaker\Menus;
use hypeJunction\Matchmaker\Router;

elgg_register_event_handler('init', 'system', function() {

// Routes
	elgg_register_plugin_hook_handler('route', 'friends', [Router::class, 'routeFriends']);

	// Menus
	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);

	// Admin settings
	elgg_extend_view('plugins/hypeMatchmaker/settings', 'framework/plugins/matchmaker/settings');
	elgg_register_action('hypeMatchmaker/settings/save', __DIR__ . '/actions/settings/save.php', 'admin');

	// Actions
	elgg_register_action('matchmaker/refresh', __DIR__ . '/actions/matchmaker/refresh.php');
	elgg_register_action('matchmaker/mute', __DIR__ . '/actions/matchmaker/mute.php');
});
