🚀 BrainMarkt API – Laravel Backend Setup Guide

This is the backend API for the BrainMarkt SaaS platform. Built with Laravel and PostgreSQL, it powers product feed ingestion, transformation, segmentation, and reporting.

📦 Tech Stack

Laravel 10.x (PHP 8.4+)

PostgreSQL 12+

Composer + NPM

Repo: https://github.com/vonteqdev/brainmarkt

🔧 Local Setup (macOS)

Install dependencies:

brew install php composer postgresql node git
brew services start php
brew services start postgresql

Clone and set up the project:

git clone https://github.com/vonteqdev/brainmarkt.git
cd brainmarkt

composer install
npm install

cp .env.example .env
php artisan key:generate

Update .env:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=brainmarkt_db
DB_USERNAME=brainmarkt_user
DB_PASSWORD=secret

Create the database and user:

psql postgres

Inside psql:

CREATE USER brainmarkt_user WITH PASSWORD 'secret';
CREATE DATABASE brainmarkt_db OWNER brainmarkt_user;
GRANT ALL PRIVILEGES ON DATABASE brainmarkt_db TO brainmarkt_user;
\q

Run migrations:

php artisan migrate
php artisan db:seed # optional

Serve the app:

php artisan serve

Visit: http://localhost:8000

📂 Push to GitHub (Using Personal Access Token)

git init
git add .
git commit -m "Initial Laravel setup"
git remote add origin https://github.com/vonteqdev/brainmarkt.git
git branch -M main
git push -u origin main

💡 When prompted for username/password:

Username: your GitHub username

Password: your GitHub token

🔐 Optional

API Auth (Laravel Breeze + Sanctum):

composer require laravel/breeze --dev
php artisan breeze:install api
php artisan migrate

Docker (Laravel Sail):

composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up

✅ Done

You’re ready to build and deploy the BrainMarkt API 🚀
