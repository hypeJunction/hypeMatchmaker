<?php

use hypeJunction\Matchmaker\Matchmaker;

$username = elgg_extract('username', $vars);
if ($username) {
	$user = get_user_by_username($username);
} else {
	$user = elgg_get_logged_in_user_entity();
}

if (!elgg_instanceof($user, 'user') || !$user->canEdit()) {
	forward('', '404');
}

elgg_set_page_owner_guid($user->guid);

elgg_register_menu_item('title', array(
	'name' => 'refresh',
	'text' => elgg_echo('matchmaker:suggestions:refresh'),
	'href' => elgg_http_add_url_query_elements('action/matchmaker/refresh', [
		'guid' => $user->guid,
	]),
	'is_action' => true,
	'link_class' => 'elgg-button elgg-button-action',
));

$limit = get_input('limit', 10);
$offset = get_input('offset', 0);

$matches = Matchmaker::getMatches($user->guid, 'user', '', $limit, $offset);
$matches['pagination_type'] = false;

$content = elgg_view('page/components/list', $matches);

if (elgg_is_xhr()) {
	echo $content;
} else {
	$title = elgg_echo("matchmaker:suggestions:users");
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => '',
		'class' => 'matchmaker-layout',
	));

	echo elgg_view_page($title, $layout, 'default', array(
		'class' => 'matchmaker-page-default',
	));
}
