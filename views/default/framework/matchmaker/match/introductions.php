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

$introductions = $match->getIntroductions();
if (empty($introductions)) {
	return;
}

foreach ($introductions as $guid => $introduction) {
	$from = get_entity($guid);
	if (!$from) {
		continue;
	}
	$link = elgg_view('output/url', [
		'href' => $from->getURL(),
		'text' => $from->getDisplayName(),
	]);
	?>
	<div class="matchmaker-stat">
		<div class="matchmaker-label">
			<?= elgg_echo('matchmaker:stats:introduction', [$link]) ?>
		</div>
		<?php
		if ($introduction) {
			$icon = elgg_view_entity_icon($from, 'tiny');
			$text = elgg_view('output/longtext', [
				'value' => elgg_format_element('blockquote', [], $introduction),
			]);
			echo elgg_view_image_block($icon, $text, [
				'class' => 'matchmaker-introduction',
			]);
		}
		?>
	</div>
	<?php
}