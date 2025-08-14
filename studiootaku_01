-- Base de datos para StudioOtaku con soporte para Google OAuth
CREATE DATABASE IF NOT EXISTS studiootaku_db;
USE studiootaku_db;

-- Tabla de usuarios con soporte para Google OAuth
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NULL, -- NULL para usuarios de Google
    google_id VARCHAR(100) UNIQUE NULL, -- ID único de Google
    google_name VARCHAR(100) NULL, -- Nombre de Google
    google_picture VARCHAR(500) NULL, -- URL de la foto de perfil de Google
    is_vip BOOLEAN DEFAULT FALSE,
    vip_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_google_id (google_id),
    INDEX idx_vip_status (is_vip, vip_expires_at)
);

-- Tabla de suscripciones VIP
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    payment_proof VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_status (status)
);

-- Tabla de sesiones de usuario
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
);

-- Tabla de solicitudes de pago (para usuarios no autenticados)
CREATE TABLE IF NOT EXISTS payment_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_proof VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Insertar usuario administrador por defecto
INSERT INTO users (username, email, password_hash, is_vip, created_at) VALUES 
('admin', 'admin@studiootaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, NOW())
ON DUPLICATE KEY UPDATE username = username;

-- Crear índices adicionales para optimización
CREATE INDEX idx_users_google ON users(google_id, email);
CREATE INDEX idx_users_vip ON users(is_vip, vip_expires_at);
CREATE INDEX idx_subscriptions_user ON subscriptions(user_id, created_at);
CREATE INDEX idx_sessions_expires ON user_sessions(expires_at); 