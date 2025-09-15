# Script para actualizar favicon a demon-slayer.ico en todos los archivos HTML
$rootPath = "C:\Users\Admin\Desktop\castillo-infinito"

# Buscar todos los archivos HTML recursivamente
$htmlFiles = Get-ChildItem -Path $rootPath -Filter "*.html" -Recurse

$updatedCount = 0
$totalFiles = $htmlFiles.Count

Write-Host "Iniciando actualizacion de favicon en $totalFiles archivos HTML..." -ForegroundColor Green

foreach ($file in $htmlFiles) {
    try {
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        $originalContent = $content
        
        # Determinar la ruta relativa correcta basada en la ubicacion del archivo
        $fileDir = $file.DirectoryName
        $relativeToRoot = $fileDir.Replace($rootPath, "").TrimStart("\")
        
        if ($relativeToRoot -eq "") {
            # Archivo en la raiz
            $faviconPath = "img/demon-slayer.ico"
        } else {
            # Archivo en subdirectorio - calcular ruta relativa
            $depth = ($relativeToRoot -split "\\").Length
            $faviconPath = ("../" * $depth) + "img/demon-slayer.ico"
        }
        
        # Actualizar favicon principal
        $content = $content -replace 'href="[^"]*\.ico"', "href=`"$faviconPath`""
        
        # Actualizar apple-touch-icon
        $content = $content -replace '<link rel="apple-touch-icon" href="[^"]*"', "<link rel=`"apple-touch-icon`" href=`"$faviconPath`""
        
        # Solo escribir si hubo cambios
        if ($content -ne $originalContent) {
            Set-Content -Path $file.FullName -Value $content -Encoding UTF8
            $updatedCount++
            Write-Host "Actualizado: $($file.Name) -> $faviconPath" -ForegroundColor Yellow
        }
    }
    catch {
        Write-Host "Error procesando $($file.Name): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "Actualizacion completada!" -ForegroundColor Green
Write-Host "Archivos actualizados: $updatedCount de $totalFiles" -ForegroundColor Cyan
