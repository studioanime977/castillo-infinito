<?php
// Configuración de la API de Gmail para StudioOtaku
define('GMAIL_CLIENT_ID', 'PON_AQUI_TU_CLIENT_ID.apps.googleusercontent.com');
define('GMAIL_CLIENT_SECRET', 'PON_AQUI_TU_CLIENT_SECRET');
define('GMAIL_REDIRECT_URI', 'https://tudominio.com/gmail_callback.php');
define('GMAIL_SCOPES', 'https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');

// Función para obtener token de acceso
function getGmailAccessToken() {
    // Aquí implementarías la lógica OAuth2 para Gmail
    // Por ahora, retornamos null para usar el sistema alternativo
    return null;
}

// Función para enviar correo usando Gmail API
function sendGmailAPI($to, $subject, $message, $attachmentPath = null) {
    // Implementación de Gmail API
    // Por ahora, retornamos false para usar el sistema alternativo
    return false;
}

// Función para obtener información del usuario de Google
function getGoogleUserInfo($accessToken) {
    if (empty($accessToken)) {
        return null;
    }
    
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken;
    $response = file_get_contents($url);
    
    if ($response === false) {
        return null;
    }
    
    return json_decode($response, true);
}

// Función para registrar usuario desde Google
function registerUserFromGoogle($googleUserInfo) {
    try {
        require_once 'config.php';
        $pdo = getDBConnection();
        
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
        $stmt->execute([$googleUserInfo['id'], $googleUserInfo['email']]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser) {
            // Usuario existe, actualizar información de Google
            $stmt = $pdo->prepare("UPDATE users SET google_id = ?, google_name = ?, google_picture = ?, last_login = NOW() WHERE id = ?");
            $stmt->execute([
                $googleUserInfo['id'],
                $googleUserInfo['name'],
                $googleUserInfo['picture'] ?? null,
                $existingUser['id']
            ]);
            return $existingUser;
        } else {
            // Crear nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO users (username, email, google_id, google_name, google_picture, created_at, last_login) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $googleUserInfo['name'],
                $googleUserInfo['email'],
                $googleUserInfo['id'],
                $googleUserInfo['name'],
                $googleUserInfo['picture'] ?? null
            ]);
            
            $userId = $pdo->lastInsertId();
            return [
                'id' => $userId,
                'username' => $googleUserInfo['name'],
                'email' => $googleUserInfo['email'],
                'google_id' => $googleUserInfo['id'],
                'google_name' => $googleUserInfo['name'],
                'google_picture' => $googleUserInfo['picture'] ?? null,
                'is_vip' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'last_login' => date('Y-m-d H:i:s')
            ];
        }
    } catch (Exception $e) {
        error_log("Error registrando usuario de Google: " . $e->getMessage());
        return null;
    }
}
?> 