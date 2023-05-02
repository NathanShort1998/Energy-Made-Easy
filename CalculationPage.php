<!DOCTYPE html>
<!-- Nathan Short Senior Project: Energy Made Easy -->

<html>
	<head>	
		<link rel="stylesheet" href="EME_Stylesheet.css">
		<?php
		function Generate_Page(){							   
			$db = parse_ini_file("database_info.ini");
			$host = $db['hostname'];
			$database_name = $db['dbname'];
			$user_name = $db['username'];
			$dbpassword = $db['dbpassword'];
			$Link = pg_connect("host=" . $host . " dbname=" . $database_name . " user=" . $user_name . " password='" . $dbpassword . "'");
			
			//Update the name of the name and hours_of_sun of the project in the DB
			$Query = "UPDATE project SET project_name = '$_POST[new_project_name]', hours_of_sun = $_POST[hours_of_sun] where project_id = $_POST[userchoice];";
			$RHTML = "";
		
			
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
				$RHTML .= sprintf("<p>" . $Query . "</p>");
			}
			
			//Delete the old panel entries associated with the project so we can put in the new entries
			else{
				//Drop the panel entries so we can add the new entries
				$Query = "DELETE FROM panel WHERE project_id = $_POST[userchoice];";
				if(!($Result = pg_query($Link, $Query))){
					$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
				}
				
				//Get user's info, their project names, and total cost of the projects
				$Query = "SELECT projectusers.email, project.username, projectusers.fname, projectusers.lname, project.project_name from project JOIN projectusers ON project.username = projectusers.username WHERE projectusers.username = '$_POST[username]' AND project.project_id = $_POST[userchoice];";
				if(!($Result = pg_query($Link, $Query))){
					$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
				}
				
				//Display User info and generate the end of the info box and begining of the summary info
				else{
					//This for-each loop only runs once. Displays user's info
					$Cursor = pg_fetch_all($Result);
					foreach($Cursor as $Row){
						$RHTML .= sprintf("<p><b>Username: </b>" . $Row[username] . "</p>");
						$RHTML .= sprintf("<p><b>Full name: </b>" . $Row[fname] . " " . $Row[lname] . "</p>");
						$RHTML .= sprintf("<p><b>Email: </b>" . $Row[email] . "</p>");
						$RHTML .= sprintf("<p><b>Project Name: </b>" . $Row[project_name] . "</p>");
						
						/**This section makes it so the system hangs on to the user's info for the calculation page**/
						
						$RHTML .= sprintf("<div id=user_info> ");
						$RHTML .= sprintf("<input type=\"text\" id=\"username\" name=\"username\" value=\"" . $Row[username] . "\">");
						$RHTML .= sprintf("<input type=\"text\" id=\"userchoice\" name=\"userchoice\" value=\"'$_POST[userchoice]'\">");
						$RHTML .= sprintf("</div> ");
					}
				
					//This section of code ends the HTML for the info box and adds the print button and summary 
					$RHTML .= sprintf("</div></div></div>");
					$RHTML .= sprintf("<div class=\"column2\">");
					$RHTML .= sprintf("<div id=info_box style=\"float:right; width:200px; margin-right: 45px\">");
					$RHTML .= sprintf("<div id=inner_info_box><button onclick=\"window.print()\" style=\"height:50px; width:125px\"> Print This Page </button>");
					$RHTML .= sprintf("</div></div></div></div>");
					$RHTML .= sprintf("<div id=Extended_Page_Separator_div style=\"height:225px\"></div>");
					$RHTML .= sprintf("<div id=page_main style=\"width:1000px\"><div id=page_main_cont style=\"display:flex\"><div id=page_main_inner_cont>");
					
					//Finding the ID names of submitted panels
					$found_all = "false";
					$panels_found = 0;
					$counting_panel_ids = 1;
					$max_count = 0;
					//$number_of_unknown_panels = 0;
					for($for_loop_count = 1; $found_all != "true" ; $for_loop_count++){
		
						//This code submits the new panel entries to the database in place of the deleted ones
						$check_panel = $for_loop_count . "_count";
						
						//This only checks if the count field has been entered
						//I plan to add checks for names of the panel, watts, price, and anything else logical
						if(($_POST[$for_loop_count . "_count"] == " ")||($_POST[$for_loop_count . "_count"] == "")||($_POST[$for_loop_count . "_watts"] == " ")||($_POST[$for_loop_count . "_watts"] == "")||($_POST[$for_loop_count . "_price"] == " ")||($_POST[$for_loop_count . "_price"] == "")||($_POST[$for_loop_count . "_model"] == " ")||($_POST[$for_loop_count . "_model"] == "")){
							//$RHTML .= sprintf("<p>Entry number " . $counting_panel_ids . " lacked a <b>Count</b> value. Entering a value of '1' for count...</p>");
							$max_count++;
						}
						/**
						elseif(($_POST[$for_loop_count . "_watts"] == " ")||($_POST[$for_loop_count . "_watts"] == "")){
							//$RHTML .= sprintf("<p>Entry number " . $counting_panel_ids . " lacked a <b>Watt</b> value. Entering a value of '0' for watts...</p>");
							//$max_count++;
						}
						
						elseif(($_POST[$for_loop_count . "_price"] == " ")||($_POST[$for_loop_count . "_price"] == "")){
							//$RHTML .= sprintf("<p>Entry number " . $counting_panel_ids . " lacked a <b>Price</b> value. Entering $0.00 for the price...</p>");
							//$max_count++;
						}
						
						elseif(($_POST[$for_loop_count . "_model"] == " ")||($_POST[$for_loop_count . "_model"] == "")){
							//$number_of_unknown_panels++;
							//$RHTML .= sprintf("<p>Entry number " . $counting_panel_ids . " lacked a <b>Model name</b>. Entering 'Unknown-" . $number_of_unknown_panels . "' as the name</p>");
							//$max_count++;
						}**/
						
						//This else statement will need to expand into branching if-else statements to reflect other missing fields
						else{
							$panels_found++;
							$Query = "INSERT INTO panel VALUES(" . $_POST[userchoice] . ", " . $counting_panel_ids++ . "";
							
							//Add count to the query
							$count_string = $for_loop_count . "_count";
							if(($_POST[$count_string] == "")||($_POST[$count_string] == " ")){
								$Query .= ", 1";
							}
							else{
								$Query .= ", " . $_POST[$count_string] . "";
							}
							
							//Add company name to the query
							$company_string = $for_loop_count . "_company";
							if(($_POST[$company_string] == "")||($_POST[$company_string] == " ")){
								$Query .= ", Null";
							}
							else{
								$Query .= ", '" . $_POST[$company_string] . "'";
							}
							
							//Add model name to the query
							$model_string = $for_loop_count . "_model";
							if(($_POST[$model_string] == "")||($_POST[$model_string] == " ")){
								$Query .= ", 'Unknown-". $number_of_unknown_panels . "'";
							}
							else{
								$Query .= ", '" . $_POST[$model_string] . "'";
							}
							
							//Add watt to the query
							$watt_string = $for_loop_count . "_watts";
							if(($_POST[$watt_string] == "")||($_POST[$watt_string] == " ")){
								$Query .= ", 0";
							}
							else{
								$Query .= ", " . $_POST[$watt_string] . "";
							}
							
							//Add volt to the query
							$volt_string = $for_loop_count . "_volts";
							if(($_POST[$volt_string] == "")||($_POST[$volt_string] == " ")){
								$Query .= ", 0";
							}
							else{
								$Query .= ", " . $_POST[$volt_string] . "";
							}
							
							//Add length to the query
							$length_string = $for_loop_count . "_length";
							if(($_POST[$length_string] == "")||($_POST[$length_string] == " ")){
								$Query .= ", Null";
							}
							else{
								$Query .= ", " . $_POST[$length_string] . "";
							}
							
							//Add width to the query
							$width_string = $for_loop_count . "_width";
							if(($_POST[$width_string] == "")||($_POST[$width_string] == " ")){
								$Query .= ", Null";
							}
							else{
								$Query .= ", " . $_POST[$width_string] . "";
							}
							
							//Add measuring unit to the query
							$measure_string = $for_loop_count . "_measure";
							if(($_POST[$measure_string] == "")||($_POST[$measure_string] == " ")){
								$Query .= ", 'Meters'";
							}
							else{
								$Query .= ", '" . $_POST[$measure_string] . "'";
							}
							
							//Add price to the query
							$price_string = $for_loop_count . "_price";
							if(($_POST[$price_string] == "")||($_POST[$price_string] == " ")){
								$Query .= ", 0);";
							}
							else{
								$Query .= ", " . $_POST[$price_string] . ");";
							}
							//$RHTML .= sprintf("<p> " . $Query . "</p>");
							
							//Submitting each of the panels to the database
							if(!($Result = pg_query($Link, $Query))){
								$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
							}
							//max_count counts how many iterations of the loop the program is on
							$max_count++;						
							
							//This IF statement determins if we have found the right number of panels
							if(($panels_found == $_POST[total_panels_in_project])||($max_count == $_POST[total_panel_to_count])){
								$found_all = "true";
							}
							
							//Aids in diagnostics
							/**
							$RHTML .= sprintf("<p>Loop iteration:" . $for_loop_count . "</p>");
							$RHTML .= sprintf("<p>Found status:" . $found_all . "<p>");
							$RHTML .= sprintf("<p>Panels Found:" . $panels_found . "<p>");
							$RHTML .= sprintf("<p>Total Panls:" . $_POST[total_panels_in_project] . "<p>");
							$RHTML .= sprintf("<p>Max Count (Should be 4):" . $max_count . "<p>");
							$RHTML .= sprintf("<p>Total panel to count:" . $_POST[total_panel_to_count] . "<p>");
							**/
						}
					}
					
					//Aids in diagnostics
					//$RHTML .= sprintf("<p>End of loop " . $for_loop_count . " - found all: " . $found_all . "</p>");
					
					//Displaying all values sent by POST 
					/**foreach ($_POST as $key => $value){
						$RHTML .= sprintf("<p>" . $key . " - " . $value . "</p>");
					}**/
					
					
					//This code will display information from the panels of this project and summarize the information
					$Query = "select project.hours_of_sun, panel.model_name, panel.watts, panel.volts, panel.price, panel.count AS num_panels from project JOIN panel ON project.project_id = panel.project_id WHERE panel.project_id = $_POST[userchoice];";
					
					if(!($Result = pg_query($Link, $Query))){
						$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
					}
					
					else{
						$Cursor = pg_fetch_all($Result);
						$Total_Electricity_Made = 0;
						$Panel_Electricity_Total = 0;
						$Hours_of_sun =0;
						
						//Create a table to make the infromation more organized and structured
						$RHTML .= sprintf("<table style=\"text-align:center\"><tr>");
						$RHTML .= sprintf("<th style=\"width:150px\">Panel Model Name</th>");
						$RHTML .= sprintf("<th style=\"width:150px\">Panels Watts</th>");
						$RHTML .= sprintf("<th style=\"width:150px\">Panel Volts</th>");
						$RHTML .= sprintf("<th style=\"width:150px\">Price per Panel</th>");
						$RHTML .= sprintf("<th style=\"width:150px\">Number of Panels</th>");
						$RHTML .= sprintf("<th style=\"width:150px\">Electricity Made</th></tr>");
						
						
						//Hours of Sun * Watts * .75 = Panel_Total
						foreach($Cursor as $Row){
							$Panel_Electricity_Total = $Row[num_panels] * $Row[watts] * $Row[hours_of_sun] * .75;
							$Total_Electricity_Made = $Total_Electricity_Made + $Panel_Electricity_Total;
							$RHTML .= sprintf("<tr><td>" . $Row[model_name] . "</td>");
							$RHTML .= sprintf("<td>" . $Row[watts] . "</td>");
							$RHTML .= sprintf("<td>" . $Row[volts] . "</td>");
							$RHTML .= sprintf("<td>" . $Row[price] . "</td>");
							$RHTML .= sprintf("<td>" . $Row[num_panels] . "</td>");
							$RHTML .= sprintf("<td>" . $Panel_Electricity_Total . " Watts</td></tr>");
						}
						$RHTML .= sprintf("</table><br><p style=\"font-size:20px\">A total of <b>" . $Total_Electricity_Made . " Watts </b> or <b>" . $Total_Electricity_Made / 1000 . " Killowatts </b> is generated each day with this group of panels.</p>");
					}
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