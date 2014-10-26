<?php

use hypeJunction\Matchmaker\UserMatchmaker;

$entity = elgg_extract('entity', $vars);
$dbprefix = elgg_get_config('dbprefix');

// User to user relationship names
$query = "SELECT DISTINCT(r.relationship) 
		FROM {$dbprefix}entity_relationships r
		JOIN {$dbprefix}users_entity u1 ON u1.guid = r.guid_one
		JOIN {$dbprefix}users_entity u2 ON u2.guid = r.guid_two";

$rows = get_data($query);
$user_to_user_relationship_names = array();

if ($rows) {
	foreach ($rows as $r) {
		$user_to_user_relationship_names[$r->relationship] = $r->relationship;
	}
}

$setting_name = UserMatchmaker::CONNECTION_RELATIONSHIP_NAMES;
echo '<div>';
echo '<label>' . elgg_echo("matchmaker:settings:connection_relationship_names") . '</label>';
echo '<span class="elgg-text-help">' . elgg_echo("matchmaker:settings:connection_relationship_names:help") . '</span>';
echo elgg_view('input/checkboxes', array(
	'default' => false,
	'name' => "params[$setting_name]",
	'value' => (isset($entity->$setting_name)) ? unserialize($entity->$setting_name) : array('friend'),
	'options' => $user_to_user_relationship_names,
));
echo '</div>';

$setting_name = UserMatchmaker::FRIENDSHIP_RELATIONSHIP_NAMES;
echo '<div>';
echo '<label>' . elgg_echo("matchmaker:settings:friendship_relationship_names") . '</label>';
echo '<span class="elgg-text-help">' . elgg_echo("matchmaker:settings:friendship_relationship_names:help") . '</span>';
echo elgg_view('input/checkboxes', array(
	'default' => false,
	'name' => "params[$setting_name]",
	'value' => (isset($entity->$setting_name)) ? unserialize($entity->$setting_name) : array('friend'),
	'options' => $user_to_user_relationship_names,
));
echo '</div>';

// User to group relationship names
$query = "SELECT DISTINCT(r.relationship) 
		FROM {$dbprefix}entity_relationships r
		JOIN {$dbprefix}users_entity u ON u.guid = r.guid_one
		JOIN {$dbprefix}groups_entity g ON g.guid = r.guid_two";

$rows = get_data($query);
$user_to_group_relationship_names = array();

if ($rows) {
	foreach ($rows as $r) {
		$user_to_group_relationship_names[$r->relationship] = $r->relationship;
	}
}

$setting_name = UserMatchmaker::MEMBERSHIP_RELATIONSHIP_NAMES;
echo '<div>';
echo '<label>' . elgg_echo("matchmaker:settings:membership_relationship_names") . '</label>';
echo '<span class="elgg-text-help">' . elgg_echo("matchmaker:settings:membership_relationship_names:help") . '</span>';
echo elgg_view('input/checkboxes', array(
	'default' => false,
	'name' => "params[$setting_name]",
	'value' => (isset($entity->$setting_name)) ? unserialize($entity->$setting_name) : array('member'),
	'options' => $user_to_group_relationship_names,
));
echo '</div>';

$profile_fields = elgg_get_config('profile_fields');
if (is_array($profile_fields)) {
	foreach ($profile_fields as $name => $type) {
		$metadata_names[elgg_echo("profile:$name")] = $name;
	}

	$setting_name = UserMatchmaker::METADATA_NAMES;
	echo '<div>';
	echo '<label>' . elgg_echo("matchmaker:settings:metadata_names") . '</label>';
	echo '<span class="elgg-text-help">' . elgg_echo("matchmaker:settings:metadata_names:help") . '</span>';
	echo elgg_view('input/checkboxes', array(
		'default' => false,
		'name' => "params[$setting_name]",
		'value' => (isset($entity->$setting_name)) ? unserialize($entity->$setting_name) : array(),
		'options' => $metadata_names,
	));
	echo '</div>';
}

$weights = array(
	UserMatchmaker::DIRECT_RELATIONSHIPS,
	UserMatchmaker::INDIRECT_RELATIONSHIPS,
	UserMatchmaker::SECOND_DEGREE_RELATIONSHIPS,
	UserMatchmaker::SHARED_GROUP_RELATIONSHIPS,
	UserMatchmaker::SHARED_METADATA_RELATIONSHIPS,
);

foreach ($weights as $w) {
	echo '<fieldset class="elgg-fieldset">';
	echo '<legend>' . elgg_echo("matchmaker:settings:$w") . '</legend>';
	echo '<p>' . elgg_echo("matchmaker:settings:$w:help") . '</p>';
	echo '<div>';
	echo '<label>' . elgg_echo("matchmaker:settings:weight") . '</label>';
	echo '<p class="elgg-text-help">' . elgg_echo("matchmaker:settings:weight:help") . '</p>';
	echo elgg_view('input/text', array(
		'name' => "params[$w]",
		'value' => (isset($entity->$w)) ? $entity->$w : 1,
	));
	echo '</div>';
	echo '</fieldset>';
}


echo '<div>';
echo '<label>' . elgg_echo("matchmaker:settings:pagehandler") . '</label>';
echo '<span class="elgg-text-help">' . elgg_echo("matchmaker:settings:pagehandler:help") . '</span>';
echo elgg_view('input/text', array(
	'default' => false,
	'name' => "params[pagehandler]",
	'value' => (isset($entity->pagehandler)) ? $entity->pagehandler : MATCHMAKER_PAGEHANDLER,
));
echo '</div>';
