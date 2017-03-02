<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'tokeninput',
	'#label' => elgg_echo('matchmaker:suggest:suggested_guids'),
	'#help' => elgg_echo('matchmaker:suggest:suggested_guids:help', [$entity->getDisplayName()]),
	'name' => 'suggested_guids',
	'required' => true,
	'callback' => 'matchmaker_suggestions_autocomplete',
	'multiple' => true,
	'query' => [
		'guid' => $entity->guid,
	],
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('matchmaker:suggest:introduction'),
	'#help' => elgg_echo('matchmaker:suggest:introduction:help'),
	'name' => 'introduction',
	'rows' => 3,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);


$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('matchmaker:suggest'),
]);

elgg_set_form_footer($footer);