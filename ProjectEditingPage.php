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
			$Query = "";
			$RHTML = "";
			$Choice = $_POST[userchoice];
			$Choice2 = 0;
			
			//First, create the new Project and Panel in the database
			if($_POST[userchoice]==0){
				$Choice = $_POST[userchoice] + 1;
				$Query = "SELECT MAX(project_id) AS max_id FROM project;";
				if(!($Result = pg_query($Link, $Query))){
					$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
				}
				else{
					$Cursor = pg_fetch_all($Result);
					foreach($Cursor as $Row) {
						$Choice = $Row[max_id] ;
					}
					$Choice2 = $Choice + 1;
					$Query = "INSERT INTO project VALUES (" . $Choice2 . ", '$_POST[username]', 'New_Project')";
					
					if(!($Result = pg_query($Link, $Query))){
						$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
					}
					
					else{
						$Query = "INSERT INTO panel VALUES (" . $Choice2 . ", 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
						
						if(!($Result = pg_query($Link, $Query))){
							$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
						}
						else{
							$Choice++;
						}
					}
				}
			}
			
			//Next, get the required information from the database to display user info
			$Query = "SELECT projectusers.email, project.username, projectusers.fname, projectusers.lname, project.project_name from project JOIN projectusers ON project.username = projectusers.username WHERE projectusers.username = '$_POST[username]' AND project.project_id = " . $Choice . ";";
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
					$RHTML .= sprintf("<p><b>Project Name:</b> <input type=\"text\" id=\"new_project_name\" name=\"new_project_name\" value = \"" . $Row[project_name] . "\"></p>");
					
					/**This section makes it so the system hangs on to the user's info for the calculation page**/
					$RHTML .= sprintf("<div id=user_info> ");
					$RHTML .= sprintf("<p>Username: <br><input type=\"text\" id=\"username\" name=\"username\" value=\"" . $Row[username] . "\"></p>");
					$RHTML .= sprintf("<p>Project ID: <br><input type=\"number\" id=\"userchoice\" name=\"userchoice\" value=\"$_POST[userchoice]\"></p>");
					
					$Query = sprintf("Select COUNT(project_id) as number_of_panels from panel WHERE project_id = $_POST[userchoice] ;");
					if(!($Result = pg_query($Link, $Query))){
						$RHTML .= sprintf("<p>Failed: " . pg_last_error($Link) . "</p>");
					}
					else{
						$Cursor = pg_fetch_all($Result);
						foreach($Cursor as $Row){						
							$RHTML .= sprintf("<p>Active panels:  <br><input type=\"number\" id=\"total_panels_in_project\" name=\"total_panels_in_project\" value=\"" . $Row[number_of_panels] . "\"></p>");
						}
					}
					$RHTML .= sprintf("<p>Total panels to count:  <br><input type=\"number\" id=\"total_panels_to_count\" name=\"total_panels_to_count\" value=\"" . $Row[number_of_panels] . "\"></p>");
					$RHTML .= sprintf("</div> ");
				}
			}
			
			//Add the HTML syntax between the User Info Box and the Panel Info Box
			
			$RHTML .= sprintf("</div></div></div>");
			$RHTML .= sprintf("<div class=\"column2\"><div id=account_box><div id=inner_account_box>");
			$RHTML .= sprintf("<a href=\"LoginPage.php\"> Log Out </a></div></div></div></div>");
			$RHTML .= sprintf("<div id=Extended_Page_Separator_div></div>");
			$RHTML .= sprintf("<div id=page_main><div id=page_main_cont style=\"width:950px\"><div id=page_main_inner_cont>");
			
			//Now we get the all the panel information
			
			$Query = "SELECT project.hours_of_sun AS hours_of_sun, project.project_name AS project_name, panel.project_id AS project_id, panel.panel_id AS panel_id, panel.count AS count, panel.model_name AS model_name, panel.company_name AS company_name, panel.price AS price, panel.watts AS watts, panel.volts AS volts, panel.length AS length, panel.width AS width, panel.units_of_measure AS units_of_measure FROM project JOIN panel ON project.project_id = panel.project_id WHERE project.project_id = " . $Choice . " ORDER BY panel.panel_id;";
			
			if(!($Result = pg_query($Link, $Query))){
				$RHTML .= "<p>Failed: " . pg_last_error($Link) . "</p>";
			}
			
			else{
				$Cursor = pg_fetch_all($Result);        
				
				$RHTML .= sprintf("<h1></h1>");
				$RHTML .= sprintf("<table style=\"text-align:center\">\n");  
				$RHTML .= sprintf("<tr>\n");
				
				/** Print header row **/
				$RHTML .= sprintf("<th width=\"85px\">Count</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Model Name</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Company Name</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Price</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Watts</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Volts</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Length</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Width</th>\n");
				$RHTML .= sprintf("<th width=\"85px\">Units of Measure</th>\n");
				$RHTML .= sprintf("</tr></table>\n");
				
				/** Print the query result as panel entries **/
				$panel_count = 1;
				foreach($Cursor as $Row) {
					if($panel_count == 1){
					$RHTML .= sprintf("<p>How many hours of sun will these panels get per day?   <input type=\"number\" name=\"hours_of_sun\" min=\"0\" step=\".01\" value=\"" . $Row[hours_of_sun] . "\"></input></p>\n");
					$RHTML .= sprintf("<div id=\"panel_list\">\n");
					}
					$RHTML .= sprintf("<p id=\"panel" . $Row[panel_id] . "\" class=\"panel_input\">\n");             
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_count\" min=\"0\" value=\"" . $Row[count] . "\" width=10px size=\"4\"></input>");
					$RHTML .= sprintf("<input type=\"text\" name=\"" . $Row[panel_id] . "_model\" value=\"" . $Row[model_name] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<input type=\"text\" name=\"" . $Row[panel_id] . "_company\" value=\"" . $Row[company_name] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_price\" min=\"0\" step=\".01\" value=\"" . $Row[price] . "\" size=\"7\"></input>");
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_watts\" min=\"0\" step=\".01\" value=\"" . $Row[watts] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_volts\" min=\"0\" step=\".01\" value=\"" . $Row[volts] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_length\" min=\"0\" step=\".01\" value=\"" . $Row[length] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<input type=\"number\" name=\"" . $Row[panel_id] . "_width\" min=\"0\" step=\".01\" value=\"" . $Row[width] . "\" size=\"10\"></input>");
					$RHTML .= sprintf("<select name=\"" . $Row[panel_id] . "_measure\" value=\"" . $Row[units_of_measure] . "\">");
						$RHTML .= sprintf("<option value=\"Meters\">Meters</option>");
						$RHTML .= sprintf("<option value=\"Yards\">Yards</option>");
						$RHTML .= sprintf("<option value=\"Feet\">Feet</option>");
					$RHTML .= sprintf("</select>");
					
					$RHTML .= sprintf("<button onclick=\"RemoveEntry(" . $panel_count . ")\">");
					$RHTML .= sprintf("<img src=\"trash_icon.png\" width=10px height=10px/>");
					$RHTML .= sprintf("</button>");
					 
					$RHTML .= sprintf("</p>");
					$panel_count++;
				}
				$RHTML .= sprintf("</div>");
				$RHTML .= sprintf("<button type=\"button\" onclick=\"AddToList(" . $panel_count . ")\">Click to add another panel entry</button>");
			}
			
			return $RHTML;
		}
		
		?>
		<script>
			var max_panels = 1;
			var Total_Panels = 0;
			var entries_deleted = 0;
			
			function KeepForLater(){
				document.getElementById('user_info').style.display = 'none';
			}
			
			function AddToList(count){
				Total_Panels = document.getElementById("panel_list").childElementCount;
				document.getElementById("total_panels_in_project").setAttribute("value", Total_Panels + 1);
				document.getElementById("total_panels_to_count").setAttribute("value", Total_Panels + entries_deleted + 1);
				var new_total = document.getElementById("total_panels_in_project").getAttribute('value');
				
				var newpanel = document.createElement("p");
				max_panels = Total_Panels + max_panels;
				newpanel.setAttribute("id", "panel" + max_panels);
				newpanel.setAttribute("class", "panel_input");
				
				var countNode = document.createElement("INPUT");
					countNode.setAttribute("type","number");
					countNode.setAttribute("name", max_panels + "_count");
					countNode.setAttribute("min","0");
					countNode.setAttribute("value","");
					countNode.setAttribute("width","10px");
					countNode.setAttribute("size","4");
				newpanel.appendChild(countNode);
				
				var modelNode = document.createElement("INPUT");
					modelNode.setAttribute("type","text");
					modelNode.setAttribute("name", max_panels + "_model");
					modelNode.setAttribute("value","");
					modelNode.setAttribute("size","10");
				newpanel.appendChild(modelNode);
				
				var compNode = document.createElement("INPUT");
					compNode.setAttribute("type","text");
					compNode.setAttribute("name", max_panels + "_company");
					compNode.setAttribute("value","");
					compNode.setAttribute("size","10");
				newpanel.appendChild(compNode);
				
				var priceNode = document.createElement("INPUT");
					priceNode.setAttribute("type","number");
					priceNode.setAttribute("name", max_panels + "_price");
					priceNode.setAttribute("min","0");
					priceNode.setAttribute("step",".01");
					priceNode.setAttribute("value","");
					priceNode.setAttribute("size","7");
				newpanel.appendChild(priceNode);
				
				var wattNode = document.createElement("INPUT");
					wattNode.setAttribute("type","nubmer");
					wattNode.setAttribute("name", max_panels + "_watts");
					wattNode.setAttribute("min","0");
					wattNode.setAttribute("step",".01");
					wattNode.setAttribute("value","");
					wattNode.setAttribute("size","10");
				newpanel.appendChild(wattNode);
				
				var voltNode = document.createElement("INPUT");
					voltNode.setAttribute("type","number");
					voltNode.setAttribute("name", max_panels + "_volts");
					voltNode.setAttribute("min","0");
					voltNode.setAttribute("step",".01");
					voltNode.setAttribute("value","");
					voltNode.setAttribute("size","10");
				newpanel.appendChild(voltNode);
				
				var lengthNode = document.createElement("INPUT");
					lengthNode.setAttribute("type","number");
					lengthNode.setAttribute("name", max_panels + "_length");
					lengthNode.setAttribute("min","0");
					lengthNode.setAttribute("step",".01");
					lengthNode.setAttribute("value","");
					lengthNode.setAttribute("size","10");
				newpanel.appendChild(lengthNode);
				
				var widthNode = document.createElement("INPUT");
					widthNode.setAttribute("type","text");
					widthNode.setAttribute("name", max_panels + "_width");
					widthNode.setAttribute("min","0");
					widthNode.setAttribute("step",".01");
					widthNode.setAttribute("value","");
					widthNode.setAttribute("size","10");
				newpanel.appendChild(widthNode);
				
				var measureNode = document.createElement("select");
					measureNode.setAttribute("name", max_panels + "_measure");
					option1 = document.createElement("option");
					option1.value = "Meters";
					option1.text = "Meters";
					measureNode.appendChild(option1);
					option2 = document.createElement("option");
					option2.value = "Yards";
					option2.text = "Yards";
					measureNode.appendChild(option2);
					option3 = document.createElement("option");
					option3.value = "Feet";
					option3.text = "Feet";
					measureNode.appendChild(option3);
				newpanel.appendChild(measureNode);
				
				var deleteNode = document.createElement("button");
					deleteNode.setAttribute("onclick","RemoveEntry(" + max_panels + ")");
					deleteNode.setAttribute("type","button");
					
				var imageNode = document.createElement("img");
					imageNode.setAttribute("src","trash_icon.png");
					imageNode.setAttribute("height","10px");
					imageNode.setAttribute("width","10px");
					
				deleteNode.appendChild(imageNode);
				newpanel.appendChild(deleteNode);
				
				var TextBox = document.getElementById("panel_list");
				TextBox.appendChild(newpanel);
			}
			
			function RemoveEntry(id){
				var item_to_delete = document.getElementById("panel" + id);
				item_to_delete.remove();
				Total_Panels = document.getElementById("panel_list").childElementCount;
				entries_deleted++;
				document.getElementById("total_panels_in_project").setAttribute("value", Total_Panels);
				document.getElementById("total_panels_to_count").setAttribute("value", Total_Panels + entries_deleted);
			}
		</script>
	</head>
	
	<body onload="KeepForLater()">
	<form action="CalculationPage.php" method="POST">
		<div class=row>
			<div class="column1">
				<div id=info_box> 
					<div id=inner_info_box>
						<?php
							$Info = Generate_Page();
							echo $Info;
						?>
					<p id="result"></p>
				</div>
			</div>		
		</div>
		
		<div class=row>
			<div class="column1"></div>
			
			<div class="column2">
				<div id=account_box>
					<div id=inner_account_box>
					<input type="submit" value="Calculate"></input>
					</div>
				</div>
			</div>
		</div>
		</form>
	</body>
</html>