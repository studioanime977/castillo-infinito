<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'gmail_config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'google_login':
        handleGoogleLogin();
        break;
    case 'google_callback':
        handleGoogleCallback();
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

function handleGoogleLogin() {
    try {
        // Obtener el token de acceso del frontend
        $accessToken = $_POST['access_token'] ?? '';
        
        if (empty($accessToken)) {
            echo json_encode(['success' => false, 'message' => 'Token de acceso requerido']);
            return;
        }
        
        // Obtener información del usuario de Google
        $googleUserInfo = getGoogleUserInfo($accessToken);
        
        if (!$googleUserInfo) {
            echo json_encode(['success' => false, 'message' => 'No se pudo obtener información de Google']);
            return;
        }
        
        // Registrar o actualizar usuario en la base de datos
        $user = registerUserFromGoogle($googleUserInfo);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar usuario']);
            return;
        }
        
        // Crear sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['google_id'] = $user['google_id'];
        $_SESSION['google_picture'] = $user['google_picture'];
        $_SESSION['is_vip'] = $user['is_vip'] ?? 0;
        $_SESSION['is_google_user'] = true;
        
        // Guardar sesión en base de datos
        try {
            require_once 'config.php';
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_id, created_at, expires_at) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR))");
            $stmt->execute([$user['id'], session_id()]);
        } catch (Exception $e) {
            error_log("Error guardando sesión: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => '¡Bienvenido ' . $user['username'] . '!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'google_picture' => $user['google_picture'],
                'is_vip' => $user['is_vip'] ?? 0,
                'is_google_user' => true
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error en Google login: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
    }
}

function handleGoogleCallback() {
    // Esta función se llamaría desde el callback de OAuth
    // Por ahora, manejamos todo desde el frontend
    echo json_encode(['success' => false, 'message' => 'Callback no implementado']);
}

function handleLogout() {
    try {
        // Eliminar sesión de la base de datos
        if (isset($_SESSION['user_id'])) {
            try {
                require_once 'config.php';
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_id = ?");
                $stmt->execute([$_SESSION['user_id'], session_id()]);
            } catch (Exception $e) {
                error_log("Error eliminando sesión: " . $e->getMessage());
            }
        }
        
        // Destruir sesión
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente']);
        
    } catch (Exception $e) {
        error_log("Error en logout: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al cerrar sesión']);
    }
}
?> 