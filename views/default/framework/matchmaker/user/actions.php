<?php

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggUser) {
	return;
}

$menu = elgg()->menus->getMenu('user_hover', [
	'entity' => $entity,
]);

$actions = $menu->getSection('action', []);
$actions[] = ElggMenuItem::factory([
	'name' => 'details',
	'text' => elgg_echo('matchmaker:details:show'),
	'href' => '#',
	'priority' => 1,
]);

foreach ($actions as $action) {
	$action->setSection('default');
}

echo elgg_view_menu('matchmaker-actions', [
	'class' => 'elgg-menu-hz',
	'sort_by' => 'priority',
	'items' => $actions,
]);

elgg_require_js('framework/matchmaker/user/actions');