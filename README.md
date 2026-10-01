# Integrative Programming 2 – Module 1
**PFEBSIT 002 · IT Free Elective 2**

Laravel 12 project for the Module 1 laboratory activities.

## Week 1 – Development Environment
Tools: PHP, Composer, Laravel, MySQL (XAMPP), VS Code, Git, Postman.
Database: `integrative_db` (see `.env.example`).

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Test in Postman: `GET http://127.0.0.1:8000`
