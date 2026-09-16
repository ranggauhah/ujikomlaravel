@echo off
echo ========================================
echo Starting API Server...
echo ========================================
cd /d "\\wsl.localhost\Ubuntu\home\lenovo\API-UJIKOM\api"
wsl bash -c "cd /home/lenovo/API-UJIKOM/api && npm start"
pause
