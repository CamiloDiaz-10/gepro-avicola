# 🌓 Sistema de Modo Oscuro/Claro - GeproAvicola

## 📋 Descripción General

Se ha implementado un sistema completo de temas (modo oscuro/claro) para todo el proyecto GeproAvicola. Los usuarios pueden cambiar entre modos según su preferencia y la configuración se guarda automáticamente.

## 🎨 Características Implementadas

### ✅ Funcionalidades Principales

1. **Toggle de Tema**
   - Botón visual con iconos (sol/luna)
   - Ubicado en el navbar para usuarios autenticados
   - Disponible en login y registro para usuarios no autenticados
   - Transiciones suaves entre modos

2. **Persistencia**
   - Guarda la preferencia del usuario en `localStorage`
   - Respeta las preferencias del sistema operativo si no hay configuración guardada
   - Se mantiene entre sesiones

3. **Aplicación Automática**
   - Se carga antes del contenido para evitar "flash" visual
   - Funciona en todas las páginas del proyecto
   - Transiciones suaves de 200-300ms

4. **Componentes Adaptados**
   - Navbar con fondo oscuro
   - Sidebar con colores adaptados
   - Formularios (login/registro) con inputs oscuros
   - Fondos de página
   - Textos y bordes

## 📁 Archivos Creados/Modificados

### Archivos Nuevos

1. **`resources/views/components/theme-toggle.blade.php`**
   - Componente reutilizable del toggle
   - Usa Alpine.js para la interactividad
   - Iconos SVG animados

2. **`public/js/theme-manager.js`**
   - Script global para gestión del tema
   - Funciones: `toggleTheme()`, `getCurrentTheme()`
   - Inicialización automática

3. **`tailwind.config.js`**
   - Configuración de Tailwind CSS
   - Habilita `darkMode: 'class'`
   - Colores personalizados del proyecto

### Archivos Modificados

1. **`resources/views/layouts/app-with-sidebar.blade.php`**
   - Carga del script de tema
   - Configuración de Tailwind inline
   - Clases dark en body

2. **`resources/views/components/navbar.blade.php`**
   - Clases dark agregadas
   - Inclusión del componente theme-toggle
   - Colores adaptados

3. **`resources/views/layouts/sidebar.blade.php`**
   - Fondo oscuro alternativo
   - Bordes adaptados

4. **`resources/views/auth/login.blade.php`**
   - Toggle de tema incluido
   - Estilos dark para inputs
   - Labels y textos adaptados

## 🎯 Cómo Usar

### Para Usuarios

1. **Cambiar el Tema:**
   - Click en el botón sol/luna en el navbar (usuarios autenticados)
   - Click en el botón en la esquina superior del formulario (login/registro)

2. **El tema se guarda automáticamente** y se aplicará en todas las páginas

### Para Desarrolladores

#### 1. Usar el Toggle en Cualquier Vista

```blade
<x-theme-toggle />
```

#### 2. Agregar Clases Dark a Nuevos Componentes

```html
<!-- Ejemplo de elemento con modo oscuro -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Contenido
</div>
```

#### 3. Clases Comunes de Modo Oscuro

**Fondos:**
- `bg-white dark:bg-gray-800`
- `bg-gray-50 dark:bg-gray-900`
- `bg-gray-100 dark:bg-gray-700`

**Textos:**
- `text-gray-900 dark:text-white`
- `text-gray-700 dark:text-gray-200`
- `text-gray-600 dark:text-gray-300`

**Bordes:**
- `border-gray-200 dark:border-gray-700`
- `border-gray-300 dark:border-gray-600`

**Inputs:**
```css
.dark input {
    background-color: #374151;
    border-color: #4b5563;
    color: white;
}
```

#### 4. Funciones JavaScript Disponibles

```javascript
// Cambiar el tema manualmente
window.toggleTheme();

// Obtener el tema actual
const currentTheme = window.getCurrentTheme(); // 'dark' o 'light'
```

## 🔧 Configuración Técnica

### Tailwind CSS

El modo oscuro está configurado para usar la estrategia de **clase** (`class`):

```javascript
// tailwind.config.js
module.exports = {
  darkMode: 'class',
  // ...
}
```

Esto significa que el modo oscuro se activa agregando la clase `dark` al elemento `<html>`.

### LocalStorage

La preferencia se guarda en:
```javascript
localStorage.setItem('theme', 'dark'); // o 'light'
```

### Detección de Preferencias del Sistema

Si no hay configuración guardada, el sistema detecta la preferencia del SO:

```javascript
window.matchMedia('(prefers-color-scheme: dark)').matches
```

## 🎨 Paleta de Colores

### Modo Claro
- **Fondo principal**: `bg-gray-50`
- **Fondo componentes**: `bg-white`
- **Texto principal**: `text-gray-900`
- **Texto secundario**: `text-gray-600`
- **Bordes**: `border-gray-200`

### Modo Oscuro
- **Fondo principal**: `bg-gray-900`
- **Fondo componentes**: `bg-gray-800`
- **Texto principal**: `text-white`
- **Texto secundario**: `text-gray-300`
- **Bordes**: `border-gray-700`

### Color de Acento (Ambos Modos)
- **Primary**: `#10b981` (verde)
- **Primary Hover**: `#059669`

## 📱 Responsive

El toggle de tema es completamente responsive:
- **Móvil**: Solo icono
- **Desktop**: Icono + texto "Oscuro"/"Claro"

## ⚡ Rendimiento

- **Script cargado primero**: Evita flash de contenido
- **Transiciones optimizadas**: 200-300ms
- **LocalStorage**: Acceso instantáneo
- **Sin dependencias pesadas**: Solo Alpine.js

## 🐛 Solución de Problemas

### El tema no se guarda
- Verificar que localStorage esté habilitado en el navegador
- Revisar la consola del navegador por errores

### Flash de contenido al cargar
- Asegurarse de que `theme-manager.js` se carga ANTES de Tailwind CSS
- Verificar que el script esté en el `<head>`

### Clases dark no funcionan
- Verificar que Tailwind esté configurado con `darkMode: 'class'`
- Asegurarse de que la clase `dark` esté en el elemento `<html>`

## 🚀 Próximas Mejoras Sugeridas

1. **Más temas**: Agregar temas personalizados (azul, verde, morado)
2. **Transiciones avanzadas**: Animaciones más elaboradas al cambiar
3. **Preferencias por módulo**: Diferentes temas para diferentes secciones
4. **Modo automático**: Cambiar según la hora del día
5. **Accesibilidad**: Mejorar contraste y legibilidad

## 📞 Soporte

Para problemas o sugerencias sobre el sistema de temas, consultar este documento o revisar los archivos mencionados.

---

**Versión**: 1.0.0  
**Última actualización**: Octubre 2025  
**Desarrollado para**: GeproAvicola
