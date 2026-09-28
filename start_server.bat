@echo off
title Parkland Training CI Server
cd /d "%~dp0"
echo ==========================================
echo Starting Parkland Training Web Server...
echo URL: http://192.168.44.199:8080/
echo ==========================================
"C:\xampp\php\php.exe" spark serve --host 0.0.0.0 --port 8080
pause
