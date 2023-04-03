<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<?php
		function Login_Verification(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
                               "user=natshort " .
						       "password='_EY53\$QV_te5&~3V'");
			$Query = "";
			$RHTML = "";
			$encrypted_password = "";
			$verified = "";
			
			/** If the user submits nothing for the username or password**/
			if(($_POST[username] == '')||($_POST[password] == '')){
					$RHTML .= "<p>Failed: You did not enter a username or a password.</p>";
					/** Redirects **/
					/**
						REDIRECT
					**/
			}		
						
			/**Create and execute the query to see if they entered the correct password **/
			$Query .= "Select password FROM projectusers WHERE username = '$_POST[username]'";
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
				/** Redirects **/
				/**
					REDIRECT
				**/
			}
			
			else{
				$Cursor = pg_fetch_all($Result);
				foreach($Cursor as $Row) {
					foreach($Row as $Column) {                
						$PasswordInDatabase = sprintf($Column);
					} 
				}
				
				$verified = password_verify("$_POST[password]",$PasswordInDatabase);
				/**$RHTML .= sprintf($verified);**/
				
				if($verified != 1){
					header("Location: https://cs.mvnu.edu/classes/csc3032/natshort/Senior_project/LoginPage.php");
				}
			}
			return $RHTML;
		}
		
		function DisplayProjects(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
                               "user=natshort " .
						       "password='_EY53\$QV_te5&~3V'");
			$Query = "SELECT project.project_id AS Project_ID, project.project_name AS Name, TO_CHAR(SUM(price), 'fm99G999D00') AS Project_Total FROM panel JOIN project ON project.project_id = panel.project_id WHERE username = '$_POST[username]' GROUP BY project.project_id, project.project_name ORDER BY project_id;";
			$RHTML = "";
			
			/**Execute the query**/
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
			}
			
			/**Create the table the projects will be displayed in**/
			else{
				$Cursor = pg_fetch_all($Result);
			
				/** Because the column headers are the same, I decided to hard-code them in**/
				$RHTML .= sprintf("<table style=\"text-align:center\">\n");  
				$RHTML .= sprintf("<tr>\n");
				$RHTML .= sprintf("<th style=\"width:200px\"><h3> Project Name </h3></th>\n");
				$RHTML .= sprintf("<th style=\"width:300px\"><h3> Total cost of project</h3></th></tr></table>\n");
				
				$RHTML .= sprintf("<ol>");
				foreach($Cursor as $Row) {
					$RHTML .= sprintf("<li><input type=\"radio\" name=\"userchoice\" value=\"" . $Row['project_id'] . "\"></input>\n");
					$RHTML .= sprintf("&emsp;" . $Row['name'] . "&emsp;");
					if ($Row['project_total'] == 0){
						$RHTML .= sprintf("&emsp;&emsp;&emsp;$0.00&emsp;");
					}
					else{
						$RHTML .= sprintf("&emsp;&emsp;&emsp;&emsp;$" . $Row['project_total'] . "&emsp;");
					}
					$RHTML .= sprintf("</li>");
				}
				$RHTML .= sprintf("<li><input type=\"radio\" name=\"userchoice\" value=\"0\">&emsp; Create New Project</input></li>");
				$RHTML .= sprintf("</ol>");
				
				return $RHTML;
			}
		}
		
		function GetInfo(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
                               "user=natshort " .
						       "password='_EY53\$QV_te5&~3V'");
			$Query = "SELECT username, fname, lname, email FROM projectusers WHERE username = '$_POST[username]';";
			$RHTML = "";
			
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
			}
			
			else{
				$Cursor = pg_fetch_all($Result);
				foreach($Cursor as $Row){
					$RHTML .= sprintf("<p><b>Username:</b> " . $Row[username] . "</p>");
					$RHTML .= sprintf("<p><b>Full name:</b> " . $Row[fname] . " " . $Row[lname] . "</p>");
					$RHTML .= sprintf("<p><b>Email:</b> " . $Row[email] . "</p>");
					
					/**This section makes it so the system hangs on to some of the user's info**/
					
					$RHTML .= sprintf("<div id=user_info> ");
					$RHTML .= sprintf("<input type=\"text\" id=\"username\" name=\"username\" value=\"" . $Row[username] . "\">");
					$RHTML .= sprintf("</div> ");
				}
			}
			return $RHTML;
		}
	
		?>
		
		<script>
			function KeepForLater(){
				document.getElementById('user_info').style.display = 'none';
			}
		</script>
	</head>
	
	<body onload="KeepForLater()">
		<form action="ProjectEditingPage.php" method="POST">
			<?php
				$SumVar = Login_Verification(username,password);
				echo $SumVar;
			?>
			<div class=row>
				<div class="column1">
					<div id=info_box> 
						<div id=inner_info_box>
							<?php
								$Info = GetInfo(username);
								echo $Info;
							?>
							
						</div>	
					</div>
				</div>
				
				<div class="column2">
					<div id=account_box>
						<div id=inner_account_box>
							<a href="LoginPage.php"> Log Out </a>
						</div>
					</div>
				</div>
			</div>
		
			<div id=separator_div>
			</div>
			<div id=page_main>
				<div id=page_main_cont style="max-width:550px">
					<div id=page_main_inner_cont>
						<h1>Your Projects</h1>
						<div id=display_table>
							<?php
								$Projs = DisplayProjects(username);
								echo $Projs;
							?>
						</div>
					</div>
				</div>		
			</div>
			
			<div class=row>
				<div class="column1">
				</div>
				
				<div class="column2">
					<div id=account_box>
						<div id=inner_account_box>
							<input type="submit" value="Edit selected project"></input>
						</div>
					</div>
				</div>
			</div>
		</form>
	</body>
</html>