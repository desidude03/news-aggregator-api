
# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/10.x/installation)

Clone the repository

    git clone https://github.com/desidude03/news-aggregator-api.git

Switch to the repo folder

    cd news-aggregator-api

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Note: Update your Stripe public Private and Webhook key in .env file.

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database seeders

    php artisan db:seed

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

fetch and store articles from NewsAPI (**Set API key in .env as NEWSAPI_API_KEY before fetching or running cron**)

    php artisan news:newsApi

fetch and store articles from New York Times (**Set API key in .env as NYTIMES_API_KEY before fetching or running cron**)

    php artisan news:nyTimes

fetch and store articles from The Guardian (**Set API key in .env as GUARDIAN_API_KEY before fetching or running cron**)

    php artisan news:guardian

You can now access the API Docs at http://127.0.0.1:8000/docs

Running the Application on Docker

Start the Docker Containers: Use Sail to start the application

    ./vendor/bin/sail up -d

Run Database Migrations: Apply the database migrations

    ./vendor/bin/sail artisan migrate

Access the Application: Once the containers are running, the application will be accessible at

    http://localhost:8000

To stop the application, use:

    ./vendor/bin/sail down

Environment Variables Ensure the .env file is properly set up with the following database values

    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=<your_database_name>
    DB_USERNAME=sail
    DB_PASSWORD=password


Run Test cases

    php artisan test