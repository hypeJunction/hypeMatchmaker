<?php

namespace hypeJunction\Matchmaker;

$user = elgg_get_logged_in_user_entity();
$match_guid = get_input('match_guid');

if (add_entity_relationship($user->guid, Matchmaker::RELATIONSHIP_NAME_MUTE, $match_guid)) {
	system_message(elgg_echo('matchmaker:mute:success'));
} else {
	reigster_error(elgg_echo('matchmaker:mute:error'));
}

forward(REFERER);
