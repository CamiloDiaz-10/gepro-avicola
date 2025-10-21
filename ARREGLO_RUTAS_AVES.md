# Arreglo: Rutas de Aves para Propietarios y Empleados

## 🐛 Problema Original

**Error:** `Route [owner.aves.show.byqr] not defined`

**Causa:** Faltaban las rutas específicas para que Propietarios y Empleados pudieran ver los detalles de las aves por código QR.

---

## ✅ Solución Implementada

### 1. Rutas Agregadas en `routes/web.php`

#### **Para Propietarios (Owner):**
```php
Route::middleware(['role:Propietario'])->prefix('owner')->name('owner.')->group(function () {
    // ... otras rutas ...
    
    // Aves (Propietario)
    Route::get('aves', [BirdsController::class, 'index'])->name('aves.index');
    Route::get('aves/create', [BirdsController::class, 'create'])->name('aves.create');
    Route::post('aves', [BirdsController::class, 'store'])->name('aves.store');
    Route::get('aves/qr/{token}', [BirdsController::class, 'showByQr'])->name('aves.show.byqr'); // ⭐ NUEVA
    Route::patch('aves/{bird}/estado', [BirdsController::class, 'updateEstado'])->name('aves.estado.update');
    Route::get('aves/export/csv', [BirdsController::class, 'exportCsv'])->name('aves.export.csv');
    Route::get('aves/scan', [BirdsController::class, 'scan'])->name('aves.scan');
    Route::delete('aves/{bird}', [BirdsController::class, 'destroy'])->name('aves.destroy');
});
```

#### **Para Empleados (Employee):**
```php
Route::middleware(['role:Empleado'])->prefix('employee')->name('employee.')->group(function () {
    // ... otras rutas ...
    
    // Aves (Empleado)
    Route::get('aves', [BirdsController::class, 'index'])->name('aves.index'); // ⭐ NUEVA
    Route::get('aves/qr/{token}', [BirdsController::class, 'showByQr'])->name('aves.show.byqr'); // ⭐ NUEVA
    Route::get('aves/export/csv', [BirdsController::class, 'exportCsv'])->name('aves.export.csv'); // ⭐ NUEVA
});
```

---

### 2. Seguridad Agregada en `BirdsController::showByQr()`

```php
public function showByQr(string $token)
{
    $bird = Bird::with(['lote', 'tipoGallina'])->where('qr_token', $token)->first();
    
    if (!$bird) {
        return view('admin.aves.qr-not-found', compact('token'));
    }

    // ⭐ NUEVO: Verificar acceso si el usuario está autenticado y no es admin
    $user = auth()->user();
    if ($user && !$user->hasRole('Administrador')) {
        // Verificar si tiene acceso al lote de esta ave
        if (!$user->hasAccessToLote($bird->IDLote)) {
            abort(403, 'No tienes permiso para ver esta ave. Pertenece a una finca no asignada.');
        }
    }
    
    return view('admin.aves.show-by-qr', compact('bird'));
}
```

**Protección implementada:**
- ✅ Propietarios solo ven aves de sus fincas asignadas
- ✅ Empleados solo ven aves de sus fincas asignadas
- ✅ Intento de ver ave no permitida → Error 403
- ✅ Administradores ven todas las aves sin restricción

---

### 3. Vista Actualizada: `index.blade.php`

**Detección de contexto mejorada:**
```php
@php
    $current = Route::currentRouteName();
    $area = 'admin'; // Default
    if (\Illuminate\Support\Str::startsWith($current, 'owner.')) {
        $area = 'owner';
    } elseif (\Illuminate\Support\Str::startsWith($current, 'employee.')) {
        $area = 'employee'; // ⭐ NUEVO
    } elseif (\Illuminate\Support\Str::startsWith($current, 'veterinario.')) {
        $area = 'veterinario';
    }
@endphp
```

**Rutas dinámicas en la vista:**
```blade
<a href="{{ route($area.'.aves.show.byqr', $b->qr_token) }}">
    Ver Detalles
</a>
```

Esto genera:
- Admin: `route('admin.aves.show.byqr', $token)`
- Owner: `route('owner.aves.show.byqr', $token)` ✅
- Employee: `route('employee.aves.show.byqr', $token)` ✅
- Veterinario: `route('veterinario.aves.show.byqr', $token)`

---

## 🎯 URLs Generadas

### Administrador
- Listado: `/admin/aves`
- Detalle: `/admin/aves/qr/{token}`

### Propietario (Ana López)
- Listado: `/owner/aves`
- Detalle: `/owner/aves/qr/{token}`
- Crear: `/owner/aves/create`
- Escanear: `/owner/aves/scan`

### Empleado
- Listado: `/employee/aves`
- Detalle: `/employee/aves/qr/{token}`
- Exportar: `/employee/aves/export/csv`

---

## 🔒 Control de Acceso

### Ana López (Propietaria)

**Puede ver aves de:**
- ✅ Avícola Los Pinos (Finca ID: 3)
- ✅ Finca La Esperanza (Finca ID: 4)

**NO puede ver aves de:**
- ❌ Finca El Paraíso (Finca ID: 1)
- ❌ Granja San José (Finca ID: 2)
- ❌ Granja Santa María (Finca ID: 5)

**Comportamiento:**
1. Lista de aves en `/owner/aves` → Solo muestra aves de Fincas 3 y 4
2. Click en "Ver Detalles" → Redirige a `/owner/aves/qr/{token}`
3. Si intenta ver ave de otra finca → Error 403

---

## 🧪 Pruebas

### Test 1: Login como Ana
```bash
1. Login: ana.lopez@geproavicola.com / ana123
2. Ir a: /owner/aves
3. Debe ver solo aves de Fincas 3 y 4
4. Click en "Ver Detalles" → Funciona ✅
```

### Test 2: Verificar Permisos
```bash
1. Obtener token de ave de Finca 1 (no asignada a Ana)
2. Intentar acceder: /owner/aves/qr/{token}
3. Resultado esperado: Error 403 ✅
```

### Test 3: Login como Empleado
```bash
1. Login: empleado@geproavicola.com / empleado123
2. Ir a: /employee/aves
3. Debe ver solo aves de Finca 1
4. Click en "Ver Detalles" → Funciona ✅
```

---

## 📝 Comandos para Aplicar

### Limpiar Cache
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Verificar Rutas
```bash
php artisan route:list | grep "aves.show.byqr"
```

**Salida esperada:**
```
GET|HEAD  admin/aves/qr/{token}      admin.aves.show.byqr
GET|HEAD  owner/aves/qr/{token}      owner.aves.show.byqr
GET|HEAD  employee/aves/qr/{token}   employee.aves.show.byqr
GET|HEAD  veterinario/aves/qr/{token} veterinario.aves.show.byqr
```

---

## ✅ Resultado Final

### Antes (❌)
- Error: `Route [owner.aves.show.byqr] not defined`
- Propietarios no podían ver detalles de aves
- Sin control de acceso por finca

### Después (✅)
- Ruta `owner.aves.show.byqr` definida
- Ruta `employee.aves.show.byqr` definida
- Control de acceso por finca implementado
- Propietarios/Empleados solo ven sus aves
- Mensaje claro en caso de acceso denegado

---

## 🔐 Seguridad Implementada

1. **Filtrado en listado:** Solo muestra aves de fincas asignadas
2. **Verificación en detalle:** Valida acceso al lote antes de mostrar
3. **Error descriptivo:** "No tienes permiso para ver esta ave. Pertenece a una finca no asignada."
4. **Consistent con sistema:** Usa `hasAccessToLote()` del User model

---

**Estado:** ✅ COMPLETAMENTE ARREGLADO Y SEGURO
**Última actualización:** 2025-10-20
