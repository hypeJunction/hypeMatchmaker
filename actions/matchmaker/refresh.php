<?php

namespace hypeJunction\Matchmaker;

$user = elgg_get_logged_in_user_entity();

$result = elgg_delete_annotations(array(
	'annotation_owner_guids' => $user->guid,
	'annotation_names' => array(Matchmaker::ANNOTATION_NAME_INFO, Matchmaker::ANNOTATION_NAME_SCORE),
	'limit' => 0,
));

if ($result) {
	system_message(elgg_echo('matchmaker:refresh:success'));
} else {
	reigster_error(elgg_echo('matchmaker:refresh:error'));
}

forward(REFERER);
