<?php

namespace hypeJunction\Matchmaker;

use ElggPlugin;

$params = get_input('params', array(), false); // don't filter the results so that html inputs remain unchanged
$plugin = elgg_get_plugin_from_id('hypeMatchmaker');

if (!($plugin instanceof ElggPlugin)) {
	register_error(elgg_echo('plugins:settings:save:fail', array('hypeMatchmaker')));
	forward(REFERER);
}

$plugin_name = $plugin->getManifest()->getName();

$result = false;

foreach ($params as $k => $v) {
	if (is_array($v)) {
		$v = serialize($v);
	}
	$result = $plugin->setSetting($k, $v);
	if (!$result) {
		register_error(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
		forward(REFERER);
		exit;
	}
}

system_message(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
forward(REFERER);
