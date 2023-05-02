CREATE TABLE projectusers (
	email 		varchar(60) not null, 
	username 		varchar(40) unique, 
	fname 		varchar(20), 
	lname 		varchar(20), 
	password 		varchar(60) not null, 
	primary 		key (email, username));

CREATE TABLE project (
	project_id 		numeric(4,0) unique, 
	username 		varchar(40) not null, 
	project_name	varchar(40),
	primary key 	(project_id, username),
	foreign key		(username) REFERENCES projectusers (username));

CREATE TABLE panel (
	project_id 		numeric(4,0) not null, 
	panel_id 		numeric(4,0) not null, 
	count 		numeric(4,0), 
	company_name	varchar(40), 
	model_name 		varchar(40), 
	watts 		numeric(8,2),  
	volts 		numeric(8,2),  
	length 		numeric(6,2),  
	width 		numeric(6,2),  
	units_of_measure	varchar(12),  
	price 		numeric(8,2),  
	primary key 	(project_id, panel_id),
	foreign key		(project_id) REFERENCES project (project_id));
