<?php

use hypeJunction\Matchmaker\Match;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

$match = $entity->getVolatileData('matchmaker');
if (!$match instanceof Match) {
	return true;
}

$shared_connection_guids = $match->getSharedConnectionGuids();
if (empty($shared_connection_guids)) {
	return;
}
?>
<div class="matchmaker-stat">
	<div class="matchmaker-label">
		<?= elgg_echo('matchmaker:stats:shared_connections', [$entity->getDisplayName()]) ?>
	</div>
	<?=
	elgg_list_entities([
		'guids' => $shared_connection_guids,
		'list_type' => 'gallery',
		'gallery_class' => 'elgg-gallery-users',
		'limit' => 0,
		'pagination' => false,
		'item_view' => 'framework/matchmaker/icon',
	]);
	?>
</div>