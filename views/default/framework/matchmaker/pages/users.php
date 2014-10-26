<?php

namespace hypeJunction\Matchmaker;

$page_owner = elgg_get_page_owner_entity();
$limit = elgg_extract('limit', $vars, 10);
$offset = elgg_extract('offset', $vars, 0);

$matches = Matchmaker::getMatches($page_owner->guid, 'user', '', $limit, $offset);

elgg_push_context('matchmaker');
echo elgg_view('page/components/list', $matches);
elgg_pop_context();