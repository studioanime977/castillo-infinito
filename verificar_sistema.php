<?php
// Archivo de verificación completa del sistema StudioOtaku
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔍 Verificación del Sistema StudioOtaku</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }";
echo ".success { color: #28a745; font-weight: bold; }";
echo ".error { color: #dc3545; font-weight: bold; }";
echo ".warning { color: #ffc107; font-weight: bold; }";
echo ".section { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007bff; }";
echo ".test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }";
echo ".test-success { background: #d4edda; border-left: 4px solid #28a745; }";
echo ".test-error { background: #f8d7da; border-left: 4px solid #dc3545; }";
echo ".test-warning { background: #fff3cd; border-left: 4px solid #ffc107; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Verificación del Sistema StudioOtaku</h1>";
echo "<p>Este archivo verifica que todos los componentes del sistema estén funcionando correctamente.</p>";

// 1. Verificar PHP
echo "<div class='section'>";
echo "<h2>📋 1. Verificación de PHP</h2>";

$phpVersion = phpversion();
echo "<div class='test-result test-success'>";
echo "<strong>✅ Versión PHP:</strong> {$phpVersion}";
echo "</div>";

if (function_exists('mail')) {
    echo "<div class='test-result test-success'>";
    echo "<strong>✅ Función mail():</strong> Disponible";
    echo "</div>";
} else {
    echo "<div class='test-result test-error'>";
    echo "<strong>❌ Función mail():</strong> No disponible";
    echo "</div>";
}

if (extension_loaded('pdo')) {
    echo "<div class='test-result test-success'>";
    echo "<strong>✅ Extensión PDO:</strong> Disponible";
    echo "</div>";
} else {
    echo "<div class='test-result test-error'>";
    echo "<strong>❌ Extensión PDO:</strong> No disponible";
    echo "</div>";
}

echo "</div>";

// 2. Verificar archivos
echo "<div class='section'>";
echo "<h2>📁 2. Verificación de Archivos</h2>";

$requiredFiles = [
    'index.html' => 'Archivo principal del sitio',
    'config.php' => 'Configuración del sistema',
    'auth.php' => 'Sistema de autenticación',
    'subscription.php' => 'Sistema de suscripciones',
    'payment_form.php' => 'Formulario de pagos',
    'admin.php' => 'Panel administrativo'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='test-result test-success'>";
        echo "<strong>✅ {$file}:</strong> {$description} - Existe";
        echo "</div>";
    } else {
        echo "<div class='test-result test-error'>";
        echo "<strong>❌ {$file}:</strong> {$description} - NO EXISTE";
        echo "</div>";
    }
}

echo "</div>";

// 3. Verificar directorios
echo "<div class='section'>";
echo "<h2>📂 3. Verificación de Directorios</h2>";

$uploadDir = 'uploads/payments/';
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "<div class='test-result test-success'>";
        echo "<strong>✅ Directorio uploads:</strong> Creado exitosamente";
        echo "</div>";
    } else {
        echo "<div class='test-result test-error'>";
        echo "<strong>❌ Directorio uploads:</strong> No se pudo crear";
        echo "</div>";
    }
} else {
    echo "<div class='test-result test-success'>";
    echo "<strong>✅ Directorio uploads:</strong> Ya existe";
    echo "</div>";
}

if (is_writable($uploadDir)) {
    echo "<div class='test-result test-success'>";
    echo "<strong>✅ Permisos uploads:</strong> Escritura permitida";
    echo "</div>";
} else {
    echo "<div class='test-result test-error'>";
    echo "<strong>❌ Permisos uploads:</strong> Sin permisos de escritura";
    echo "</div>";
}

echo "</div>";

// 4. Verificar base de datos
echo "<div class='section'>";
echo "<h2>🗄️ 4. Verificación de Base de Datos</h2>";

if (file_exists('config.php')) {
    try {
        require_once 'config.php';
        $pdo = getDBConnection();
        echo "<div class='test-result test-success'>";
        echo "<strong>✅ Conexión BD:</strong> Exitosa";
        echo "</div>";
        
        // Verificar tablas
        $tables = ['users', 'subscriptions', 'user_sessions', 'payment_requests'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='test-result test-success'>";
                echo "<strong>✅ Tabla {$table}:</strong> Existe";
                echo "</div>";
            } else {
                echo "<div class='test-result test-warning'>";
                echo "<strong>⚠️ Tabla {$table}:</strong> No existe";
                echo "</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='test-result test-error'>";
        echo "<strong>❌ Error BD:</strong> " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='test-result test-error'>";
    echo "<strong>❌ Configuración:</strong> Archivo config.php no encontrado";
    echo "</div>";
}

echo "</div>";

// 5. Probar envío de correo
echo "<div class='section'>";
echo "<h2>📧 5. Prueba de Envío de Correo</h2>";

$adminEmail = 'STUDIOOTAKU6@GMAIL.COM';
$subject = "🧪 Prueba de Correo StudioOtaku - " . date('d/m/Y H:i:s');
$message = "
<html>
<head><title>Prueba de Correo</title></head>
<body>
    <h2>🧪 Prueba del Sistema de Correo</h2>
    <p>Este es un correo de prueba para verificar que el sistema funcione correctamente.</p>
    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
    <p><strong>Servidor:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No disponible') . "</p>
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
        echo "<div class='test-result test-success'>";
        echo "<strong>✅ Correo:</strong> Enviado exitosamente!";
        echo "</div>";
        echo "<p>Revisa tu bandeja de entrada en {$adminEmail}</p>";
    } else {
        echo "<div class='test-result test-error'>";
        echo "<strong>❌ Correo:</strong> No se pudo enviar";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div class='test-result test-error'>";
    echo "<strong>❌ Excepción:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";

// 6. Resumen y recomendaciones
echo "<div class='section'>";
echo "<h2>📋 6. Resumen y Recomendaciones</h2>";

echo "<h3>✅ Si todo está funcionando:</h3>";
echo "<ul>";
echo "<li>El sistema está listo para usar</li>";
echo "<li>Los usuarios pueden enviar pagos</li>";
echo "<li>Recibirás correos automáticamente</li>";
echo "<li>El panel admin está disponible</li>";
echo "</ul>";

echo "<h3>⚠️ Si hay problemas:</h3>";
echo "<ul>";
echo "<li><strong>Archivos faltantes:</strong> Sube todos los archivos al servidor</li>";
echo "<li><strong>Base de datos:</strong> Ejecuta database.sql en tu MySQL</li>";
echo "<li><strong>Correos no funcionan:</strong> Contacta a tu proveedor de hosting</li>";
echo "<li><strong>Permisos:</strong> Verifica permisos 755 para archivos y 644 para directorios</li>";
echo "</ul>";

echo "<h3>🔧 Pasos para solucionar:</h3>";
echo "<ol>";
echo "<li>Sube TODOS los archivos al servidor</li>";
echo "<li>Verifica que PHP esté habilitado</li>";
echo "<li>Configura la base de datos con database.sql</li>";
echo "<li>Edita config.php con tus credenciales</li>";
echo "<li>Prueba el sistema con este archivo</li>";
echo "</ol>";

echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Volver al Sitio Principal</a>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?> 