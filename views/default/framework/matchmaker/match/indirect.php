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

$indirect = $match->getIndirectRelationships();
if (!$indirect) {
	return;
}

$relationships = array_map(function($val) {
	return elgg_echo("relationship:$val");
}, $indirect);

$relationships = implode(', ', $relationships);
?>
<div class="matchmaker-stat">
	<div class="matchmaker-label">
		<?= elgg_echo("matchmaker:stats:indirect_relationships", [$entity->getDisplayName(), $relationships]) ?>
	</div>
</div>