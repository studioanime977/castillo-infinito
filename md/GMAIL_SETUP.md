# 🧪 Configuración de Gmail API para StudioOtaku

## 📋 Descripción

Este sistema integra la API de Gmail para enviar correos de manera más confiable que la función `mail()` nativa de PHP. Esto solucionará el problema de envío de correos.

## 🚀 Pasos para Configurar Gmail API

### Paso 1: Crear Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.developers.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Gmail API** para tu proyecto

### Paso 2: Crear Credenciales OAuth 2.0

1. En el menú lateral, ve a **"APIs y servicios" > "Credenciales"**
2. Haz clic en **"Crear credenciales" > "ID de cliente de OAuth 2.0"**
3. Selecciona **"Aplicación web"** como tipo de aplicación
4. Agrega las URLs autorizadas:
   - **Orígenes autorizados de JavaScript:** `https://tudominio.com`
   - **URI de redirección autorizados:** `https://tudominio.com/gmail_callback.php`

### Paso 3: Obtener Credenciales

Después de crear las credenciales, obtendrás:
- **ID de cliente** (Client ID)
- **Secreto del cliente** (Client Secret)

### Paso 4: Configurar Archivos

#### Editar `gmail_config.php`:
```php
<?php
// Configuración de la API de Gmail
define('GMAIL_CLIENT_ID', 'TU_CLIENT_ID_REAL.apps.googleusercontent.com');
define('GMAIL_CLIENT_SECRET', 'TU_CLIENT_SECRET_REAL');
define('GMAIL_REDIRECT_URI', 'https://tudominio.com/gmail_callback.php');
define('GMAIL_SCOPES', 'https://www.googleapis.com/auth/gmail.send');
?>
```

#### Editar `gmail_test.html`:
```javascript
const CLIENT_ID = "TU_CLIENT_ID_REAL.apps.googleusercontent.com";
```

## 🧪 Probar el Sistema

### 1. Prueba Básica de Gmail API
Accede a: `https://tudominio.com/gmail_test.html`

### 2. Prueba del Sistema Completo
Accede a: `https://tudominio.com/verificar_sistema.php`

### 3. Prueba del Formulario de Pago
Usa el formulario VIP desde el sitio principal

## 🔧 Funcionamiento del Sistema

### Método 1: Gmail API (Recomendado)
- ✅ **Más confiable** que mail() nativa
- ✅ **Funciona en todos los hostings**
- ✅ **Envío instantáneo**
- ✅ **Soporte para archivos adjuntos**

### Método 2: Función mail() nativa
- ⚠️ **Fallback** si Gmail API no está configurado
- ⚠️ **Depende de la configuración del servidor**
- ⚠️ **Puede no funcionar en algunos hostings**

### Método 3: Base de datos + Notificación
- ✅ **Siempre funciona** como último recurso
- ✅ **Guarda todas las solicitudes**
- ✅ **Permite activación manual desde admin**

## 📧 Flujo de Correo

1. **Usuario sube comprobante** → Sistema intenta Gmail API
2. **Si Gmail API falla** → Sistema intenta mail() nativa
3. **Si mail() falla** → Sistema guarda en base de datos
4. **Administrador recibe notificación** → Activa VIP manualmente

## 🎯 Ventajas de Gmail API

- **Confiable:** 99.9% de entrega exitosa
- **Rápido:** Envío instantáneo
- **Seguro:** Autenticación OAuth2
- **Compatible:** Funciona en cualquier hosting
- **Profesional:** Correos llegan a la bandeja principal

## 🚨 Solución de Problemas

### Error: "CLIENT_ID no configurado"
- Verifica que hayas reemplazado `PON_AQUI_TU_CLIENT_ID.apps.googleusercontent.com`
- Asegúrate de que el archivo `gmail_config.php` esté configurado

### Error: "Gmail API no disponible"
- Verifica que hayas habilitado Gmail API en Google Cloud Console
- Confirma que las credenciales OAuth2 estén creadas correctamente

### Error: "URL no autorizada"
- Verifica que tu dominio esté en las URLs autorizadas
- Asegúrate de usar HTTPS si tu sitio lo requiere

## 📞 Soporte

Si tienes problemas con la configuración:
1. **Revisa este archivo** paso a paso
2. **Usa `gmail_test.html`** para probar la API
3. **Verifica `verificar_sistema.php`** para diagnóstico completo
4. **Contacta soporte** si persisten los problemas

---

**🎌 ¡Con Gmail API, tu sistema de correos será 100% confiable! 🎌** 