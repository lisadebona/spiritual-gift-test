<?php 
session_start();
require 'inc/functions.php'; 
$data = new SpiritualGifts();
// $info = $data->records();
// $form_data = ( isset($_POST) ) ? $_POST : '';
// $formResult = $data->submit_member($form_data);
// $is_logged_in = ( isset($_SESSION['spiritual_gift_test_started']) && $_SESSION['spiritual_gift_test_started'] ) ? true : false;
$config = $data->config();
$site_url = $config['site_url'];
$main_site = $config['main_site'];
// if( isset($_GET['logout']) &&  $_GET['logout'] ) {
// 	$data->sessionKill();
// }
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
</script>
</head>
<body class="thanks">
<div id="response" class="animated"></div>
<div class="site">

	<div class="site-content">

		<div id="thankYou" class="container">

			<div class="thank-message animated fadeIn">
				<div class="logo">
					<a href="<?=$main_site?>">
						<img src="images/logo.png" alt="Grace Bible Church">
					</a>
				</div>
				<div class="inner">
					<h1>Thank you!</h1>
					<p>Your Spiritual Gift test result will be emailed to you.</p>
					<div class="button">
						<!-- <a href="<?=$site_url?>" class="link">Return to Main Page</a>
						<span>or</span> -->
						<a href="<?=$main_site?>" class="link btn btn-sm btn-primary">Go to Grace Bible Church Homepage</a>
					</div>
				</div>
			</div>
			
		</div>
		
	</div>

</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
</html>