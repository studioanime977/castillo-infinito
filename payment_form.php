<?php
// Archivo para manejar pagos sin autenticación con Gmail API
header('Content-Type: application/json; charset=utf-8');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar datos
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$currency = trim($_POST['currency'] ?? '');
$amount = trim($_POST['amount'] ?? '');

// Validar datos básicos
if (empty($name) || empty($email) || empty($currency) || empty($amount)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email no válido']);
    exit;
}

// Crear directorio de uploads si no existe
$uploadDir = 'uploads/payments/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Error al crear directorio de uploads']);
        exit;
    }
}

// Procesar archivo
$fileName = '';
$filePath = '';
if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
    $fileExtension = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $fileName = 'payment_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $filePath)) {
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF, PDF']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al subir el archivo de comprobante']);
    exit;
}

// Configurar correo
$adminEmail = 'STUDIOOTAKU6@GMAIL.COM';
$subject = "💰 Nuevo Pago VIP StudioOtaku - " . htmlspecialchars($name);

// Mensaje HTML mejorado
$message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Nuevo Pago VIP</title>
</head>
<body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;'>
    <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
        <div style='background: linear-gradient(45deg, #ff5cad, #ff66cc); color: white; padding: 30px; border-radius: 10px; text-align: center; margin: -20px -20px 20px -20px;'>
            <h1 style='margin: 0; font-size: 28px;'>💰 Nuevo Pago VIP StudioOtaku</h1>
            <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Se ha recibido una nueva solicitud de suscripción VIP</p>
        </div>
        
        <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3 style='margin: 0 0 15px 0; color: #333;'>👤 Información del Cliente</h3>
            <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>
                <div><strong>Nombre:</strong> " . htmlspecialchars($name) . "</div>
                <div><strong>Email:</strong> " . htmlspecialchars($email) . "</div>
                <div><strong>Moneda:</strong> " . htmlspecialchars($currency) . "</div>
                <div><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</div>
            </div>
        </div>
        
        <div style='text-align: center; margin: 30px 0;'>
            <div style='font-size: 36px; font-weight: bold; color: #28a745;'>💰 " . number_format($amount, 0, ',', '.') . "</div>
            <div style='color: #666; font-size: 18px;'>" . htmlspecialchars($currency) . "</div>
        </div>
        
        <div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;'>
            <h3 style='margin: 0 0 15px 0; color: #28a745;'>🏦 Información de Pago</h3>
            <p style='margin: 8px 0;'><strong>Método:</strong> Llave Bancaria</p>
            <p style='margin: 8px 0;'><strong>Llave:</strong> <span style='background: #ff5cad; color: #000; padding: 8px 15px; border-radius: 8px; font-weight: bold; font-family: monospace; font-size: 16px;'>STUDIOOTAKU6@GMAIL.COM</span></p>
            <p style='margin: 8px 0;'><strong>Métodos aceptados:</strong> Nequi, Daviplata, Bancos colombianos</p>
        </div>
        
        <div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;'>
            <p style='margin: 0; color: #856404;'><strong>📎 Comprobante:</strong> El archivo '{$fileName}' ha sido subido exitosamente.</p>
        </div>
        
        <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3 style='margin: 0 0 15px 0; color: #333;'>📋 Acciones Requeridas</h3>
            <ol style='margin: 0; padding-left: 20px; color: #555;'>
                <li style='margin: 8px 0;'>Verificar el pago en tu cuenta bancaria/llave</li>
                <li style='margin: 8px 0;'>Revisar el comprobante adjunto</li>
                <li style='margin: 8px 0;'>Confirmar que el monto coincida</li>
                <li style='margin: 8px 0;'>Activar el VIP del usuario si el pago es correcto</li>
                <li style='margin: 8px 0;'>Enviar confirmación al usuario</li>
            </ol>
        </div>
        
        <div style='background: #f8f9fa; padding: 20px; text-align: center; color: #666; margin: 20px -20px -20px -20px; border-radius: 0 0 10px 10px;'>
            <p style='margin: 5px 0;'>Este correo fue enviado automáticamente por el sistema StudioOtaku</p>
            <p style='margin: 5px 0;'>No responder a este correo</p>
        </div>
    </div>
</body>
</html>
";

// Intentar múltiples métodos de envío
$mailSent = false;
$methodUsed = '';

// Método 1: Gmail API (si está configurado)
try {
    if (file_exists('gmail_config.php')) {
        require_once 'gmail_config.php';
        if (defined('GMAIL_CLIENT_ID') && GMAIL_CLIENT_ID !== 'PON_AQUI_TU_CLIENT_ID.apps.googleusercontent.com') {
            // Aquí se implementaría el envío por Gmail API
            // Por ahora, saltamos al siguiente método
        }
    }
} catch (Exception $e) {
    error_log("Error con Gmail API: " . $e->getMessage());
}

// Método 2: Función mail() nativa con headers optimizados
if (!$mailSent) {
    try {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: StudioOtaku <noreply@studiootaku.com>\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";
        
        $mailSent = mail($adminEmail, $subject, $message, $headers);
        if ($mailSent) {
            $methodUsed = 'mail() nativa';
        }
    } catch (Exception $e) {
        error_log("Error con mail(): " . $e->getMessage());
    }
}

// Método 3: Headers alternativos si el anterior falla
if (!$mailSent) {
    try {
        $simpleHeaders = "MIME-Version: 1.0\r\n";
        $simpleHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
        $simpleHeaders .= "From: StudioOtaku <noreply@studiootaku.com>\r\n";
        
        $mailSent = mail($adminEmail, $subject, $message, $simpleHeaders);
        if ($mailSent) {
            $methodUsed = 'mail() con headers simples';
        }
    } catch (Exception $e) {
        error_log("Error con headers simples: " . $e->getMessage());
    }
}

// Intentar guardar en base de datos
$dbSaved = false;
try {
    if (file_exists('config.php')) {
        require_once 'config.php';
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO payment_requests (name, email, currency, amount, payment_proof, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$name, $email, $currency, $amount, $fileName]);
        $dbSaved = true;
    }
} catch (Exception $e) {
    error_log("Error de base de datos: " . $e->getMessage());
}

// Respuesta final con información detallada
if ($mailSent) {
    echo json_encode([
        'success' => true, 
        'message' => '¡Pago enviado exitosamente! Recibirás un correo de confirmación una vez verificado.',
        'mail_sent' => true,
        'db_saved' => $dbSaved,
        'method_used' => $methodUsed,
        'file_uploaded' => $fileName
    ]);
} else {
    if ($dbSaved) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pago recibido. El correo no se pudo enviar, pero tu solicitud está registrada.',
            'mail_sent' => false,
            'db_saved' => true,
            'file_uploaded' => $fileName,
            'note' => 'Contacta soporte para activar tu VIP manualmente'
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'Pago recibido. Tu solicitud ha sido procesada.',
            'mail_sent' => false,
            'db_saved' => false,
            'file_uploaded' => $fileName,
            'note' => 'El archivo se subió correctamente'
        ]);
    }
}
?> 