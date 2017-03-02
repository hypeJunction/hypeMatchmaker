<?php

use hypeJunction\Matchmaker\Matchmaker;

$guid = get_input('guid');
$entity = get_entity($guid);

$user = elgg_get_logged_in_user_entity();

if (!$entity instanceof ElggUser || !$entity->isFriend()) {
	return elgg_error_response(elgg_echo('matchmaker:suggest:error:friends_only'));
}

$introduction = get_input('introduction');
$suggested_guids = get_input('suggested_guids', []);
if (!is_array($suggested_guids)) {
	$suggested_guids = string_to_tag_array($suggested_guids);
}

$suggested_friends = array_unique(array_filter($suggested_friends));

if (empty($suggested_guids)) {
	return elgg_error_response(elgg_echo('matchmaker:suggest:error:suggested_guids'));
}

$success = $error = $already = 0;

$ia = elgg_set_ignore_access(true);

foreach ($suggested_guids as $suggested_guid) {
	$suggestion = get_entity($suggested_guid);
	if (!$suggestion) {
		$error++;
		continue;
	}

	if ($entity->guid == $suggestion->guid) {
		$error++;
		continue;
	}
	
	$is_friend = $entity->isFriendsWith($suggestion->guid);
	if (!$is_friend && elgg_is_active_plugin('friend_request')) {
		$is_friend = check_entity_relationship($entity->guid, 'friendrequest', $suggestion->guid);
	}

	if ($is_friend) {
		$already++;
		continue;
	}

	$intro = elgg_get_entities_from_metadata([
		'types' => 'object',
		'subtypes' => 'suggested_friend',
		'owner_guids' => $user->guid,
		'container_guids' => $entity->guid,
		'metadata_name_value_pairs' => [
			'suggested_guid' => $suggestion->guid,
		],
		'limit' => 1,
	]);

	if ($intro) {
		$already++;
		continue;
	}

	add_entity_relationship($entity->guid, 'suggested_friend', $suggestion->guid);

	$intro = new ElggObject();
	$intro->subtype = 'suggested_friend';
	$intro->description = $introduction;
	$intro->owner_guid = $user->guid;
	$intro->container_guid = $entity->guid;
	$intro->suggested_guid = $suggestion->guid;
	$intro->access_id = ACCESS_PRIVATE;
	$intro->save() ? $success++ : $error++;

	$text = '';
	if ($introduction) {
		$text = elgg_echo('matchmaker:suggest:notify:intro_text', [$introduction]);
	}

	$subject = elgg_echo('matchmaker:suggest:notify:subject');
	$message = elgg_echo('matchmaker:suggest:notify:message', [
		$user->name,
		$suggestion->name,
		$text,
		$suggestion->getURL(),
		elgg_normalize_url("friends/suggestions/$entity->username")
	]);

	$params = [
		'object' => $suggestion,
		'action' => 'suggested_friend',
		'summary' => elgg_echo('matchmaker:suggest:notify:summary', [
			$user->name,
			$suggestion->name,
		]),
		elgg_normalize_url("friends/suggestions/$entity->username"),
	];

	notify_user($entity->guid, $user->guid, $subject, $message, $params);

	Matchmaker::invalidateCache($entity->guid);
}

elgg_set_ignore_access($ia);


if ($error) {
	register_error(elgg_echo('machmaker:suggest:count:error', [$error]));
}

if ($already) {
	system_message(elgg_echo('matchmaker:suggest:count:already', [$already]));
}

if ($success) {
	system_message(elgg_echo('matchmaker:suggest:count:success', [$success]));
}
