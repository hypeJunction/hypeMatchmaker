<?php

use hypeJunction\Matchmaker\Matchmaker;

$guid = get_input('guid');
$user = get_entity($guid);

if (!$user || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('matchmaker:refresh:error'));
}

$result = elgg_delete_annotations([
	'annotation_owner_guids' => $user->guid,
	'annotation_names' => [
		Matchmaker::ANNOTATION_NAME_INFO,
		Matchmaker::ANNOTATION_NAME_SCORE,
	],
	'limit' => 0,
]);

if ($result) {
	return elgg_ok_response('', elgg_echo('matchmaker:refresh:success'));
}

return elgg_error_response(elgg_echo('matchmaker:refresh:error'));