<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

class Subscription {
    private $pdo;
    private $auth;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->auth = new Auth();
    }
    
    // Crear suscripción VIP
    public function createSubscription($userId, $amount, $currency, $paymentProof = null) {
        try {
            $paypalPaymentId = 'PAY-' . uniqid();
            
            $stmt = $this->pdo->prepare("INSERT INTO subscriptions (user_id, paypal_payment_id, amount, currency, payment_proof) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $paypalPaymentId, $amount, $currency, $paymentProof]);
            
            // Enviar correo de confirmación
            $this->sendSubscriptionEmail($userId, $amount, $currency, $paypalPaymentId);
            
            return ['success' => true, 'message' => 'Suscripción creada exitosamente', 'payment_id' => $paypalPaymentId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al crear suscripción: ' . $e->getMessage()];
        }
    }
    
    // Aprobar suscripción (admin)
    public function approveSubscription($subscriptionId) {
        try {
            $this->pdo->beginTransaction();
            
            // Obtener información de la suscripción
            $stmt = $this->pdo->prepare("SELECT user_id, amount, currency FROM subscriptions WHERE id = ? AND status = 'pending'");
            $stmt->execute([$subscriptionId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subscription) {
                throw new Exception('Suscripción no encontrada o ya procesada');
            }
            
            // Actualizar estado de la suscripción
            $stmt = $this->pdo->prepare("UPDATE subscriptions SET status = 'completed' WHERE id = ?");
            $stmt->execute([$subscriptionId]);
            
            // Activar VIP del usuario por 1 mes
            $vipExpiresAt = date('Y-m-d H:i:s', strtotime('+1 month'));
            $stmt = $this->pdo->prepare("UPDATE users SET is_vip = TRUE, vip_expires_at = ? WHERE id = ?");
            $stmt->execute([$vipExpiresAt, $subscription['user_id']]);
            
            // Enviar correo de confirmación VIP
            $this->sendVIPActivationEmail($subscription['user_id']);
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Suscripción aprobada exitosamente'];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Error al aprobar suscripción: ' . $e->getMessage()];
        }
    }
    
    // Rechazar suscripción (admin)
    public function rejectSubscription($subscriptionId, $reason = '') {
        try {
            $stmt = $this->pdo->prepare("UPDATE subscriptions SET status = 'failed' WHERE id = ?");
            $stmt->execute([$subscriptionId]);
            
            // Obtener usuario para enviar correo
            $stmt = $this->pdo->prepare("SELECT user_id FROM subscriptions WHERE id = ?");
            $stmt->execute([$subscriptionId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($subscription) {
                $this->sendSubscriptionRejectionEmail($subscription['user_id'], $reason);
            }
            
            return ['success' => true, 'message' => 'Suscripción rechazada exitosamente'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al rechazar suscripción: ' . $e->getMessage()];
        }
    }
    
    // Obtener suscripciones pendientes (admin)
    public function getPendingSubscriptions() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, u.username, u.email 
                FROM subscriptions s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.status = 'pending' 
                ORDER BY s.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Enviar correo de confirmación de suscripción
    private function sendSubscriptionEmail($userId, $amount, $currency, $paymentId) {
        try {
            $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $subject = "Suscripción VIP StudioOtaku - Pendiente de Aprobación";
            $message = "
                <html>
                <body>
                    <h2>¡Hola {$user['username']}!</h2>
                    <p>Tu solicitud de suscripción VIP ha sido recibida.</p>
                    <p><strong>Detalles de la suscripción:</strong></p>
                    <ul>
                        <li>ID de Pago: {$paymentId}</li>
                        <li>Monto: {$amount} {$currency}</li>
                        <li>Estado: Pendiente de aprobación</li>
                    </ul>
                    <p>Recibirás un correo de confirmación una vez que tu pago sea verificado.</p>
                    <p>¡Gracias por elegir StudioOtaku!</p>
                </body>
                </html>
            ";
            
            $this->sendEmail($user['email'], $subject, $message);
        } catch (Exception $e) {
            error_log("Error enviando correo de suscripción: " . $e->getMessage());
        }
    }
    
    // Enviar correo de activación VIP
    private function sendVIPActivationEmail($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $subject = "¡Tu suscripción VIP StudioOtaku ha sido activada!";
            $message = "
                <html>
                <body>
                    <h2>¡Felicidades {$user['username']}!</h2>
                    <p>Tu suscripción VIP ha sido aprobada y activada exitosamente.</p>
                    <p>Ahora tienes acceso completo a todos los animes de StudioOtaku.</p>
                    <p>Tu VIP estará activo por 1 mes desde hoy.</p>
                    <p>¡Disfruta de tu experiencia premium!</p>
                </body>
                </html>
            ";
            
            $this->sendEmail($user['email'], $subject, $message);
        } catch (Exception $e) {
            error_log("Error enviando correo de activación VIP: " . $e->getMessage());
        }
    }
    
    // Enviar correo de rechazo de suscripción
    private function sendSubscriptionRejectionEmail($userId, $reason) {
        try {
            $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $subject = "Suscripción VIP StudioOtaku - Requiere atención";
            $message = "
                <html>
                <body>
                    <h2>Hola {$user['username']}</h2>
                    <p>Tu solicitud de suscripción VIP no pudo ser procesada.</p>
                    <p><strong>Motivo:</strong> {$reason}</p>
                    <p>Por favor, verifica tu información de pago e intenta nuevamente.</p>
                    <p>Si tienes alguna pregunta, contáctanos.</p>
                </body>
                </html>
            ";
            
            $this->sendEmail($user['email'], $subject, $message);
        } catch (Exception $e) {
            error_log("Error enviando correo de rechazo: " . $e->getMessage());
        }
    }
    
    // Función para enviar correos
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . SMTP_USER . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
}

// Instanciar la clase de suscripciones
$subscription = new Subscription();

// Procesar solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_subscription':
                if (!$this->auth->isAuthenticated()) {
                    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
                    exit;
                }
                
                $result = $subscription->createSubscription(
                    $_SESSION['user_id'],
                    $_POST['amount'],
                    $_POST['currency'],
                    $_POST['payment_proof'] ?? null
                );
                echo json_encode($result);
                exit;
                
            case 'approve_subscription':
                // Solo admin puede aprobar
                if (!$this->auth->isAuthenticated() || $_SESSION['username'] !== 'admin') {
                    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
                    exit;
                }
                
                $result = $subscription->approveSubscription($_POST['subscription_id']);
                echo json_encode($result);
                exit;
                
            case 'reject_subscription':
                // Solo admin puede rechazar
                if (!$this->auth->isAuthenticated() || $_SESSION['username'] !== 'admin') {
                    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
                    exit;
                }
                
                $result = $subscription->rejectSubscription($_POST['subscription_id'], $_POST['reason'] ?? '');
                echo json_encode($result);
                exit;
        }
    }
}
?> 