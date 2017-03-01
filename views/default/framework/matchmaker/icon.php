<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$size = elgg_extract('size', $vars, 'tiny');
echo elgg_view_entity_icon($entity, $size);
