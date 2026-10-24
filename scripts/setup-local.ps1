<#
.SYNOPSIS
    Uzazi Clinic — local dev setup.
    Runs migrations + seeders, marks the app as installed (skips web installer),
    and optionally starts the dev server.

.EXAMPLE
    .\scripts\setup-local.ps1            # setup only
    .\scripts\setup-local.ps1 -Serve     # setup + php artisan serve
    .\scripts\setup-local.ps1 -Fresh     # wipe DB and re-seed demo data
#>
param(
    [switch]$Serve,
    [switch]$Fresh
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "==> Uzazi Clinic local setup" -ForegroundColor Cyan

# 1. Ensure .env exists
if (-not (Test-Path '.env')) {
    Copy-Item '.env.example' '.env'
    Write-Host "    .env created from .env.example" -ForegroundColor Yellow
}

# 2. Ensure APP_KEY
$key = (Select-String -Path '.env' -Pattern '^APP_KEY=(.+)$').Matches.Groups[1].Value
if ([string]::IsNullOrWhiteSpace($key)) {
    php artisan key:generate --force | Out-Null
    Write-Host "    APP_KEY generated" -ForegroundColor Yellow
}

# 3. Ensure sqlite file exists (default local DB)
if (-not (Test-Path 'database\database.sqlite')) {
    New-Item -ItemType File -Path 'database\database.sqlite' | Out-Null
    Write-Host "    database.sqlite created" -ForegroundColor Yellow
}

# 4. Migrate + seed
if ($Fresh) {
    Write-Host "==> migrate:fresh --seed (database wiped + demo data)" -ForegroundColor Cyan
    php artisan migrate:fresh --seed --force
} else {
    Write-Host "==> migrate --seed" -ForegroundColor Cyan
    php artisan migrate --seed --force
}
if ($LASTEXITCODE -ne 0) { throw "Migration/seed failed (exit $LASTEXITCODE)" }

# 5. Mark installed (skips web installer redirect)
@{ installed_at = (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'); version = '1.0.0'; method = 'cli' } |
    ConvertTo-Json -Compress | Set-Content -Path 'storage\app\installed' -NoNewline
Write-Host "    installed flag written (web installer bypassed)" -ForegroundColor Green

# 6. Build frontend if manifest missing
if (-not (Test-Path 'public\build\manifest.json')) {
    Write-Host "==> Building frontend assets" -ForegroundColor Cyan
    npm run build
}

Write-Host ""
Write-Host "DONE — run the app:" -ForegroundColor Green
Write-Host "   php artisan serve" -ForegroundColor White
Write-Host "   Login: admin@drissa.test / password" -ForegroundColor White

if ($Serve) {
    php artisan serve
}
