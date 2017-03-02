<?php

$english = array(

	'matchmaker:suggestions:refresh' => 'Refresh',
	'matchmaker:suggestions:users' => 'Suggested friends',
	
	'matchmaker:settings:connection_relationship_names' => 'Connections',
	'matchmaker:settings:connection_relationship_names:help' => '
		Specify which user to user relationships should be used in matching algorithms,
		e.g. for second degree connections
	',
	
	'matchmaker:settings:friendship_relationship_names' => 'Friendships',
	'matchmaker:settings:friendship_relationship_names:help' => '
		Specify which user to user relationships should be interpreted as a friendship
		or a pending frienship (e.g. friend, follower, friend request).
		Once such a relationship is established, users will be exclude users from
		matching algorithms.
	',
	
	'matchmaker:settings:membership_relationship_names' => 'Memberships',
	'matchmaker:settings:membership_relationship_names:help' => '
		Specify which user to group relationships should be interpreted as group
		membership and used in matching fellow group members
	',
	
	'matchmaker:settings:metadata_names' => 'Profile fields',
	'matchmaker:settings:metadata_names:help' => 'Specify which profile fields and tags should be used in matching algorithms',
	
	'matchmaker:settings:weight' => 'Weight',
	'matchmaker:settings:weight:help' => '
		Specify the weight matches of this type will have in the overall suggestion ranking.
		For instance, if each matched tag has more importance than a mutual friend,
		adjust the weights to reflect that (e.g. 1.5 and 0.75).
		To disable matching by this type, set the value to 0.
	',
	
	'matchmaker:settings:direct_relationships' => 'Direct relationships',
	'matchmaker:settings:direct_relationships:help' => '
		Looks for users that the current user has a relationship with (Connections),
		which have not been added as friends (Friendships).
		For example, User X, who is a colleague that hasn\'t been friended yet
	',

	'matchmaker:settings:indirect_relationships' => 'Indirect relationships',
	'matchmaker:settings:indirect_relationships:help' => '
		Looks for users that have a relationship with the current user, including friendships,
		but haven\'t been added back as friends yet. 
		For example, User X that has followed or friended the current user,
		but hasn\'t been added back as friend
	',

	'matchmaker:settings:second_degree' => 'Second degree connections',
	'matchmaker:settings:second_degree:help' => '
		Looks for users that have mutual connections with the current user. 
		For example, both current user and User X are following a collegue or friend.
	',

	'matchmaker:settings:shared_group' => 'Group membership',
	'matchmaker:settings:shared_group:help' => '
		Looks for users that are members of same groups as current user.
		For example, User X is also a member of Group A.
	',

	'matchmaker:settings:metadata' => 'Profile fields and tags',
	'matchmaker:settings:metadata:help' => 'Looks for users that have same tags. E.g. User X is also interested in Elgg',
	
	'matchmaker:stats:direct_relationships' => 'You are connected to %s as %s',
	'matchmaker:stats:indirect_relationships' => '%s connected to you as %s',
	'matchmaker:stats:shared_connections' => 'Connections you share with %s',
	'matchmaker:stats:shared_groups' => 'Groups you share with %s',
	'matchmaker:stats:shared_meta' => '%s also listed %s in their %s',
	'matchmaker:stats:introduction' => 'A friend suggestion was made by %s',
	
	'matchmaker:no_results' => 'There are no suggestions at this time, please check back later',
	
	'matchmaker:refresh:success' => 'Suggestions have been refreshed',
	'matchmaker:refresh:error' => 'Suggestions could not be refreshed',
	
	'matchmaker:suggestions:mute' => 'Remove from suggestions',
	'matchmaker:mute:success' => 'User was successfully removed from your suggestions',
	'matchmaker:mute:error' => 'User could not be removed from your suggestions',

	'matchmaker:details:show' => 'Show details',
	'matchmaker:details:hide' => 'Hide details',

	'widget:friend_suggestions' => 'Suggested Friends',
	'widget:friend_suggestions:description' => 'Displays a list of users, who you share common interests and connections with',
	'matchmaker:more' => 'More suggestions',

	'matchmaker:suggest' => 'Suggest friends',
	'matchmaker:suggest:title' => 'Suggest Friends to %s',
	'matchmaker:suggest:suggested_guids' => 'Select friends',
	'matchmaker:suggest:suggested_guids:help' => 'Select one or more of your friends you would like to suggest to %s',
	'matchmaker:suggest:introduction' => 'Introduction',
	'matchmaker:suggest:introduction:help' => 'Optionally, please add a note for your friend explaining why you are suggesting these users',
	'matchmaker:suggest:error:friends_only' => 'Suggestions can only be made to friends',
	'matchmaker:suggest:error:suggested_guids' => 'Please select one or more friends you would like to suggest',

	'matchmaker:suggest:count:error' => '%s of the users could not be suggested',
	'matchmaker:suggest:count:already' => '%s of the users are already connected, have a pending request, or have been suggested previously',
	'matchmaker:suggest:count:success' => '%s of the users have been suggested',

	'matchmaker:suggest:notify:intro_text' => '

	The following reason has been given for the suggestion:
	%s
	',

	'matchmaker:suggest:notify:subject' => 'You have a new friend suggestion',
	'matchmaker:suggest:notify:summary' => '%s made a suggestion to connect with %s',
	'matchmaker:suggest:notify:message' => '
		%1$s has suggested a connection with %2$s.

		%3$s

		You can view %2$s\'s profile here:
		%4$s

		You can view all of your friend suggestions here:
		%5$s
	',

);

add_translation('en', $english);
