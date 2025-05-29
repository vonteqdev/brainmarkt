🚀 BrainMarkt API – Laravel Backend Setup Guide

This is the backend API for the BrainMarkt SaaS platform. Built with Laravel and PostgreSQL, it powers product feed ingestion, transformation, segmentation, and reporting.

📦 Tech Stack

Laravel 10.x (PHP 8.4+)

PostgreSQL 12+

Composer + NPM

Repo: https://github.com/vonteqdev/brainmarkt

🔧 Local Setup (macOS)

1. Prerequisites

brew install php composer postgresql node git
brew services start php
brew services start postgresql

2. Clone and Setup Project

git clone https://github.com/vonteqdev/brainmarkt.git
cd brainmarkt

composer install
npm install

cp .env.example .env
php artisan key:generate

3. Configure Database (PostgreSQL Recommended)

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=brainmarkt_db
DB_USERNAME=brainmarkt_user
DB_PASSWORD=secret

Then setup DB via terminal:

psql postgres

Inside psql:

CREATE USER brainmarkt_user WITH PASSWORD 'secret';
CREATE DATABASE brainmarkt_db OWNER brainmarkt_user;
GRANT ALL PRIVILEGES ON DATABASE brainmarkt_db TO brainmarkt_user;
\q

4. Migrations & Seeding

php artisan migrate
php artisan db:seed # optional

5. Serve the Application

php artisan serve

App will run on: http://localhost:8000

🚀 GitHub Push Instructions (With Token Auth)

git init
git add .
git commit -m "Initial Laravel setup"
git remote add origin https://github.com/vonteqdev/brainmarkt.git
git branch -M main
git push -u origin main

🧠 Use a GitHub Personal Access Token as password during push.

🔐 Optional Features

Laravel Breeze (API Auth):

composer require laravel/breeze --dev
php artisan breeze:install api
php artisan migrate

Laravel Sail (Docker):

composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up

Use .env with:

DB_HOST=pgsql

✅ Final Notes

Test API with Postman or Insomnia.

Nuxt frontend runs on http://localhost:3000 — configure CORS in Laravel if needed.

Keep .env and database credentials safe.

Commit early, commit often.

You're ready to build and launch BrainMarkt API! 🚀

