#requires -Version 5.1

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

try {
    # Resolve paths relative to this script, not the caller's working directory.
    $applicationDirectory = [System.IO.Path]::GetFullPath(
        (Join-Path -Path $PSScriptRoot -ChildPath '..\..\Planora')
    )

    foreach ($requiredFile in @('package.json', 'package-lock.json')) {
        $requiredPath = Join-Path -Path $applicationDirectory -ChildPath $requiredFile
        if (-not (Test-Path -LiteralPath $requiredPath -PathType Leaf)) {
            throw "Missing $requiredPath. Restore the project files before running this script."
        }
    }

    $nodeCommand = Get-Command node -CommandType Application -ErrorAction SilentlyContinue | Select-Object -First 1
    $npmCommand = Get-Command npm.cmd -CommandType Application -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($null -eq $nodeCommand -or $null -eq $npmCommand) {
        throw 'Node.js and npm.cmd must be available in PATH. Install Node 24 LTS, then reopen the terminal.'
    }

    $nodeVersionOutput = & $nodeCommand.Source --version
    if ($LASTEXITCODE -ne 0 -or "$nodeVersionOutput" -notmatch '^v(\d+)\.(\d+)\.(\d+)$') {
        throw 'Unable to determine the Node.js version.'
    }
    $nodeVersion = [version]::new([int]$Matches[1], [int]$Matches[2], [int]$Matches[3])
    if ($nodeVersion -lt [version]'22.13.0') {
        throw "Node $nodeVersion is too old. Use Node 24 LTS (minimum supported: 22.13.0)."
    }

    $hotFile = Join-Path -Path $applicationDirectory -ChildPath 'public\hot'
    if (Test-Path -LiteralPath $hotFile) {
        throw "Vite marker found: $hotFile. Stop npm run dev first. If no Vite server is running, remove this stale marker manually and try again."
    }

    Push-Location -LiteralPath $applicationDirectory
    try {
        Write-Host "Building frontend in $applicationDirectory with Node $nodeVersion..."

        # Keep the lockfile and project npm policy; include the build tools.
        & $npmCommand.Source ci --include=dev --include=optional
        if ($LASTEXITCODE -ne 0) {
            throw "npm ci failed with exit code $LASTEXITCODE. Fix the error above before retrying."
        }

        & $npmCommand.Source run build
        if ($LASTEXITCODE -ne 0) {
            throw "npm run build failed with exit code $LASTEXITCODE. Fix the error above before retrying."
        }

        $manifestPath = Join-Path -Path $applicationDirectory -ChildPath 'public\build\manifest.json'
        if (-not (Test-Path -LiteralPath $manifestPath -PathType Leaf)) {
            throw "Build completed without the expected manifest: $manifestPath"
        }

        Write-Host "Frontend ready: $manifestPath"
    }
    finally {
        Pop-Location
    }
}
catch {
    [Console]::Error.WriteLine("Frontend build failed: $($_.Exception.Message)")
    exit 1
}

exit 0
