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

use hypeJunction\Matchmaker\Friendships;
use hypeJunction\Matchmaker\Menus;
use hypeJunction\Matchmaker\Router;

elgg_register_event_handler('init', 'system', function() {

	// Routes
	elgg_register_plugin_hook_handler('route', 'friends', [Router::class, 'routeFriends']);

	// Menus
	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);

	// Admin settings
	elgg_register_action('hypeMatchmaker/settings/save', __DIR__ . '/actions/settings/save.php', 'admin');

	// Friendship status changes
	elgg_register_event_handler('create', 'relationship', [Friendships::class, 'relationshipCreated']);

	// Actions
	elgg_register_action('matchmaker/refresh', __DIR__ . '/actions/matchmaker/refresh.php');
	elgg_register_action('matchmaker/mute', __DIR__ . '/actions/matchmaker/mute.php');
	elgg_register_action('matchmaker/suggest', __DIR__ . '/actions/matchmaker/suggest.php');

	elgg_extend_view('elgg.css', 'framework/matchmaker/match.css');

	// Widgets
	elgg_register_widget_type('friend_suggestions', elgg_echo('widget:friend_suggestions'), elgg_echo('widget:friend_suggestions:desc'), ['dashboard']);
});

/**
 * Callback function to search users
 *
 * @param string $term    Query term
 * @param array  $options An array of getter options
 * @return array An array of elgg entities matching the search criteria
 */
function matchmaker_suggestions_autocomplete($term, $options = array()) {

	$options['query'] = $term;

	$guid = (int) get_input('guid');
	$logged_in_guid = (int) elgg_get_logged_in_user_guid();

	$rels = \hypeJunction\Matchmaker\Matchmaker::getFriendshipRelationshipNames();
	$rels[] = 'suggested_friend';

	$in_friendships = [];
	foreach ($rels as $rel) {
		$in_friendships[] = "'$rel'";
	}
	$in_friendships = implode(',', $in_friendships);
	
	$dbprefix = elgg_get_config('dbprefix');
	$options['wheres'][] = "
		NOT EXISTS(
			SELECT 1 FROM {$dbprefix}entity_relationships
			WHERE guid_one = $guid
			AND relationship IN ($in_friendships)
			AND guid_two = e.guid
		)
	";

	$options['wheres'][] = "
		EXISTS(
			SELECT 1 FROM {$dbprefix}entity_relationships
			WHERE guid_one = $logged_in_guid
			AND relationship IN ('friend')
			AND guid_two = e.guid
		)
	";

	$options['wheres'][] = "e.guid != $guid";

	$results = elgg_trigger_plugin_hook('search', 'user', $options, array());
	return elgg_extract('entities', $results, array());
}
