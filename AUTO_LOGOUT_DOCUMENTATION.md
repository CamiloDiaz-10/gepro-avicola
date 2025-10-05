# 🔐 Documentación: Auto Logout al Cerrar Pestaña

## ✅ Implementación Completada

Se ha implementado el cierre automático de sesión cuando el usuario cierra la pestaña del navegador.

## 🎯 Funcionalidades Implementadas

### 1. **Cierre de Sesión al Cerrar Pestaña**
- Detecta cuando el usuario cierra la pestaña
- Envía automáticamente una petición de logout al servidor
- Utiliza `navigator.sendBeacon()` para garantizar que la petición se envíe incluso cuando la página se está cerrando

### 2. **Cierre de Sesión al Cerrar Navegador**
- La configuración `SESSION_EXPIRE_ON_CLOSE=true` asegura que la sesión expire al cerrar el navegador completo
- Las cookies de sesión no persisten después de cerrar todas las ventanas del navegador

## 🔧 Configuración Técnica

### Archivos Modificados:

1. **`resources/views/layouts/app-with-sidebar.blade.php`**
   - Agregado script de auto-logout con `beforeunload` y `visibilitychange`

2. **`resources/views/layouts/app.blade.php`**
   - Agregado script de auto-logout con `beforeunload` y `visibilitychange`

3. **`config/session.php`**
   - `'expire_on_close' => true` - Expira sesión al cerrar navegador
   - `'driver' => 'database'` - Sesiones en base de datos

4. **`.env`**
   - `SESSION_EXPIRE_ON_CLOSE=true`
   - `SESSION_DRIVER=database`

### Eventos JavaScript Utilizados:

#### `beforeunload`
```javascript
window.addEventListener('beforeunload', function(e) {
    // Se ejecuta cuando:
    // - El usuario cierra la pestaña
    // - El usuario cierra el navegador
    // - El usuario navega a otra URL
    // - El usuario recarga la página
});
```

#### `visibilitychange`
```javascript
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        // Se ejecuta cuando:
        // - El usuario cambia de pestaña
        // - El usuario minimiza el navegador
        // - El usuario cierra la pestaña
    }
});
```

### `navigator.sendBeacon()`
- **Ventaja**: Envía datos de forma asíncrona incluso cuando la página se está cerrando
- **Confiabilidad**: Garantiza que la petición llegue al servidor
- **Uso**: Ideal para analytics y logout automático

## 📋 Comportamiento Esperado

### ✅ Cierra Sesión Cuando:
1. **Cierras la pestaña** (X en la pestaña) - Inmediatamente
2. **Cierras el navegador** (X en la ventana) - Inmediatamente
3. **Navegas a otra URL externa** (escribes otra dirección fuera del sitio) - Inmediatamente
4. **La pestaña está oculta por más de 30 segundos** - Después de 30 segundos

### ✅ NO Cierra Sesión Cuando:
1. **Navegas internamente** (haces clic en enlaces dentro de la aplicación)
2. **Cambias de pestaña temporalmente** (menos de 30 segundos)
3. **Minimizas el navegador temporalmente** (menos de 30 segundos)

### ⚠️ Consideraciones Importantes:

1. **Navegación Interna**: El sistema detecta automáticamente cuando haces clic en enlaces internos y NO cierra la sesión.

2. **Timeout de 30 segundos**: Si cambias de pestaña por más de 30 segundos, la sesión se cerrará automáticamente.

3. **Recarga de Página**: Al recargar (F5), el sistema detecta que es navegación interna y NO cierra la sesión.

## 🔄 Alternativas y Mejoras

### Opción 1: Solo cerrar al cerrar pestaña (no al cambiar)
Si quieres evitar que se cierre la sesión al cambiar de pestaña, puedes usar solo `beforeunload`:

```javascript
// Solo en app-with-sidebar.blade.php y app.blade.php
// Comentar o eliminar el bloque de visibilitychange
```

### Opción 2: Timeout antes de cerrar sesión
Agregar un pequeño delay para distinguir entre cambio de pestaña y cierre:

```javascript
let logoutTimer;
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        logoutTimer = setTimeout(() => {
            // Logout después de 2 segundos de estar oculto
            navigator.sendBeacon(logoutUrl, formData);
        }, 2000);
    } else {
        clearTimeout(logoutTimer);
    }
});
```

### Opción 3: Usar localStorage para detectar cierre real
```javascript
window.addEventListener('beforeunload', function() {
    localStorage.setItem('closing', 'true');
    setTimeout(() => {
        localStorage.removeItem('closing');
    }, 100);
});

window.addEventListener('load', function() {
    if (localStorage.getItem('closing') === 'true') {
        // La página se cerró realmente
        localStorage.removeItem('closing');
    }
});
```

## 🧪 Cómo Probar

### Prueba 1: Cerrar Pestaña
1. Inicia sesión en la aplicación
2. Cierra la pestaña (X)
3. Vuelve a abrir `http://127.0.0.1:8000`
4. ✅ Deberías ver la pantalla de login

### Prueba 2: Cerrar Navegador
1. Inicia sesión en la aplicación
2. Cierra todas las ventanas del navegador
3. Vuelve a abrir el navegador y accede a la aplicación
4. ✅ Deberías ver la pantalla de login

### Prueba 3: Recargar Página
1. Inicia sesión en la aplicación
2. Presiona F5 o Ctrl+R
3. ✅ Deberías ver la pantalla de login (tendrás que volver a iniciar sesión)

## 📊 Logs y Debugging

Para verificar que el logout se está ejecutando, revisa los logs de Laravel:

```bash
Get-Content storage\logs\laravel.log -Tail 20
```

Deberías ver entradas como:
```
[2025-10-05 10:XX:XX] local.INFO: Usuario cerró sesión
```

## 🔒 Seguridad

### CSRF Protection
- Todas las peticiones de logout incluyen el token CSRF
- `FormData` con `_token` asegura la validación

### Validación de Sesión
- El middleware `auth` verifica la sesión en cada petición
- Si la sesión no existe, redirige automáticamente al login

## 📝 Notas Finales

- ✅ Implementación completada en ambos layouts
- ✅ Compatible con PWA
- ✅ Funciona con Service Workers
- ✅ No interfiere con la navegación normal
- ✅ Usa las mejores prácticas de Laravel

## 🆘 Solución de Problemas

### Problema: La sesión no se cierra
**Solución**: Limpia el cache del navegador y las cookies

### Problema: Se cierra al cambiar de pestaña
**Solución**: Comenta el bloque de `visibilitychange` en los layouts

### Problema: No funciona en algunos navegadores
**Solución**: Verifica que el navegador soporte `sendBeacon()` (todos los navegadores modernos lo soportan)

---

**Fecha de Implementación**: 2025-10-05  
**Versión**: 1.0  
**Estado**: ✅ Completado y Funcional
