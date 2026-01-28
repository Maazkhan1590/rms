@echo off
echo Clearing Laravel caches...
cd /d D:\laragon\www\rms

REM Try to find PHP in Laragon
set PHP_PATH=D:\laragon\bin\php\php-8.2.4\php.exe
if not exist "%PHP_PATH%" set PHP_PATH=D:\laragon\bin\php\php-8.1.0\php.exe
if not exist "%PHP_PATH%" set PHP_PATH=D:\laragon\bin\php\php-8.0.0\php.exe
if not exist "%PHP_PATH%" set PHP_PATH=php

echo Using PHP: %PHP_PATH%

%PHP_PATH% artisan view:clear
%PHP_PATH% artisan config:clear
%PHP_PATH% artisan route:clear
%PHP_PATH% artisan cache:clear
%PHP_PATH% artisan optimize:clear

echo.
echo All caches cleared successfully!
echo Please refresh your browser (Ctrl+F5 for hard refresh)
pause
