<?php

use hypeJunction\Matchmaker\Matchmaker;

$user = elgg_get_logged_in_user_entity();
$match_guid = get_input('match_guid');

if (add_entity_relationship($user->guid, Matchmaker::RELATIONSHIP_NAME_MUTE, $match_guid)) {
	return elgg_ok_response('', elgg_echo('matchmaker:mute:success'));
}

return elgg_error_response(elgg_echo('matchmaker:mute:error'));