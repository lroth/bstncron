<?php
/* BACKGROUND PROCESS FOR REALTIME Twitter - updates */
// Should be run on CLI:  php twitter-feed.php > twitter.lo

require 'ctwitter_stream.php';
require 'db.inc.php';

//user to track
//Change to: @Bostonscorp
$twitter_user = "@Bostonscorp";
// $twitter_user = "@mikkoalander";


//keywords (hashtags) to track
$vote_keywords = array(
		'#AmstelLight',
		'#BlueMoonBelgianWhite',
		'#BudLight',
		'#BudLightLime',
		'#Budweiser'	,
		'#CoorsLight',
		'#Corona',
		'#CoronaLight',
		'#DosEquis',
		'#Heineken',
		'#MichelobUltra',
		'#MillerLite',
		'#NewCastle',
		'#Odouls',
		'#SamAdams',
		'#SamAdamsSeasonal',
		'#StellaArtois'
);

//Instantiate, OAuth login and start tracking
$t = new ctwitter_stream( $db_host, $db_name, $db_user, $db_pass);
$t->login('NZsnOvUY12MTK2VDGK9TWA', 'kaOQ6URRDXrBigmbGhwvmxbj6QDd85vuD3rD4Ddo', '90738711-X5tOsDuqx0GRfRhzK7jgR3RzA6WsvBL5JReWGuM', 'PN75T3TNgfwkBM5cnF6OrfcC33ukhAR0AIcNqC5ndk');
$t->start( array( $twitter_user ), $vote_keywords);