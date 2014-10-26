<?php

namespace hypeJunction\Matchmaker;

/**
 * Handle matchmaker pages
 * 
 * URL format:
 * - /suggestions/users/<username>
 * 
 * @param string $page    URL segments
 * @param string $handler Handler name
 * @return boolean
 */
function page_handler($page, $handler) {
		
	$context = (isset($page[0])) ? $page[0] : 'users';
	
	if (isset($page[1])) {
		$user = get_user_by_username($page[1]);
	} else {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if (!elgg_instanceof($user, 'user') || !$user->canEdit()) {
		register_error(elgg_echo('limited_access'));
		forward();
	}
	
	$view = "framework/matchmaker/pages/$context";
	if (!elgg_view_exists($view)) {
		return false;
	}
	
	elgg_set_page_owner_guid($user->guid);
	
	$params = array(
		'entity' => $user,
		'filter_context' => $context,
		'limit' => get_input('limit', 10),
		'offset' => get_input('offset', 0)
	);
	
	$content = elgg_view($view, $params);
	
	if (elgg_is_xhr()) {
		echo $content;
		exit;
	}
	
	$title = elgg_echo("matchmaker:suggestions:$context");
	$filter = elgg_view('framework/matchmaker/filter', $params);
	$sidebar = elgg_view('framework/matchmaker/sidebar', $params);
	
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => $filter,
		'sidebar' => $sidebar,
		'class' => 'matchmaker-layout',
	));
	
	echo elgg_view_page($title, $layout, 'default', array(
		'class' => 'matchmaker-page-default',
	));
	
	return true;
}