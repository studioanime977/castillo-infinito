<?php
// Archivo de prueba para verificar el sistema de correo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Prueba del Sistema de Correo StudioOtaku</h2>";

// Verificar configuración PHP
echo "<h3>📋 Configuración PHP:</h3>";
echo "<ul>";
echo "<li>Versión PHP: " . phpversion() . "</li>";
echo "<li>Función mail() disponible: " . (function_exists('mail') ? '✅ Sí' : '❌ No') . "</li>";
echo "<li>Extensión openssl: " . (extension_loaded('openssl') ? '✅ Sí' : '❌ No') . "</li>";
echo "<li>Extensión mbstring: " . (extension_loaded('mbstring') ? '✅ Sí' : '❌ No') . "</li>";
echo "</ul>";

// Verificar configuración del servidor
echo "<h3>🖥️ Configuración del Servidor:</h3>";
echo "<ul>";
echo "<li>Servidor web: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "</li>";
echo "<li>Directorio actual: " . getcwd() . "</li>";
echo "<li>Permisos de escritura: " . (is_writable('.') ? '✅ Sí' : '❌ No') . "</li>";
echo "</ul>";

// Probar envío de correo
echo "<h3>📧 Prueba de Envío de Correo:</h3>";

$adminEmail = 'STUDIOOTAKU6@GMAIL.COM';
$subject = "🧪 Prueba de Correo StudioOtaku - " . date('d/m/Y H:i:s');
$message = "
<html>
<head>
    <title>Prueba de Correo</title>
</head>
<body>
    <h2>🧪 Prueba del Sistema de Correo</h2>
    <p>Este es un correo de prueba para verificar que el sistema funcione correctamente.</p>
    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
    <p><strong>Servidor:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No disponible') . "</p>
    <p>Si recibes este correo, el sistema está funcionando correctamente.</p>
</body>
</html>
";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: StudioOtaku <noreply@studiootaku.com>\r\n";

echo "<p>Enviando correo de prueba a: <strong>{$adminEmail}</strong></p>";

try {
    $mailSent = mail($adminEmail, $subject, $message, $headers);
    
    if ($mailSent) {
        echo "<p style='color: green; font-weight: bold;'>✅ Correo enviado exitosamente!</p>";
        echo "<p>Revisa tu bandeja de entrada en {$adminEmail}</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Error al enviar el correo</p>";
        echo "<p>Posibles causas:</p>";
        echo "<ul>";
        echo "<li>Configuración SMTP del servidor</li>";
        echo "<li>Firewall o restricciones del hosting</li>";
        echo "<li>Configuración de correo del servidor</li>";
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Excepción al enviar correo: " . $e->getMessage() . "</p>";
}

// Verificar directorio de uploads
echo "<h3>📁 Verificación de Directorios:</h3>";
$uploadDir = 'uploads/payments/';

if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p style='color: green;'>✅ Directorio de uploads creado: {$uploadDir}</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear directorio: {$uploadDir}</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Directorio de uploads existe: {$uploadDir}</p>";
    echo "<p>Permisos: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
}

// Verificar archivo de configuración
echo "<h3>⚙️ Verificación de Configuración:</h3>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✅ Archivo config.php existe</p>";
    
    // Verificar conexión a base de datos
    try {
        require_once 'config.php';
        $pdo = getDBConnection();
        echo "<p style='color: green;'>✅ Conexión a base de datos exitosa</p>";
        
        // Verificar tabla payment_requests
        $stmt = $pdo->query("SHOW TABLES LIKE 'payment_requests'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Tabla payment_requests existe</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tabla payment_requests no existe</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Archivo config.php no encontrado</p>";
}

echo "<hr>";
echo "<p><strong>💡 Recomendaciones:</strong></p>";
echo "<ul>";
echo "<li>Si el correo no se envía, verifica la configuración SMTP de tu hosting</li>";
echo "<li>Algunos hostings requieren configuración específica para envío de correos</li>";
echo "<li>Verifica que no haya restricciones de firewall</li>";
echo "<li>Contacta a tu proveedor de hosting si persisten los problemas</li>";
echo "</ul>";

echo "<p><a href='index.html'>🏠 Volver al sitio principal</a></p>";
?> 