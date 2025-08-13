# 🔐 Configuración de Google OAuth para StudioOtaku

## 📋 Descripción

Este sistema integra **Google OAuth 2.0** para permitir a los usuarios registrarse e iniciar sesión usando su cuenta de Gmail. Los usuarios pueden acceder con un solo clic y su perfil se carga automáticamente.

## 🚀 Pasos para Configurar Google OAuth

### Paso 1: Crear Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.developers.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita las siguientes APIs:
   - **Gmail API** (para envío de correos)
   - **Google+ API** (para información del perfil)

### Paso 2: Crear Credenciales OAuth 2.0

1. En el menú lateral, ve a **"APIs y servicios" > "Credenciales"**
2. Haz clic en **"Crear credenciales" > "ID de cliente de OAuth 2.0"**
3. Selecciona **"Aplicación web"** como tipo de aplicación
4. Agrega las URLs autorizadas:
   - **Orígenes autorizados de JavaScript:** `https://tudominio.com`
   - **URI de redirección autorizados:** `https://tudominio.com/google_auth.php`

### Paso 3: Obtener Credenciales

Después de crear las credenciales, obtendrás:
- **ID de cliente** (Client ID)
- **Secreto del cliente** (Client Secret)

### Paso 4: Configurar Archivos

#### Editar `gmail_config.php`:
```php
<?php
define('GMAIL_CLIENT_ID', 'TU_CLIENT_ID_REAL.apps.googleusercontent.com');
define('GMAIL_CLIENT_SECRET', 'TU_CLIENT_SECRET_REAL');
define('GMAIL_REDIRECT_URI', 'https://tudominio.com/google_auth.php');
define('GMAIL_SCOPES', 'https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');
?>
```

#### Editar `index.html`:
```javascript
const GOOGLE_CLIENT_ID = 'TU_CLIENT_ID_REAL.apps.googleusercontent.com';
```

## 🧪 Probar el Sistema

### 1. Prueba de Google OAuth
- Accede al sitio principal
- Haz clic en **"Continuar con Google"**
- Selecciona tu cuenta de Gmail
- Verifica que se cree/actualice tu perfil

### 2. Prueba del Sistema Completo
- Accede a: `https://tudominio.com/verificar_sistema.php`
- Verifica que todas las funciones estén operativas

## 🔧 Funcionamiento del Sistema

### Flujo de Autenticación:
1. **Usuario hace clic en "Continuar con Google"**
2. **Google abre ventana de autenticación**
3. **Usuario selecciona su cuenta**
4. **Sistema obtiene información del perfil**
5. **Se crea/actualiza usuario en la base de datos**
6. **Se inicia sesión automáticamente**
7. **Se muestra perfil con foto y nombre**

### Características:
- ✅ **Registro automático** - No necesitas crear cuenta
- ✅ **Login instantáneo** - Un solo clic para acceder
- ✅ **Perfil automático** - Foto y nombre se cargan solos
- ✅ **Sesión persistente** - Mantiene login entre visitas
- ✅ **Integración VIP** - Funciona con el sistema de suscripciones

## 🎯 Ventajas de Google OAuth

- **🔒 Seguro:** Autenticación OAuth2 profesional
- **⚡ Rápido:** Acceso con un solo clic
- **👤 Personalizado:** Perfil automático con foto
- **🌐 Universal:** Funciona en cualquier dispositivo
- **📱 Responsivo:** Interfaz adaptada a móviles
- **🔄 Sincronizado:** Datos siempre actualizados

## 📊 Estructura de la Base de Datos

### Tabla `users` actualizada:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NULL, -- NULL para usuarios de Google
    google_id VARCHAR(100) UNIQUE NULL, -- ID único de Google
    google_name VARCHAR(100) NULL, -- Nombre de Google
    google_picture VARCHAR(500) NULL, -- URL de la foto de perfil
    is_vip BOOLEAN DEFAULT FALSE,
    vip_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);
```

## 🚨 Solución de Problemas

### Error: "CLIENT_ID no configurado"
- Verifica que hayas reemplazado `PON_AQUI_TU_CLIENT_ID.apps.googleusercontent.com`
- Confirma que el archivo `gmail_config.php` esté configurado

### Error: "Google API no disponible"
- Verifica que hayas habilitado las APIs necesarias
- Confirma que las credenciales OAuth2 estén creadas correctamente

### Error: "URL no autorizada"
- Verifica que tu dominio esté en las URLs autorizadas
- Asegúrate de usar HTTPS si tu sitio lo requiere

### Error: "No se pudo obtener información de Google"
- Verifica que los scopes estén configurados correctamente
- Confirma que la API de Google+ esté habilitada

## 📱 Características del Perfil

### Información que se carga automáticamente:
- **👤 Nombre completo** del usuario
- **📧 Email** de Gmail
- **🖼️ Foto de perfil** de Google
- **🆔 ID único** de Google
- **📅 Fecha de registro** automática
- **🕒 Último acceso** actualizado

### Personalización disponible:
- **🎨 Foto de perfil** personalizada
- **👑 Estado VIP** visible
- **📊 Historial** de accesos
- **⚙️ Configuración** de cuenta

## 🔄 Flujo de Usuario

### Primera vez:
1. Usuario hace clic en "Continuar con Google"
2. Se abre ventana de Google
3. Usuario autoriza la aplicación
4. Se crea cuenta automáticamente
5. Se inicia sesión
6. Se muestra perfil completo

### Visitas posteriores:
1. Usuario hace clic en "Continuar con Google"
2. Google recuerda la autorización
3. Se inicia sesión instantáneamente
4. Se carga perfil y estado VIP

## 📞 Soporte

Si tienes problemas con la configuración:
1. **Revisa este archivo** paso a paso
2. **Verifica `verificar_sistema.php`** para diagnóstico completo
3. **Revisa la consola del navegador** para errores JavaScript
4. **Contacta soporte** si persisten los problemas

---

**🎌 ¡Con Google OAuth, tu sistema de autenticación será súper profesional y fácil de usar! 🎌** 