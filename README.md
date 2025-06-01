# Laravel 12 Task Management System

This is a Task Management web application built using **Laravel 12** and **PHP 8.2**.

---

## 🚀 Technologies Used

- Laravel 12
- PHP 8.2
- MySQL
- Composer
- NPM (Vite)
- Tailwind / Bootstrap (as per your setup)

---

## 🛠️ Installation Steps

Follow the steps below to set up this project on your local machine:

### 1. Clone the Repository

```bash
Step: 1
git clone https://github.com/mohittodquest/task_management.git
cd task_management

step: 2 
    composer install

step: 3 
    npm install
    npm run build

step: 4
    cp .env.example .env

step: 5
    php artisan key:generate
    php artisan migrate
    php artisan serve

Default Login credentials:

Email: mohit@gmail.com
Password: Mohit@123
