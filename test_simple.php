<?php
// Archivo de prueba simple para verificar PHP
echo "✅ PHP está funcionando correctamente!";
echo "<br>Versión PHP: " . phpversion();
echo "<br>Fecha: " . date('d/m/Y H:i:s');
echo "<br>Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible');
?> 