/**
 * Theme Manager - Gestión de modo oscuro/claro
 * GeproAvicola
 */

(function() {
    'use strict';

    // Inicializar tema al cargar la página
    function initTheme() {
        const theme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (theme === 'dark' || (!theme && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Ejecutar inmediatamente para evitar flash
    initTheme();

    // Escuchar cambios en las preferencias del sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });

    // Función global para cambiar el tema
    window.toggleTheme = function() {
        const isDark = document.documentElement.classList.contains('dark');
        
        if (isDark) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    };

    // Función para obtener el tema actual
    window.getCurrentTheme = function() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    };

})();
