<!-- Theme Toggle Component -->
<div x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" 
x-init="
    if (darkMode) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
"
class="relative inline-block">
    <button @click="toggleTheme()" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            :title="darkMode ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
        <!-- Icono Sol (Modo Claro) -->
        <svg x-show="!darkMode" 
             class="w-5 h-5 text-yellow-500 transition-transform duration-300" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        
        <!-- Icono Luna (Modo Oscuro) -->
        <svg x-show="darkMode" 
             class="w-5 h-5 text-blue-400 transition-transform duration-300" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
            </path>
        </svg>
        
        <!-- Texto opcional -->
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline" x-text="darkMode ? 'Oscuro' : 'Claro'"></span>
    </button>
</div>
