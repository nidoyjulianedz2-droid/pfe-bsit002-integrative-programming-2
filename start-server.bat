@echo off
title Integrative Programming 2 - Server
cd /d "%~dp0"
echo Make sure MySQL is started in XAMPP.
echo API running at http://127.0.0.1:8000/api  - test it in Postman. (Ctrl+C to stop)
call php artisan serve
pause
