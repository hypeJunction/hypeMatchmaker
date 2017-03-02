<?php

$username = elgg_extract('username', $vars);
$user = get_user_by_username($username);

if (!elgg_instanceof($user, 'user') || !$user->isFriend()) {
	forward('', '404');
}

elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

$content = elgg_view_form('matchmaker/suggest', [], [
	'entity' => $user,
]);

if (elgg_is_xhr()) {
	echo $content;
} else {
	$title = elgg_echo("matchmaker:suggest:title", [$user->getDisplayName()]);
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
