<?php
$current_page = ( isset($_GET['page']) && $_GET['page'] ) ? $_GET['page'] : '1';
$categories = ( isset($info['categories']) && $info['categories'] ) ? $info['categories']:'';
$questions = ( isset($info['questions']) && $info['questions'] ) ? $info['questions']:'';
$siteRootURL = ( isset($site_url) ) ? $site_url : '';
?>
<?php if ($categories && $questions) { 
	$questionList = array();
	$i=1; foreach($questions as $id=>$q) {
		$questionList[$i] = array('ID'=>$id,'question'=>$q);
		$i++;
	}


	$count = count($questionList);
	$perpage = ceil($count/4);
	$lists = array_chunk($questionList,$perpage);


?>
	
<div class="test-wrapper">
	

	
	<div class="card questions-form">
		<div class="card-body">
			
		<div class="intro">
			<p>Directions: Write in a score from 1-4 for each of the following 98 questions.</p>
		</div>

			<form id="questionsForm" method="POST" action="<?=$siteRootURL?>inc/submit.php">
				<input type="hidden" name="testform" value="1">
				<?php if ( isset($_SESSION) ) { ?>
					<?php foreach ($_SESSION as $k=>$v) { ?>
						<?php if ($k!='memberlogin') { ?>
							<input type="hidden" name="<?=$k?>" value="<?=$v?>">			
						<?php } ?>		
					<?php } ?>	
				<?php } ?>
				
				<?php $pg=1; $ctr=1; foreach ($lists as $questions_items) { 
					$is_current = ($current_page==$pg) ? ' fadeIn active':'';
					$next_page = $pg + 1;
					$prev_page = $pg-1;
				?>
				<div id="page-<?= $pg ?>" class="questions-group animated<?= $is_current ?>">
					<ul class="questions-list">
						<?php foreach ($questions_items as $e) { 
							$q_id = $e['ID'];
							$q_quest = $e['question'];
							$fieldname = 'score_for_question_' . $q_id;
							?>
							<li class="question-item" data-index="<?= $ctr ?>" data-answered="">
								<div class="question"><span class="num"><i><?= $ctr ?></i></span> <?= $q_quest ?></div>
								<div class="choices">
									<?php for($score=0; $score<5; $score++) { 
										$field_id = 'row_'.$score.'_question_'.$q_id; ?>
										<label for="<?= $field_id ?>" class="radio-style">
											<input type="radio" name="<?= $fieldname ?>[]" class="choice-input" id="<?= $field_id ?>" value="<?= $score ?>">
											<i class="stats"></i>
											<i class="score"><?= $score ?></i>
										</label>
									<?php } ?>
								</div>
								<input type="hidden" name="question_id[]" value="<?= $q_id ?>">
							</li>	
						<?php $ctr++; } ?>
					</ul>	
					<div class="tab-button text-right">

						<?php if ($pg>1) { ?>
							<a href="#page-<?=$prev_page?>" class="btn-primary btn btn-prev buttonPrev" data-prev="#page-<?=$prev_page?>" data-next="#page-<?=$prev_page?>">&larr; Previous</a>
						<?php } ?>

						<?php if ($pg<4) { ?>
						<a href="#page-<?=$next_page?>" data-last="3" data-count="<?=$count?>" class="btn-primary btn btn-next buttonNext" data-prev="#page-<?=$prev_page?>" data-next="#page-<?=$next_page?>">Next &rarr;</a>
						<?php } ?>
						
						<?php if ($pg==4) { ?>
						<a href="#" id="submitFormBtn" class="btn-primary btn submitFormBtn">Submit</a>
						<?php } ?>

					</div>
				</div>
				<?php $pg++; } ?>
				
			</form>
		
		</div>
	</div>
</div>

<?php } ?>