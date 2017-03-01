<?php

namespace hypeJunction\Matchmaker;

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return true;
}

$match = $entity->getVolatileData('matchmaker');

if (!$match instanceof Match) {
	return true;
}

$matched_entity = $match->getMatchedEntity();

echo '<div class="matchmaker-stats">';

if ($direct = $match->getDirectRelationships()) {
	echo '<div class="matchmaker-stats-direct-relationships">';
	array_walk($direct, function(&$val) {
		return elgg_echo("relationship:$val");
	});
	echo '<label>' . elgg_echo("matchmaker:stats:direct_relationships", array($matched_entity->name, implode(', ', $direct))) . '</label>';
	echo '</div>';
}

if ($indirect = $match->getIndirectRelationships()) {
	echo '<div class="matchmaker-stats-direct-relationships">';
	array_walk($indirect, function(&$val) {
		return elgg_echo("relationship:$val");
	});
	echo '<label>' . elgg_echo("matchmaker:stats:indirect_relationships", array($matched_entity->name, implode(', ', $indirect))) . '</label>';
	echo '</div>';
}

if ($shared_connection_guids = $match->getSharedConnectionGuids()) {
	echo '<div class="matchmaker-stats-shared-connections">';
	echo '<label>' . elgg_echo('matchmaker:stats:shared_connections', array($matched_entity->name)) . '</label>';
	echo elgg_list_entities(array(
		'guids' => $shared_connection_guids,
		'list_type' => 'gallery',
		'gallery_class' => 'elgg-gallery-users',
		'limit' => 0,
		'pagination' => false,
		'item_view' => 'framework/matchmaker/icon',
	));
	echo '</div>';
}

if ($shared_group_guids = $match->getSharedGroupGuids()) {
	echo '<div class="matchmaker-stats-shared-groups">';
	echo '<label>' . elgg_echo('matchmaker:stats:shared_groups', array($matched_entity->name)) . '</label>';
	echo elgg_list_entities(array(
		'guids' => $shared_group_guids,
		'list_type' => 'gallery',
		'gallery_class' => 'elgg-gallery-groups',
		'limit' => 0,
		'pagination' => false,
		'item_view' => 'framework/matchmaker/icon',
	));
	echo '</div>';
}

if ($shared_meta = $match->getSharedMetadata()) {
	foreach ($shared_meta as $name => $values) {
		echo '<div class="matchmaker-stats-shared-meta">';
		echo '<label>' . elgg_echo("matchmaker:stats:shared_meta", array($matched_entity->name, '<b>' . implode(', ', $values) . '</b>', elgg_echo("profile:$name"))) . '</label>';
		echo '</div>';
	}
}

echo '</div>';


