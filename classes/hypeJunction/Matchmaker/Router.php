<?php

namespace hypeJunction\Matchmaker;

class Router {

	/**
	 * Route /friends/suggestions
	 *
	 * @param string $hook   "route"
	 * @param string $type   "friends"
	 * @param array  $return Route
	 * @param array  $params Hook params
	 * @return boolean
	 */
	public static function routeFriends($hook, $type, $return, $params) {

		$segments = elgg_extract('segments', $return);
		$handler = array_shift($segments);

		switch ($handler) {
			case 'suggestions' :
				$username = array_shift($segments);
				echo elgg_view_resource('friends/suggestions', [
					'username' => $username,
				]);
				return false;

			case 'suggest' :
				$username = array_shift($segments);
				echo elgg_view_resource('friends/suggest', [
					'username' => $username,
				]);
				return false;
		}
	}

}
