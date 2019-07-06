NOTICE: this installation guide is dedicated only for running the app in a local server

Installation Steps: this step recommends you to use XAMPP
1. Go to XAMPP's htdocs folder commonly found at "C:\xampp\htdocs"
2. Create a folder with the name of your choosing e.g. "C:\xampp\htdocs\app"
3. Then inside that folder extract or move the files from the zip file

Database Configuration Steps:
1. Create your database and import "listing.sql" into it.
2. Configure config.json with the correct database and credentials

API Configuration Steps:
1. Get your listennotes api key from https://www.listennotes.com (you need to subscribe for a plan to get your api key)
2. Once you obtain your api key, add it into "app/config/config.json"
3. Get your google spreadsheets api credentials by following the steps found at https://docs.wso2.com/display/IntegrationCloud/Get+Credentials+for+Google+Spreadsheet
4. Once completed, you need to add your google client id and api key into "app/config/config.json"

Installation and configuration is now complete, you may now access the app using the folder name you created eg: "https://localhost/app"