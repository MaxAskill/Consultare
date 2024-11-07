# Consultare

Consultare is a task management application built with Laravel and Vue.js.

## Features

- Create, read, update, and delete tasks
- Prioritize tasks by adding new ones to the top of the list
- User authentication and authorization
- Responsive design using Quasar framework

## Requirements

- PHP 8.1+
- Composer
- Node.js and npm
- MySQL or another compatible database

## Installation

1. Clone the repository:
    git clone https://github.com/MaxAskill/Consultare.git
    cd consultare

2. Install PHP dependencies:
    composer install    

3. Install JavaScript dependencies:
    npm install 

4. Copy the .env.example file to .env and configure your environment variables:
    cp .env.example .env

5. Generate an application key:
    php artisan key:generate

6. Set up your database in the .env file and run migrations:
    php artisan migrate

7. Compile assets:
    npm run build

8. Start the development server:
    composer run dev


## Usage

Visit `http://localhost:8000` in your web browser to access the application.



