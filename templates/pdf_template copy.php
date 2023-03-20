<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background-color:#FFF;width:100%;margin:0 auto;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:13px;">
	<tr>
		<td style="padding:20px 20px">

			<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;width:100%;margin-bottom:10px">
				<tr>
					<td width="70px">
						<a href="https://www.idahograce.com/">
							<img src="../logo.jpg" alt="Grace Bible Church" style="width:70px;height:auto;">
						</a>
					</td>
					<td style="padding-left:15px">
						<h2 style="color:#faad6d;margin:0 0;font-size:22px;">Spiritual Gift Test Result</h2>
						<p style="margin:2px 0 0;font-size:14px;position:relative;top:-5px;">Grace Bible Church</p>
					</td>
				</tr>
			</table>

			<hr>

			<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;width:100%;margin:10px 0 0">
				<tr>
					<td style="width:15%;padding:2px">Name:</td>
					<td><?=$member_name?></td>
				</tr>
				<tr>
					<td style="width:15%;padding:2px">Phone:</td>
					<td><?=$member_phone?></td>
				</tr>
				<tr>
					<td style="width:15%;padding:2px">Email:</td>
					<td><?=$member_email?></td>
				</tr>
				<tr>
					<td style="width:15%;padding:2px">Test Started:</td>
					<td><?=date('m/d/Y h:i:sa',strtotime($test_started))?></td>
				</tr>
			</table>

			<h3 style="font-size:18px;margin:20px 0 10px">Result:</h3>
			<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;width:100%;">
				<tr>
					<td>
						<?php 
						$categories = ( isset($info['categories']) && $info['categories'] ) ? $info['categories']:'';
						
						if ($test_result) { ?>
						<table cellpadding="0" cellspacing="0" style="border-collapse: collapse;width:100%;">
							<?php foreach ($test_result as $k=>$total_score) { 
								$perfect_score = (isset($categories[$k]['questions']) && $categories[$k]['questions']) ? count($categories[$k]['questions']) * $highest:0;
								$skill = (isset($categories[$k]['name']) && $categories[$k]['name']) ? $categories[$k]['name']:'';

								$answer_percentage = ($total_score/$perfect_score) * 100;
								if(fmod($answer_percentage, 1) !== 0.00){
									$answer_percentage =  number_format((float)$answer_percentage, 2, '.', '');
								}
								if($answer_percentage<1) {
									$answer_percentage = 0;
								}
								?>
								<tr>
									<td style="border-bottom:1px solid #CCC;padding:8px 5px;width:30%;"><?=$skill?></td>
									<td style="border-bottom:1px solid #CCC;padding:0 5px;width:40%;position:relative;">
										<?php if ($answer_percentage>0) { ?>
											<div style="width:<?=$answer_percentage?>%;height:20px;background-color:#faae6f;"></div>
										<?php } else { ?>
											&nbsp;
										<?php } ?>
									</td>
									<td style="border-bottom:1px solid #CCC;padding:8px 5px;text-align:center;width:20%;"><?=$total_score?></td>
								</tr>
							<?php } ?>
						</table>
						<?php } ?>
					</td>
				</tr>
			</table>


			<?php if ( isset($top_three_result) && $top_three_result ) { ?>
				<h3 style="font-size:18px;margin:30px 0 10px">Definition of your top 3 gifts:</h3>
				<ul>
					<?php foreach ($top_three_result as $cat=>$arg) { ?>
					<li style="margin-bottom:20px;">
						<h3 style="margin:0 0;font-size:15px;font-weight:bold;display:block;"><?=$arg['name']?></h3>	
						<?=$arg['definition']?>
						<?php } ?>
					</li>
				</ul>
			<?php } ?>

			<h3 style="margin:30px 0 5px;font-size:15px;font-weight:bold;"><?=$config['bottomtext']['title']?></h3>
			<p style="margin:0 0"><?=$config['bottomtext']['content']?></p>

		</td>
	</tr>
</table>