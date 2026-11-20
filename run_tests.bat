@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo === Перевірка синтаксису PHP ===
for %%f in (*.php tests\*.php) do C:\xampp\php\php.exe -l %%f
echo.
echo === Модульні тести PHPUnit ===
C:\xampp\php\php.exe phpunit.phar
pause
