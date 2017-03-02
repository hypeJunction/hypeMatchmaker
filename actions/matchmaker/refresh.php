<?php

use hypeJunction\Matchmaker\Matchmaker;

$guid = get_input('guid');
$user = get_entity($guid);

if (!$user || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('matchmaker:refresh:error'));
}

$result = Matchmaker::invalidateCache($user->guid);
if ($result) {
	return elgg_ok_response('', elgg_echo('matchmaker:refresh:success'));
}

return elgg_error_response(elgg_echo('matchmaker:refresh:error'));
