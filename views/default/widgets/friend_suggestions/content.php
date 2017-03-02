<?php

use hypeJunction\Matchmaker\Matchmaker;

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser || !$user->canEdit()) {
	return;
}

$limit = get_input('limit', 4);
$offset = get_input('offset', 0);

$matches = Matchmaker::getMatches($user->guid, 'user', '', $limit, $offset);
$matches['pagination'] = false;

$content = elgg_view('page/components/list', $matches);

echo $content;

$more_link = elgg_view('output/url', [
	'href' => "friends/suggestions/$user->guid",
	'text' => elgg_echo('matchmaker:more'),
]);

echo elgg_format_element('div', [
	'class' => 'elgg-widget-more',
], $more_link);

