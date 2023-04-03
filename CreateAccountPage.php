<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<script>
		</script>
	</head>
	
	<body>
	
		<div id=login_page_main>
			<div id=login_page_main_cont>	
				<div id=login_page_main_inner_cont>
					<!-- Form for creating a new account-->
					<form action="LoginPage.php" method="POST">
						<h1>Create Account Page</h1>
						<label for="fname">First name:</label><br>
						<input type="text" id="fname" name="fname"></input><br><br>
						
						<label for="lname">Last name:</label><br>
						<input type="text" id="lname" name="lname"></input><br><br>
						
						<label for="email">E-mail:</label><br>
						<input type="text" id="email" name="email"></label><br><br>
						
						<label for="username">Username:</label><br>
						<input type="text" id="username" name="username"></input><br><br>
						
						<label for="password">Password:</label><br>
						<input type="text" id="password" name="password"></input><br><br>
						
						<input type="submit" value="Create Account">
					</form> 
				</div>
			</div>		
		</div>	
	</body>
</html>