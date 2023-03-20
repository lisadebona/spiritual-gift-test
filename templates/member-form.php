<div class="member-info-form">
	<div class="card">
		<div class="card-body">
			<form method="post" action="inc/submit.php" id="memberform">
					<div id="form-response"></div>
					<input type="hidden" name="memberlogin" value="1">
					<div class="form-group">
						<label for="fullname">Full Name:</label>
						<input type="text" name="fullname" class="form-control" />
					</div>
					<div class="form-group">
						<label for="phone">Mobile Number:</label>
						<input type="text" name="phone" id="phone" class="form-control" />
					</div>
					<div class="form-group">
						<label for="email">Email Address:</label>
						<input type="email" name="email" class="form-control" />
					</div>

					<div class="form-button">
						<input type="submit" class="btn btn-primary" id="startBtn" value="START">
					</div>
			</form>
		</div>
	</div>
</div>