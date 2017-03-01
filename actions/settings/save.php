<?php

namespace hypeJunction\Matchmaker;

use ElggPlugin;

$params = get_input('params', array(), false); // don't filter the results so that html inputs remain unchanged
$plugin = elgg_get_plugin_from_id('hypeMatchmaker');

if (!($plugin instanceof ElggPlugin)) {
	return elgg_error_response(elgg_echo('plugins:settings:save:fail', ['hypeMatchmaker']));
}

$plugin_name = $plugin->getManifest()->getName();

$result = false;

foreach ($params as $k => $v) {
	if (is_array($v)) {
		$v = serialize($v);
	}
	$result = $plugin->setSetting($k, $v);
	if (!$result) {
		return elgg_error_response(elgg_echo('plugins:settings:save:fail', [$plugin_name]));
	}
}

return elgg_ok_response('', elgg_echo('plugins:settings:save:ok', [$plugin_name]));
