# Exs Backend API ⚙️

This is the backend API for the **Exs** financial management platform. It is built with **Laravel** and serves the mobile app (Flutter), web dashboard, and AI assistant by providing secure authentication, data storage, financial analytics, goal tracking, and intelligent recommendations.

## ✅ Features

- User registration and login
- Income and expense management
- Financial goals with time-based reminders
- Monthly financial analysis
- Notifications system
- Rule-based recommendations for companies
- API support for the AI assistant
- Supports Arabic and English

## 🛠 Requirements

- PHP >= 8.1
- Composer
- Laravel 10+
- MySQL or SQLite database

## 🚀 Installation & Setup

```bash
git clone https://github.com/exs-finance/exs-backend.git
cd exs-backend

composer install

cp .env.example .env
php artisan key:generate

# Set up your database connection in the .env file

php artisan migrate
php artisan serve
