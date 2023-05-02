#/bin/bash
#Remove existing version of EME database if present
myuserID=$(whoami)

# Create database and run file with DML and import commands
createdb energy_made_easy
psql -d energy_made_easy -f eme_create_tables.sql
