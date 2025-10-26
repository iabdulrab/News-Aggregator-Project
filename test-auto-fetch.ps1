# Auto-Fetch Feature Test Script
# This script demonstrates the auto-fetch feature

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "News Aggregator - Auto-Fetch Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000/api/articles"

# Test 1: Search for a unique term (should trigger auto-fetch)
Write-Host "Test 1: Searching for 'cryptocurrency' (first time)..." -ForegroundColor Yellow
Write-Host "This should trigger auto-fetch from news sources (may take 5-30 seconds)..." -ForegroundColor Gray
Write-Host ""

try {
    $response1 = Invoke-WebRequest -Uri "${baseUrl}?q=cryptocurrency&per_page=5" -UseBasicParsing -TimeoutSec 60
    $data1 = $response1.Content | ConvertFrom-Json
    $autoFetch1 = $response1.Headers['X-Auto-Fetch']
    
    Write-Host "✓ Response received!" -ForegroundColor Green
    Write-Host "  Articles found: $($data1.total)" -ForegroundColor White
    Write-Host "  Auto-fetch triggered: $($autoFetch1 -eq 'true')" -ForegroundColor White
    
    if ($autoFetch1 -eq 'true') {
        Write-Host "  Status: Fresh articles fetched from news sources! 🎉" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Test 1 failed: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "----------------------------------------" -ForegroundColor Gray
Write-Host ""

# Wait a moment
Start-Sleep -Seconds 2

# Test 2: Same search again (should use cached results)
Write-Host "Test 2: Searching for 'cryptocurrency' again..." -ForegroundColor Yellow
Write-Host "This should return cached results instantly..." -ForegroundColor Gray
Write-Host ""

try {
    $response2 = Invoke-WebRequest -Uri "${baseUrl}?q=cryptocurrency&per_page=5" -UseBasicParsing -TimeoutSec 10
    $data2 = $response2.Content | ConvertFrom-Json
    $autoFetch2 = $response2.Headers['X-Auto-Fetch']
    
    Write-Host "✓ Response received!" -ForegroundColor Green
    Write-Host "  Articles found: $($data2.total)" -ForegroundColor White
    Write-Host "  Auto-fetch triggered: $($autoFetch2 -eq 'true')" -ForegroundColor White
    Write-Host "  Status: Using cached results (fast!) ⚡" -ForegroundColor Green
    
    # Show first article
    if ($data2.data.Count -gt 0) {
        Write-Host ""
        Write-Host "Sample Article:" -ForegroundColor Cyan
        Write-Host "  Title: $($data2.data[0].title)" -ForegroundColor White
        Write-Host "  Source: $($data2.data[0].source.name)" -ForegroundColor White
        Write-Host "  Published: $($data2.data[0].published_at)" -ForegroundColor White
    }
} catch {
    Write-Host "✗ Test 2 failed: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "----------------------------------------" -ForegroundColor Gray
Write-Host ""

# Test 3: Search with source filter
Write-Host "Test 3: Searching 'technology' from Guardian only..." -ForegroundColor Yellow
Write-Host ""

try {
    $response3 = Invoke-WebRequest -Uri "${baseUrl}?q=technology&source=guardian&per_page=3" -UseBasicParsing -TimeoutSec 60
    $data3 = $response3.Content | ConvertFrom-Json
    $autoFetch3 = $response3.Headers['X-Auto-Fetch']
    
    Write-Host "✓ Response received!" -ForegroundColor Green
    Write-Host "  Articles found: $($data3.total)" -ForegroundColor White
    Write-Host "  Auto-fetch triggered: $($autoFetch3 -eq 'true')" -ForegroundColor White
    
    if ($autoFetch3 -eq 'true') {
        Write-Host "  Status: Fetched from Guardian only 📰" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Test 3 failed: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Testing Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "You can now:" -ForegroundColor Yellow
Write-Host "1. Check logs: Get-Content storage/logs/laravel.log -Tail 20" -ForegroundColor White
Write-Host "2. Test more searches at: http://localhost:8000/api/articles?q=YOUR_SEARCH" -ForegroundColor White
Write-Host "3. View all articles: http://localhost:8000/api/articles" -ForegroundColor White
Write-Host ""

