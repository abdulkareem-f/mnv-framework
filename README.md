# MNV Framework


### Installation

1. Open in cmd or terminal app and navigate to project folder
2. Run following commands

    ```bash
    composer install
    cp .env.example .env
    ```

3. Set the database information in `.env`

   ```
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mnv
   DB_USERNAME=root
   DB_PASSWORD=
   ```
### Running

1. Run your server with `php 8.0.2`
2. Import the `mnv.sql` database file to your empty database (note that because I didn't create migrations)
3. open the project in your browser to the directory `public`

### Routing

I have prepared small example for articles, and you can find the links in the header and articles, but also you see all the routes here:

- Home (welcome): `{{baseUrl}}/` (GET)
- All articles: `{{baseUrl}}articles` (GET)
- Articles By date: `{{baseUrl}}articles/{year}/{month}/{day}` (GET)
- Create an article: `{{baseUrl}}articles/create` (GET) | `{{baseUrl}}articles` (POST)
- Update an article: `{{baseUrl}}articles/{id}/edit` (GET) | `{{baseUrl}}articles/{id}/update` (POST)
- View an article: `{{baseUrl}}articles/{{id}}` (GET)

### Testing
You need to edit the `phpunit.xml` file to set your database information too:

```
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="mnv"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value=""/>
```

You can test the whole project by running this command:

```bash
vendor/bin/phpunit tests  
```


## Note

I have used the `src/core/Database.php` class from external source