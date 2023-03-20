<header class="site-header">
	<div class="container">
		<div class="flexwrap">
			<div class="logo">
				<a href="<?=$main_site?>">
					<img src="images/logo.png" alt="Grace Bible Church">
				</a>
			</div>
			<h2 class="site-title">Spiritual Gift Test</h2>
		</div>
		<div id="userInfo">
			<?php 
			$userFullname = ( isset($_SESSION['fullname']) && $_SESSION['fullname'] ) ? $_SESSION['fullname'] : '';
			$userEmail = ( isset($_SESSION['email']) && $_SESSION['email'] ) ? $_SESSION['email'] : '';
			if ($userFullname) { ?>
			<div class="user fullname">Fullname: <?= $userFullname ?></div>
			<?php } ?>
			<?php if ($userEmail) { ?>
			<div class="user email">Email: <?= $userEmail ?></div>
			<?php } ?>

			<div class="logoutdiv"><a href="<?php echo $site_url ?>?out=1">Log Out</a></div>
		</div>
	</div>
</header>