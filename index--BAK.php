<?php 
session_start();
require 'inc/functions.php'; 
$data = new SpiritualGifts();
$info = $data->records();
$form_data = ( isset($_POST) ) ? $_POST : '';
$formResult = $data->submit_member($form_data);
$is_logged_in = ( isset($_SESSION['spiritual_gift_test_started']) && $_SESSION['spiritual_gift_test_started'] ) ? true : false;
$config = $data->config();
$site_url = $config['site_url'];
$main_site = $config['main_site'];
$body_class = 'home';
$is_completed = ( isset($_GET['completed']) &&  $_GET['completed'] ) ? true : false;
if($is_completed) {
	$body_class = 'thank-page';
	session_destroy();
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Spiritual Gift Test - Grace Bible Church</title>
<link rel="shortcut icon" href="images/favicon.png" type="image/png"/>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<script type="text/javascript">
var siteURL = '<?=$site_url?>';
var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});
if( typeof params.completed!='undefined' && params.completed==1) {
	//history.pushState('', document.title, siteURL);
}
</script>
</head>
<body class="<?=$body_class?>">

<?php if ($is_completed) { ?>
	
	<?php include('templates/response.php'); ?>

<?php } else { ?>

	<div id="response" class="animated"></div>
	<div class="site">

		<div class="site-header">
			<div class="container">
				<div class="flexwrap">
					<div class="logo">
						<a href="<?=$main_site?>">
							<img src="images/logo.png" alt="Grace Bible Church">
						</a>
					</div>
					<h2 class="site-title">Spiritual Gift Test</h2>
				</div>
			</div>
		</div>

		<div class="site-content">
			<div id="top"></div>

			<div class="container">
				<div id="main-content" class="main-content">

					<?php if ($is_logged_in) { ?>

						<?php include('templates/start_test.php'); ?>

					<?php } else { ?>

						<?php include('templates/member-form.php'); ?>

					<?php } ?>

				</div>
			</div>

		</div>

	</div>

<?php } ?>
<div id="loader">
	<div class="inner">
		<!-- <div class="lds-ring"><div></div><div></div><div></div><div></div></div> -->
		<div class="lds-hourglass"></div>
		<div class="loader-text"></div>
	</div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
</html>