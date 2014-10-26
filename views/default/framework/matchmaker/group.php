<?php

namespace hypeJunction\Matchmaker;

$entity = elgg_extract('entity', $vars);

echo elgg_view_entity_icon($entity, elgg_extract('size', $vars, 'tiny'));
