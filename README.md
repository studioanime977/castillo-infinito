# 🧧 StudioOtaku - Sistema de Anime con Suscripción VIP

## 📋 Descripción

StudioOtaku es una plataforma web para descargar animes con un sistema de suscripción VIP que incluye:
- ✅ Sistema de registro e inicio de sesión
- ✅ Suscripción VIP mensual (20.000 COP / $5 USD)
- ✅ Integración con PayPal
- ✅ Panel administrativo
- ✅ Envío automático de correos
- ✅ Bloqueo de contenido para usuarios no VIP

## 🚀 Instalación

### Requisitos Previos
- Servidor web con PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión PHP PDO habilitada
- Extensión PHP mail habilitada

### Paso 1: Configurar Base de Datos
1. Crear una base de datos MySQL llamada `studiootaku_db`
2. Importar el archivo `database.sql` en tu base de datos
3. O ejecutar manualmente las consultas SQL del archivo

### Paso 2: Configurar Archivos
1. **Editar `config.php`:**
   ```php
   // Configuración de la base de datos
   define('DB_HOST', 'localhost');        // Tu host de base de datos
   define('DB_USER', 'tu_usuario');      // Tu usuario de MySQL
   define('DB_PASS', 'tu_password');     // Tu contraseña de MySQL
   define('DB_NAME', 'studiootaku_db');  // Nombre de tu base de datos
   
   // Configuración de PayPal (opcional para desarrollo)
   define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_AQUI');
   define('PAYPAL_CLIENT_SECRET', 'TU_CLIENT_SECRET_AQUI');
   
   // Configuración de correo
   define('SMTP_USER', 'STUDIOOTAKU6@GMAIL.COM');
   define('SMTP_PASS', 'TU_PASSWORD_DE_APLICACION_GMAIL');
   
   // URL de tu sitio
   define('SITE_URL', 'https://tudominio.com');
   ```

2. **Configurar Gmail para envío de correos:**
   - Habilitar autenticación de 2 factores en tu cuenta Gmail
   - Generar una contraseña de aplicación
   - Usar esa contraseña en `SMTP_PASS`

### Paso 3: Permisos de Archivos
```bash
chmod 755 *.php
chmod 755 *.html
chmod 644 *.css
chmod 644 *.js
```

### Paso 4: Acceso Administrativo
- **URL del panel admin:** `https://tudominio.com/admin.php`
- **Usuario por defecto:** `admin`
- **Contraseña por defecto:** `admin123`

⚠️ **IMPORTANTE:** Cambia la contraseña del administrador después de la primera instalación.

## 💳 Configuración de Pagos

### PayPal (USD)
- **Enlace:** [https://paypal.me/injenieroensistemas](https://paypal.me/injenieroensistemas)
- **Precio:** $5.00 USD por mes
- **Método:** Pagos internacionales

### Llave Bancaria (COP)
- **Llave:** STUDIOOTAKU6@GMAIL.COM
- **Precio:** $20.000 COP por mes
- **Métodos:** Nequi, Daviplata, Bancos colombianos
- **Proceso:** Pago directo + comprobante

## 🔧 Funcionalidades del Sistema

### Para Usuarios
- **Registro:** Crear cuenta con username, email y contraseña
- **Login:** Iniciar sesión con credenciales
- **Suscripción VIP:** Acceder a contenido premium
- **Descargas:** Acceso completo a todos los animes (solo VIP)

### Para Administradores
- **Dashboard:** Estadísticas generales del sistema
- **Gestión de Suscripciones:** Aprobar/rechazar pagos pendientes
- **Gestión de Usuarios:** Ver todos los usuarios y su estado VIP
- **Configuración:** Ajustar precios y configuraciones

## 📧 Sistema de Correos

El sistema envía automáticamente:
1. **Confirmación de suscripción** - Al crear una suscripción
2. **Activación VIP** - Al aprobar un pago
3. **Rechazo de suscripción** - Al rechazar un pago

## 🛡️ Seguridad

- Contraseñas hasheadas con bcrypt
- Sesiones seguras con tokens únicos
- Protección contra SQL injection
- Validación de formularios
- Control de acceso administrativo

## 📱 Características Responsivas

- Diseño adaptativo para móviles y tablets
- Interfaz moderna y atractiva
- Navegación intuitiva
- Modales responsivos

## 🔄 Flujo de Suscripción VIP

1. **Usuario se registra** en la plataforma
2. **Selecciona plan VIP** (COP o USD)
3. **Realiza el pago** según el método elegido
4. **Sube comprobante** de pago
5. **Administrador revisa** y aprueba/rechaza
6. **Sistema activa VIP** automáticamente
7. **Usuario recibe correo** de confirmación

## 📊 Base de Datos

### Tablas Principales
- **users:** Información de usuarios y estado VIP
- **subscriptions:** Historial de suscripciones y pagos
- **user_sessions:** Gestión de sesiones activas

### Índices de Rendimiento
- Búsquedas rápidas por email y username
- Filtrado eficiente de suscripciones por estado
- Gestión optimizada de sesiones

## 🚨 Solución de Problemas

### Error de Conexión a Base de Datos
- Verificar credenciales en `config.php`
- Confirmar que MySQL esté ejecutándose
- Verificar permisos del usuario de base de datos

### Correos No Se Envían
- Verificar configuración SMTP en `config.php`
- Confirmar contraseña de aplicación de Gmail
- Revisar logs del servidor

### Panel Admin No Accesible
- Verificar que el usuario 'admin' exista en la base de datos
- Confirmar permisos de archivos
- Revisar configuración de sesiones

## 📞 Soporte

Para soporte técnico o consultas:
- **Email:** STUDIOOTAKU6@GMAIL.COM
- **WhatsApp:** [Link del grupo](https://chat.whatsapp.com/LgQP3YXx2eS2XRav5ashRm)

## 📄 Licencia

Este proyecto es propiedad de StudioOtaku. Todos los derechos reservados.

---

**🎌 ¡Disfruta de tu plataforma de anime premium! 🎌** 