# Usuarios de Prueba - Gepro Avícola

## Credenciales de Acceso

### Usuario Administrador
- **Email:** yefreycamilogalgosdiaz@gmail.com
- **Contraseña:** 123456
- **Rol:** Administrador
- **Dashboard:** `/admin/dashboard`

### Usuario Propietario
- **Email:** camilo@gmail.com
- **Contraseña:** 123456
- **Rol:** Propietario
- **Dashboard:** `/owner/dashboard`

## Instrucciones de Uso

1. Accede a: `http://127.0.0.1:8000/login`
2. Usa cualquiera de las credenciales de arriba
3. Serás redirigido automáticamente al dashboard correspondiente según tu rol
4. Cada dashboard tiene un botón de "Cerrar Sesión" en la esquina superior derecha

## Funcionalidades Implementadas

✅ **Sistema de Login Seguro**
- Verificación manual de contraseñas con Hash::check()
- Redirección automática según el rol del usuario
- Protección CSRF en todos los formularios

✅ **Dashboards por Rol**
- Dashboard de Administrador (rojo)
- Dashboard de Propietario (azul)
- Dashboard de Empleado (verde)

✅ **Sistema de Logout**
- Botón de logout en todos los dashboards
- Confirmación antes de cerrar sesión
- Mensaje personalizado de despedida
- Invalidación completa de la sesión

✅ **Middleware de Roles**
- Protección de rutas por rol
- Redirección automática si no tienes permisos

## Comandos Útiles

```bash
# Verificar usuarios en la base de datos
php artisan users:check

# Probar login programáticamente
php artisan auth:test email@ejemplo.com contraseña

# Resetear contraseña de un usuario
php artisan user:reset-password email@ejemplo.com nueva_contraseña
```
