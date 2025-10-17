/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class', // Habilitar modo oscuro con clase 'dark'
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Colores personalizados para GeproAvicola
        primary: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
