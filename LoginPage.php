<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<?php
		function Create_Account(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
							   "user=natshort " .
							   "password='_EY53\$QV_te5&~3V'");
			$Query = "";
			$RHTML = "";	
			
			$hashed_password = password_hash("$_POST[password]", PASSWORD_DEFAULT);
		
			$Query .= "INSERT INTO projectusers VALUES( '$_POST[email]', '$_POST[username]', '$_POST[fname]', '$_POST[lname]', '" . $hashed_password . "');";
			
			if(!($Result = pg_query($Link, $Query))){
				if(($_POST[username] == '')&&($_POST[password] == '')){
					$RHTML .= "<p>Please enter your username and password.</p>";
				}
				elseif(($_POST[username] == '')||($_POST[password] == '')){
					$RHTML .= "<p>Error: You did not enter a correct username or password.</p>";
				}
				else{
					$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
				}
			}
			
			return $RHTML;
		}
		?>
		<script>
		</script>
	</head>
	
	<body>
	<?php
		$SumVar = Create_Account(email,username,fname,lname,password);
		echo $SumVar;
	?>
	
		<div id=login_page_main>		
			<div id=login_page_main_cont>		
				<div id=login_page_main_inner_cont>					
					<!-- Form for logging into one's account-->
					<form action="AccountPage.php" method="POST">
						<h1> Login Page</h1>						
						<label for="username">Username:</label><br>
						<input type="text" id="username" name="username"></input><br><br>
						
						<label for="password">Password:</label><br>
						<input type="password" id="password" name="password"></input><br><br>
						
						<input type="submit" value="Login">
					</form> 
					<a href="CreateAccountPage.php">Create an account</a>
				</div>
			</div>		
		</div>
	</body>
</html>