<?php

namespace hypeJunction\Matchmaker;

use ElggMenuItem;

/**
 * Setup user hover menu
 * 
 * @param string $hook   "register"
 * @param string $type   "menu:user_hover"
 * @param array  $return Menu
 * @param array  $params Hook Params
 * @return array
 */
function user_hover_menu_setup($hook, $type, $return, $params) {
	
	$entity = elgg_extract('entity', $params);
	
	$match = $entity->getVolatileData('matchmaker');
	if (!$match) {
		return $return;
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

/**
 * Replace default group view to show proper gallery
 * 
 * @param string $hook   "view"
 * @param string $type   "group/default"
 * @param string $return HTML
 * @param array  $params Hook params
 * @return string
 */
function default_group_view($hook, $type, $return, $params) {
	if (!elgg_in_context('matchmaker') || $params['vars']['list_type'] !== 'gallery') {
		return $return;
	}
	
	return elgg_view('framework/matchmaker/group', $params['vars']);
}

/**
 * Run unit tests
 *
 * @param string $hook   Equals 'unit_test'
 * @param string $type   Equals 'system'
 * @param array  $value  An array of unit test locations
 * @param array  $params Additional params
 * @return string[] Updated array of unit test locations
 */
function unit_test($hook, $type, $value, $params) {

	$path = elgg_get_plugins_path();
	//$value[] = $path . PLUGIN_ID . '/tests/MatchmakerTest.php';
	return $value;
}
