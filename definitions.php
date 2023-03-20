<?php 
require 'inc/functions.php'; 
$data = new SpiritualGifts();
$info = $data->config();

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
</script>
</head>
<body class="definitions-page">
	<div class="site">
		<?php include("templates/header.php") ?>
		<?php 
			$categories = ( isset($info['categories']) && $info['categories'] ) ? $info['categories'] : '';
			if($categories) { ?>
			<div class="content-wrapper">

				<div class="container">
					<div class="page-header">
						<h1 class="page-title">Definitions</h1>
						<a href="data/gifts/all.pdf" target="_blank" class="btn btn-primary" download><i class="fa fa-download"></i> Download All Definitions</a>
					</div>
					

					<div class="list-wrapper">
					  <ul class="definition-list">
					  	<?php foreach ($categories as $c) { 
					  		$name = $c['name'];
					  		$definition = $c['definition'];
					  		$pdf_url = $c['details'];
					  	?>
					  		<li>
					  			<div class="inside">
					  				<h3><?=$name?></h3>
					  				<p><?=$definition?></p>
					  				<div class="button">
					  					<a href="<?=$pdf_url?>" target="_blank" class="btn2">View Details</a>
					  				</div>
					  			</div>
					  		</li>
					  	<?php } ?>
					  </ul>
				  </div>
				</div>
			</div>
		<?php } ?>
	</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>
</html>