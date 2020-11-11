# projects-demo

A sample demo project for an app listing work project and their tasks.

### 1. Requirements

Requires PHP >= 7.2.5 with `ext-ctype`, `ext-iconv` and `ext-iconv`.
If you're going to use the built-in Symfony web server, install the [Symfony CLI](https://symfony.com/download) as well.

### 2. Installation 
- Clone the repository
- Run `composer install`
- Tweak the .env values - note the SQL connection parameters and the `LOCAL_API_URL` parameter.
If you run the project with `symfony server:start`, the project url will be `http://localhost:8000` and the
`LOCAL_API_URL` should be `http://localhost:8000/api/`
- Run the doctrine migrations
- Load the fixtures to generate some sample data
- Run `symfony server:start`, or use a vhost on your favourite web server
- Open the project url. The first user credentials are `admin@example.com`/`Projects1!`
