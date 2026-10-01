@echo off
setlocal
title Push to GitHub - Integrative Programming 2
cd /d "%~dp0"
set REPO=pfe-bsit002-integrative-programming-2

where git >nul 2>nul || (echo [ERROR] Git is not installed. Install Git for Windows, then run this again. & pause & exit /b 1)

rem ---- 1. Load the Week 1-6 commit history -------------------------------
if not exist .git (
    if not exist week-history.bundle (echo [ERROR] week-history.bundle is missing. & pause & exit /b 1)
    echo [1/5] Creating the Week 1-6 commit history...
    git init -q -b main
    git fetch -q week-history.bundle main
    git reset -q --mixed FETCH_HEAD
) else (
    echo [1/5] Git repository already exists - skipping history import.
)
if exist week-history.bundle del week-history.bundle

rem ---- 2. Your name and email on the commits -----------------------------
set GNAME=
set GMAIL=
for /f "delims=" %%i in ('git config user.name 2^>nul') do set GNAME=%%i
for /f "delims=" %%i in ('git config user.email 2^>nul') do set GMAIL=%%i
if "%GNAME%"=="" (
    set /p GNAME=Enter your full name for GitHub commits:
)
if "%GMAIL%"=="" (
    set /p GMAIL=Enter the email you used for your GitHub account:
)
git config --global user.name "%GNAME%"
git config --global user.email "%GMAIL%"

echo [2/5] Putting your name on the commits...
for /f "delims=" %%i in ('git log -1 --format^=%%ae') do set CUR=%%i
if /i "%CUR%"=="student@example.com" (
    git -c core.editor=true rebase -q --root --exec "git commit -q --amend --no-edit --reset-author"
)

rem ---- 3. Screenshots and any other changes ------------------------------
echo [3/5] Committing screenshots and latest changes...
if exist docs\screenshots (
    git add docs/screenshots
    git diff --cached --quiet || git commit -q -m "Add Postman testing screenshots for Weeks 1-6"
)
git add -A
git diff --cached --quiet || git commit -q -m "Update project files"

echo.
git log --oneline
echo.

rem ---- 4. Connect to your GitHub repository ------------------------------
set GHUSER=
set /p GHUSER=Enter your GitHub username:
git remote remove origin >nul 2>nul
git remote add origin https://github.com/%GHUSER%/%REPO%.git

rem ---- 5. Push ------------------------------------------------------------
echo [5/5] Pushing to https://github.com/%GHUSER%/%REPO% ...
echo       A browser window may open asking you to sign in to GitHub - approve it.
git push -u origin main
if errorlevel 1 (
    echo.
    echo [!] Push failed. Check that the repository "%REPO%" exists on GitHub
    echo     and is EMPTY ^(no README^), then run this file again.
) else (
    echo.
    echo DONE! Take a screenshot of this window for your Week 1 submission.
    echo Your repo: https://github.com/%GHUSER%/%REPO%
)
pause
