<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return true;
}

$stats = elgg_view('framework/matchmaker/match/direct', $vars);
$stats .= elgg_view('framework/matchmaker/match/indirect', $vars);
$stats .= elgg_view('framework/matchmaker/match/connections', $vars);
$stats .= elgg_view('framework/matchmaker/match/groups', $vars);
$stats .= elgg_view('framework/matchmaker/match/metadata', $vars);

$stats = elgg_format_element('div', [
	'class' => 'matchmaker-stats',
], $stats);

echo elgg_view('object/elements/summary', [
	'entity' => $entity,
	'tags' => false,
	'content' => $stats,
	'icon' => elgg_view_entity_icon($entity, 'small'),
]);
