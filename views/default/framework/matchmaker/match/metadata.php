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

$shared_meta = $match->getSharedMetadata();
if (empty($shared_meta)) {
	return;
}

foreach ($shared_meta as $name => $values) {
	$values = elgg_format_element('b', [], implode(', ', $values));
	$label = elgg_echo("profile:$name");
	?>
	<div class="matchmaker-stat">
		<div class="matchmaker-label">
			<?= elgg_echo('matchmaker:stats:shared_meta', [$entity->getDisplayName(), $values, $label]) ?>
		</div>

	</div>
	<?php
}
