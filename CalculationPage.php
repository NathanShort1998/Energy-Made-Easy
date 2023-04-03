<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<?php
		
		function Generate_Page(){
			$Link = pg_connect("host=localhost " . 
							   "dbname=energy_made_easy " . 
                               "user=natshort " .
						       "password='_EY53\$QV_te5&~3V'");
			
			//Update the name of the name and hours_of_sun of the project in the DB
			$Query = "UPDATE project SET project_name = '$_POST[new_project_name]', hours_of_sun = $_POST[hours_of_sun] where project_id = $_POST[userchoice];";
			$RHTML = "";
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
			}
			
			else{
				$Query = "DELETE FROM panel WHERE project_id = $_POST[userchoice];";
				if(!($Result = pg_query($Link, $Query))){
					$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
				}
				else{
					
				}
				
				$Query = "SELECT projectusers.email, project.username, projectusers.fname, projectusers.lname, project.project_name from project JOIN projectusers ON project.username = projectusers.username WHERE projectusers.username = '$_POST[username]' AND project.project_id = $_POST[userchoice];";
				if(!($Result = pg_query($Link, $Query))){
					$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
				}
				
				//Display User info and generate part of the page
				else{
					$Cursor = pg_fetch_all($Result);
					foreach($Cursor as $Row){
						$RHTML .= sprintf("<h5>Username: " . $Row[username] . "<br>");
						$RHTML .= sprintf("Full name: " . $Row[fname] . " " . $Row[lname] . "<br>");
						$RHTML .= sprintf("Email: " . $Row[email] . "<br>");
						$RHTML .= sprintf("Project Name: " . $Row[project_name] . "</h5>");
						
						/**This section makes it so the system hangs on to the user's info for the calculation page**/
						
						$RHTML .= sprintf("<div id=user_info> ");
						$RHTML .= sprintf("<input type=\"text\" id=\"username\" name=\"username\" value=\"" . $Row[username] . "\">");
						$RHTML .= sprintf("<input type=\"text\" id=\"userchoice\" name=\"userchoice\" value=\"'$_POST[userchoice]'\">");
						$RHTML .= sprintf("</div> ");
					}
				
					$RHTML .= sprintf("</div></div></div>");
					$RHTML .= sprintf("<div class=\"column2\">");
					$RHTML .= sprintf("<div id=info_box style=\"float:right; width:200px; margin-right: 45px\">");
					$RHTML .= sprintf("<div id=inner_info_box><button onclick=\"window.print()\" style=\"height:50px; width:125px\"> Print This Page </button>");
					$RHTML .= sprintf("</div></div></div></div>");
					$RHTML .= sprintf("<div id=Extended_Page_Separator_div style=\"height:225px\"></div>");
					$RHTML .= sprintf("<div id=page_main><div id=page_main_cont><div id=page_main_inner_cont>");
					
					//Finding the ID names of submitted panels
					$found_all = false;
					$panels_found = 0;
					$counting_panel_ids = 1;
					$max_count = 0;
					for($for_loop_count = 1; $found_all != true ; $for_loop_count++){
						
						$check_panel = $for_loop_count . "_count";
						if(($_POST[$for_loop_count . "_count"] != " ")||($_POST[$for_loop_count . "_count"] != "")){
							$panels_found++;
							$count_string = $for_loop_count . "_count";
							$Query = "INSERT INTO panel VALUES(" . $_POST[userchoice] . ", " . $counting_panel_ids++ . ", " . $_POST[$count_string]. "";
											
							$company_string = $for_loop_count . "_company";
							$Query .= ", '" . $_POST[$company_string] . "'";
							
							$model_string = $for_loop_count . "_model";
							$Query .= ", '" . $_POST[$model_string] . "'";
							
							$watt_string = $for_loop_count . "_watts";
							$Query .= ", " . $_POST[$watt_string] . "";
							
							$volt_string = $for_loop_count . "_volts";
							$Query .= ", " . $_POST[$volt_string] . "";
							
							$length_string = $for_loop_count . "_length";
							$Query .= ", " . $_POST[$length_string] . "";
							
							$width_string = $for_loop_count . "_width";
							$Query .= ", " . $_POST[$width_string] . "";
							
							$measure_string = $for_loop_count . "_measure";
							$Query .= ", '" . $_POST[$measure_string] . "'";
							
							$price_string = $for_loop_count . "_price";
							$Query .= ", " . $_POST[$price_string] . ");";
							$RHTML .= "<p> " . $Query . "</p>";
							
							//Submit each query here:
							if(!($Result = pg_query($Link, $Query))){
								$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
							}
							$max_count++;
						}
						else{
							$RHTML .= sprintf("<p>Entry lacked a count value</p>");
							$max_count++;
						}
						
						if(($panels_found == $_POST[total_panels_in_project])||($max_count == $_POST[total_panel_to_count])){
							$found_all = true;
						}
						
						//Aids in diagnostics
						//$RHTML .= sprintf("<p>Loop iteration:" . $for_loop_count . "</p>");
					}
				
					//Displaying all values sent by POST 
					/*foreach ($_POST as $key => $value){
						$RHTML .= sprintf("<p>" . $key . " - " . $value . "</p>");
					}*/
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
		<div class=row>
			<div class="column1">
				<div id=info_box> 
					<div id=inner_info_box>
						<?php
							$Page_Info = Generate_Page(username,userchoice);
							echo $Page_Info;
					?>
				</div>
			</div>		
		</div>	
	</body>
</html>