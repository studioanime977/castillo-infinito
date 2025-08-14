<?php
// Configuración de la base de datos AlwaysData
define('DB_HOST', 'mysql-studiootaku.alwaysdata.net');
define('DB_USER', '426590'); // Usuario de AlwaysData
define('DB_PASS', 'Cs181029**'); // Contraseña de AlwaysData
define('DB_NAME', 'studiootaku_01');

// Configuración de PayPal
define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_AQUI');
define('PAYPAL_CLIENT_SECRET', 'TU_CLIENT_SECRET_AQUI');
define('PAYPAL_CURRENCY_COP', 'COP');
define('PAYPAL_CURRENCY_USD', 'USD');
define('SUBSCRIPTION_PRICE_COP', 20000);
define('SUBSCRIPTION_PRICE_USD', 5.00);

// Configuración de correo
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'STUDIOOTAKU6@GMAIL.COM');
define('SMTP_PASS', 'TU_PASSWORD_DE_APLICACION');

// Configuración del sitio
define('SITE_NAME', 'StudioOtaku');
define('SITE_URL', 'http://localhost/castillo-infinito');

// Crear conexión a la base de datos
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES utf8");
        return $pdo;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Crear tablas si no existen
function createTables() {
    $pdo = getDBConnection();
    
    // Tabla de usuarios
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        is_vip BOOLEAN DEFAULT FALSE,
        vip_expires_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Tabla de suscripciones
    $sql = "CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        paypal_payment_id VARCHAR(100) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        currency VARCHAR(3) NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        payment_proof VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    
    // Tabla de sesiones
    $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_token VARCHAR(255) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
}

// Inicializar tablas
createTables();
?> 