@echo off
setlocal EnableExtensions DisableDelayedExpansion

rem Resolve paths relative to this script, not the caller's working directory.
set "PLANORA_APP_DIR=%~dp0..\..\Planora"
if not exist "%PLANORA_APP_DIR%\package.json" (
    echo ERROR: Missing package.json in "%PLANORA_APP_DIR%". Restore the project files. 1>&2
    goto :preflightFailure
)
if not exist "%PLANORA_APP_DIR%\package-lock.json" (
    echo ERROR: Missing package-lock.json in "%PLANORA_APP_DIR%". Restore the project files. 1>&2
    goto :preflightFailure
)

where.exe node >nul 2>&1
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: Node.js is missing from PATH. Install Node 24 LTS and reopen the terminal. 1>&2
    goto :preflightFailure
)
where.exe npm.cmd >nul 2>&1
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: npm.cmd is missing from PATH. Install Node 24 LTS and reopen the terminal. 1>&2
    goto :preflightFailure
)

call node -e "const v=process.versions.node.split('.').map(Number); if(v[0]<22 || (v[0]===22 && v[1]<13)) process.exit(1); console.log('Node '+process.versions.node);"
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: Node.js 22.13.0 or newer is required. Use Node 24 LTS. 1>&2
    goto :preflightFailure
)

if exist "%PLANORA_APP_DIR%\public\hot" (
    echo ERROR: Vite marker found at "%PLANORA_APP_DIR%\public\hot". 1>&2
    echo Stop npm run dev first. If no Vite server is running, remove this stale marker manually and try again. 1>&2
    goto :preflightFailure
)

pushd "%PLANORA_APP_DIR%"
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: Cannot enter the Laravel application directory. 1>&2
    goto :preflightFailure
)

echo Building frontend in "%CD%"...
rem Keep the lockfile and project npm policy; include the build tools.
call npm.cmd ci --include=dev --include=optional
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: npm ci failed. Fix the error above before retrying. 1>&2
    goto :failure
)

call npm.cmd run build
if not "%ERRORLEVEL%"=="0" (
    echo ERROR: npm run build failed. Fix the error above before retrying. 1>&2
    goto :failure
)

if not exist "public\build\manifest.json" (
    echo ERROR: Build completed without public\build\manifest.json. 1>&2
    goto :failure
)

echo Frontend ready: "%CD%\public\build\manifest.json"
popd
exit /b 0

:failure
popd
:preflightFailure
exit /b 1
