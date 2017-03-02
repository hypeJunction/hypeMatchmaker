<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return true;
}

$profile = '';
foreach (['description', 'briefdescription'] as $field) {
	if ($entity->$field) {
		$profile = elgg_view('output/longtext', [
			'value' => elgg_get_excerpt($entity->$field),
		]);
		break;
	}
}

$actions = elgg_view('framework/matchmaker/user/actions', $vars);

$stats = elgg_view('framework/matchmaker/match/direct', $vars);
$stats .= elgg_view('framework/matchmaker/match/indirect', $vars);
$stats .= elgg_view('framework/matchmaker/match/connections', $vars);
$stats .= elgg_view('framework/matchmaker/match/groups', $vars);
$stats .= elgg_view('framework/matchmaker/match/metadata', $vars);

$stats = elgg_format_element('div', [
	'class' => 'matchmaker-stats hidden',
], $stats);

echo elgg_view('object/elements/summary', [
	'entity' => $entity,
	'tags' => false,
	'content' => $profile . $actions . $stats,
	'icon' => elgg_view_entity_icon($entity, 'small'),
]);
