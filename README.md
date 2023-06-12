# flatvote
flatvote experimental

url:  https://flatvote.zaidazadkiel.com/
repo: https://github.com/ZaidaZadkiel/flatvote

Using Docker settings in ./docker
to run:

```
make build
make run
```

you should have a docker with `http://localhost:8888/api` for the api, and `http://localhost:3000` for the react app

to set database credz edit `./.env `

it's convenient to set a local server like nginx to use the docker ports as a single host
location /     - flatvote homepage
location /api/ - api selfdocument page
location /ws   - react dev websocket

For hosting on Apache, edit the file `./misc/.htaccess` and set the correct environment variables, they are used only in the PHP api
MYSQL_DATABASE - full db name as seen by mysql
MYSQL_USER     - user created in mysql with SHOW TABLES permission
MYSQL_PASSWORD - the pass
upload to `yourhosting/api/.htaccess` and see if `yourhosting/api/index.php` shows any error

api/index.php should show "No tables found" when your DB has not updated schema or no read access, use the file `./misc/data_schema.sql` to add the current table schema 