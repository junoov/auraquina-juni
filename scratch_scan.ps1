$targetPath = "C:\Users\Lenovo"
Write-Host "Scanning directories in $targetPath (including hidden)..." -ForegroundColor Cyan

$results = Get-ChildItem -Path $targetPath -Directory -Force -ErrorAction SilentlyContinue | ForEach-Object {
    $dirName = $_.Name
    $dirPath = $_.FullName
    
    # Calculate size
    $files = Get-ChildItem -Path $dirPath -Recurse -File -Force -ErrorAction SilentlyContinue
    $totalSize = 0
    foreach ($file in $files) {
        $totalSize += $file.Length
    }
    
    [PSCustomObject]@{
        FolderName = $dirName
        SizeGB     = [math]::round($totalSize / 1GB, 2)
        Path       = $dirPath
    }
}

$results | Where-Object { $_.SizeGB -gt 0.1 } | Sort-Object SizeGB -Descending | Format-Table -AutoSize

Write-Host "Scanning C:\ root folders..." -ForegroundColor Cyan
$rootResults = Get-ChildItem -Path "C:\" -Directory -Force -ErrorAction SilentlyContinue | ForEach-Object {
    $dirName = $_.Name
    $dirPath = $_.FullName
    
    # Calculate size
    $files = Get-ChildItem -Path $dirPath -Recurse -File -Force -ErrorAction SilentlyContinue
    $totalSize = 0
    foreach ($file in $files) {
        $totalSize += $file.Length
    }
    
    [PSCustomObject]@{
        FolderName = $dirName
        SizeGB     = [math]::round($totalSize / 1GB, 2)
        Path       = $dirPath
    }
}
$rootResults | Where-Object { $_.SizeGB -gt 0.1 } | Sort-Object SizeGB -Descending | Format-Table -AutoSize
