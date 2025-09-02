<?php
session_start();
require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    // Registrar nuevo usuario
    public function register($username, $email, $password) {
        try {
            // Verificar si el usuario ya existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }
            
            // Hash de la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);
            
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }
    
    // Iniciar sesión
    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, password, is_vip, vip_expires_at FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Crear sesión
                $sessionToken = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                $stmt = $this->pdo->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['id'], $sessionToken, $expiresAt]);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_vip'] = $user['is_vip'];
                $_SESSION['vip_expires_at'] = $user['vip_expires_at'];
                $_SESSION['session_token'] = $sessionToken;
                
                return ['success' => true, 'message' => 'Sesión iniciada exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Credenciales incorrectas'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()];
        }
    }
    
    // Cerrar sesión
    public function logout() {
        if (isset($_SESSION['session_token'])) {
            $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->execute([$_SESSION['session_token']]);
        }
        
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada exitosamente'];
    }
    
    // Verificar si el usuario está autenticado
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            return false;
        }
        
        // Verificar si la sesión sigue siendo válida
        $stmt = $this->pdo->prepare("SELECT user_id FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
        $stmt->execute([$_SESSION['session_token']]);
        
        return $stmt->rowCount() > 0;
    }
    
    // Verificar si el usuario es VIP
    public function isVIP() {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        if (!$_SESSION['is_vip']) {
            return false;
        }
        
        // Verificar si el VIP no ha expirado
        if ($_SESSION['vip_expires_at'] && strtotime($_SESSION['vip_expires_at']) < time()) {
            // Actualizar estado VIP
            $stmt = $this->pdo->prepare("UPDATE users SET is_vip = FALSE, vip_expires_at = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            $_SESSION['is_vip'] = false;
            $_SESSION['vip_expires_at'] = null;
            
            return false;
        }
        
        return true;
    }
    
    // Obtener información del usuario actual
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $stmt = $this->pdo->prepare("SELECT id, username, email, is_vip, vip_expires_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Instanciar la clase de autenticación
$auth = new Auth();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register':
                $result = $auth->register($_POST['username'], $_POST['email'], $_POST['password']);
                echo json_encode($result);
                exit;
                
            case 'login':
                $result = $auth->login($_POST['username'], $_POST['password']);
                echo json_encode($result);
                exit;
                
            case 'logout':
                $result = $auth->logout();
                echo json_encode($result);
                exit;
        }
    }
}

// Si es una petición GET, devolver estado de autenticación
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($auth->isAuthenticated()) {
        $user = $auth->getCurrentUser();
        echo json_encode([
            'authenticated' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'authenticated' => false
        ]);
    }
    exit;
}
?> 