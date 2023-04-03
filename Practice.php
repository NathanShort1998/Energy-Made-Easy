<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<?php
		
		function testing(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
                               "user=natshort " .
						       "password='_EY53\$QV_te5&~3V'");
			$Query = "SELECT password FROM projectusers WHERE username = 'natshort'";
			$RHTML = "";
			$password = "";
			$encrypted ="";
			
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
			}
			else{
				$Cursor = pg_fetch_all($Result);
				foreach($Cursor as $Row) {
					$RHTML .= $Row[password];
				}
				$passwordguess = "helloworld";
				$encrypted = password_hash($password, PASSWORD_DEFAULT);
				$RHTML .= sprintf("<br>" . $encrypted . "<br>");
				$RHTML .= sprintf("<br>" . $passwordguess . "<br>");
				
				$Check = password_verify($passwordguess,$encrypted);
				$RHTML .= $Check;
				
				
			}
			return $RHTML;
		}
			
		?>
		<script>
		</script>
	</head>
	
	<body>
		<?php
			/**
			echo "test123";
			echo "<br>";
			$testpassword = "test123";
			$hash = password_hash($testpassword, PASSWORD_DEFAULT);
			echo $hash;
			
			$input = "test123";
			//echo "<br><br>Before";
			
			$result = password_verify($input,$hash);
			//echo "After<br>";
			
			$message = "Result: " . $result;

			if($result = 1){
				echo "<br>Success" . $message . " means correct";
			}
			else{
				echo "Failed";
			}
			**/
			$VarSum = testing();
			echo $VarSum;
		?>
					
	</body>
</html>