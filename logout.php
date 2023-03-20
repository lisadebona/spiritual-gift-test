<?php
session_start();
include("inc/config.php");
$site_url = $info['site_url'];
$logout = ( isset($_GET['out']) && $_GET['out']==1 ) ? true : false;
if($logout) {
	if( session_destroy() ) {
		header( "Location: " . $site_url );
	}
}