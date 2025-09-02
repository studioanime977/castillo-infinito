<?php
session_start();
require_once 'config.php';
require_once 'auth.php';
require_once 'subscription.php';

$auth = new Auth();
$subscription = new Subscription();

// Verificar si es admin
if (!$auth->isAuthenticated() || $_SESSION['username'] !== 'admin') {
    header('Location: index.html');
    exit;
}

// Obtener estadísticas
$pdo = getDBConnection();

// Total de usuarios
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Usuarios VIP
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE is_vip = TRUE AND vip_expires_at > NOW()");
$totalVIP = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Suscripciones pendientes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM subscriptions WHERE status = 'pending'");
$pendingSubscriptions = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener suscripciones pendientes
$pendingSubs = $subscription->getPendingSubscriptions();

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT id, username, email, is_vip, vip_expires_at, created_at FROM users ORDER BY created_at DESC");
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - StudioOtaku</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(180deg, #1c002b 0%, #0a0a0a 100%);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-header {
            background: #29003f;
            padding: 1rem;
            text-align: center;
            border-bottom: 3px solid #ff5cad;
        }
        .admin-header h1 {
            color: #ff66cc;
            text-shadow: 2px 2px #000;
        }
        .admin-nav {
            background: #3c004f;
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        .admin-nav button {
            background: #600088;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .admin-nav button:hover {
            background: #ff5cad;
        }
        .admin-nav button.active {
            background: #ff5cad;
            color: #000;
        }
        .admin-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #1c0030;
            border: 2px solid #ff5cad55;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card h3 {
            color: #ff5cad;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .section {
            background: #1c0030;
            border: 2px solid #ff5cad55;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #00f0ff;
            margin-bottom: 1rem;
            border-bottom: 2px solid #ff5cad;
            padding-bottom: 0.5rem;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ff5cad55;
        }
        th {
            background: #3c004f;
            color: #ff5cad;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0.25rem;
            transition: background 0.3s;
        }
        .btn-approve {
            background: #28a745;
            color: #fff;
        }
        .btn-reject {
            background: #dc3545;
            color: #fff;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .status-failed {
            color: #dc3545;
            font-weight: bold;
        }
        .vip-badge {
            background: #ff5cad;
            color: #000;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>👑 Panel Administrativo StudioOtaku</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>

    <div class="admin-nav">
        <button onclick="showSection('dashboard')" class="active">📊 Dashboard</button>
        <button onclick="showSection('subscriptions')">💳 Suscripciones</button>
        <button onclick="showSection('users')">👥 Usuarios</button>
        <button onclick="showSection('settings')">⚙️ Configuración</button>
        <a href="index.html" style="background: #ff5cad; color: #000; text-decoration: none; padding: 0.5rem 1rem; border-radius: 5px;">🏠 Volver al Sitio</a>
    </div>

    <div class="admin-content">
        <!-- Dashboard -->
        <div id="dashboard" class="section">
            <h2>📊 Estadísticas Generales</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Usuarios</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalVIP; ?></h3>
                    <p>Usuarios VIP Activos</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $pendingSubscriptions; ?></h3>
                    <p>Suscripciones Pendientes</p>
                </div>
            </div>
        </div>

        <!-- Suscripciones -->
        <div id="subscriptions" class="section hidden">
            <h2>💳 Suscripciones Pendientes</h2>
            <?php if (empty($pendingSubs)): ?>
                <p>No hay suscripciones pendientes.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Monto</th>
                                <th>Moneda</th>
                                <th>ID Pago</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingSubs as $sub): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub['username']); ?></td>
                                    <td><?php echo htmlspecialchars($sub['email']); ?></td>
                                    <td><?php echo $sub['amount']; ?></td>
                                    <td><?php echo $sub['currency']; ?></td>
                                    <td><?php echo htmlspecialchars($sub['paypal_payment_id']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($sub['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-approve" onclick="approveSubscription(<?php echo $sub['id']; ?>)">
                                            ✅ Aprobar
                                        </button>
                                        <button class="btn btn-reject" onclick="rejectSubscription(<?php echo $sub['id']; ?>)">
                                            ❌ Rechazar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Usuarios -->
        <div id="users" class="section hidden">
            <h2>👥 Todos los Usuarios</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Estado VIP</th>
                            <th>Expira VIP</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['is_vip'] && $user['vip_expires_at'] && strtotime($user['vip_expires_at']) > time()): ?>
                                        <span class="vip-badge">VIP ACTIVO</span>
                                    <?php else: ?>
                                        <span>No VIP</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($user['vip_expires_at']) {
                                        echo date('d/m/Y H:i', strtotime($user['vip_expires_at']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Configuración -->
        <div id="settings" class="section hidden">
            <h2>⚙️ Configuración del Sistema</h2>
            <div style="background: #2a0040; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                <h3>Configuración de Precios</h3>
                <p><strong>Precio COP:</strong> $<?php echo number_format(SUBSCRIPTION_PRICE_COP, 0, ',', '.'); ?> pesos colombianos</p>
                <p><strong>Precio USD:</strong> $<?php echo SUBSCRIPTION_PRICE_USD; ?> dólares estadounidenses</p>
            </div>
            <div style="background: #2a0040; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                <h3>Configuración de PayPal</h3>
                <p><strong>Email PayPal:</strong> <?php echo SMTP_USER; ?></p>
                <p><strong>PayPal.me:</strong> <a href="https://paypal.me/injenieroensistemas" target="_blank" style="color: #ff5cad;">https://paypal.me/injenieroensistemas</a></p>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionName) {
            // Ocultar todas las secciones
            document.querySelectorAll('.section').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(sectionName).classList.remove('hidden');
            
            // Actualizar botones activos
            document.querySelectorAll('.admin-nav button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function approveSubscription(subscriptionId) {
            if (confirm('¿Estás seguro de que quieres aprobar esta suscripción?')) {
                fetch('subscription.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=approve_subscription&subscription_id=${subscriptionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Suscripción aprobada exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function rejectSubscription(subscriptionId) {
            const reason = prompt('Motivo del rechazo:');
            if (reason !== null) {
                fetch('subscription.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=reject_subscription&subscription_id=${subscriptionId}&reason=${encodeURIComponent(reason)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Suscripción rechazada exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html> 