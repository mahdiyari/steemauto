# Development
Project is under development (re-coding)
We will use Angular 6 in the new release, branch: `steemauto-angular`
  
# steemauto
steemauto.com - Curation Trail, Fanbase, Scheduled Posts, ... for Steemit.com Users.
check @steemauto on steemit.com for more information about this project.

## Development
Back-end developed by PHP and Javascript (nodejs).
Front-end developed by HTML, CSS, Bootstrap and Javascript.

## Installing
If you want to install and test on your private server, you will need apache2 (for running php codes)
and npm + nodejs (+ npm install pm2) for running /nodejs/ codes.  
Install mysql and import /mysql.sql file to your mysql database  
You should edit /inc/conf/db.php  
Put all files in your apache2 (or any PHP server) and edit /nodejs/config.js and start all /nodejs/.js files by pm2 (except config.js)  
Now, you should be able to explore steemauto in your local server  
You will need to change @steemauto to your account name in /dash.php to login and using steemauto  
also, you must edit /inc/dep/login_register.php and add your steemconnect app information  

## Contributions
Any contributions (suggestions and developments) are welcome!

## License
GNU General Public License v3.0
