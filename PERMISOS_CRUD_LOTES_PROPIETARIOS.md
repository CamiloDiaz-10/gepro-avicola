# Permisos CRUD Completos de Lotes para Propietarios

## ✅ Funcionalidad Implementada

Los **Propietarios** ahora tienen **permisos completos CRUD** (Crear, Leer, Actualizar, Eliminar) sobre los lotes de sus fincas asignadas.

---

## 🔐 Matriz de Permisos

| Acción | Administrador | Propietario | Empleado |
|--------|---------------|-------------|----------|
| **Ver listado de lotes** | ✅ Todos | ✅ Solo sus fincas | ✅ Solo sus fincas |
| **Ver detalles de lote** | ✅ Todos | ✅ Solo sus fincas | ✅ Solo sus fincas |
| **Crear lote nuevo** | ✅ Cualquier finca | ✅ Solo en sus fincas | ❌ No permitido |
| **Editar lote** | ✅ Cualquier lote | ✅ Solo de sus fincas | ❌ No permitido |
| **Eliminar lote** | ✅ Cualquier lote | ✅ Solo de sus fincas | ❌ No permitido |

---

## 🛠️ Cambios Implementados

### 1. Rutas Completas (`routes/web.php`)

#### **Propietarios - Rutas CRUD Completas**
```php
Route::middleware(['role:Propietario'])->prefix('owner')->name('owner.')->group(function () {
    // Lotes (Propietario) - CRUD completo
    Route::get('lotes', [LoteController::class, 'index'])->name('lotes.index');              // Listar
    Route::get('lotes/create', [LoteController::class, 'create'])->name('lotes.create');     // Formulario crear
    Route::post('lotes', [LoteController::class, 'store'])->name('lotes.store');             // Guardar nuevo
    Route::get('lotes/{lote}', [LoteController::class, 'show'])->name('lotes.show');         // Ver detalles
    Route::get('lotes/{lote}/edit', [LoteController::class, 'edit'])->name('lotes.edit');    // Formulario editar
    Route::put('lotes/{lote}', [LoteController::class, 'update'])->name('lotes.update');     // Actualizar
    Route::delete('lotes/{lote}', [LoteController::class, 'destroy'])->name('lotes.destroy'); // Eliminar
});
```

**URLs Generadas:**
- `GET /owner/lotes` → Listado
- `GET /owner/lotes/create` → Crear nuevo
- `POST /owner/lotes` → Guardar
- `GET /owner/lotes/{id}` → Ver detalles
- `GET /owner/lotes/{id}/edit` → Editar
- `PUT /owner/lotes/{id}` → Actualizar
- `DELETE /owner/lotes/{id}` → Eliminar

#### **Empleados - Solo Lectura**
```php
Route::middleware(['role:Empleado'])->prefix('employee')->name('employee.')->group(function () {
    // Lotes (Empleado) - Solo lectura
    Route::get('lotes', [LoteController::class, 'index'])->name('lotes.index');
    Route::get('lotes/{lote}', [LoteController::class, 'show'])->name('lotes.show');
});
```

---

### 2. Sidebar Actualizado (`sidebar.blade.php`)

#### **Propietarios:**
```blade
<!-- Mis Fincas -->
<li>
    <a href="{{ route('owner.lotes.index') }}">
        <i class="fas fa-layer-group"></i>
        <span>Gestionar Lotes</span>
    </a>
</li>
<li>
    <a href="{{ route('owner.lotes.create') }}">  ⭐ NUEVO
        <i class="fas fa-plus-circle"></i>
        <span>Crear Lote</span>
    </a>
</li>
```

**Sidebar visual:**
```
Mis Fincas
├── Gestionar Lotes
└── Crear Lote  ⭐ NUEVO
```

#### **Empleados:**
```blade
<!-- Mis Fincas -->
<li>
    <a href="{{ route('employee.lotes.index') }}">
        <i class="fas fa-layer-group"></i>
        <span>Mis Lotes</span>
    </a>
</li>
<!-- No tiene botón "Crear" -->
```

---

### 3. Vista Actualizada (`index.blade.php`)

#### **Detección Automática de Contexto:**
```php
@php
    $current = Route::currentRouteName();
    $area = 'admin'; // Default
    if (\Illuminate\Support\Str::startsWith($current, 'owner.')) {
        $area = 'owner';
    } elseif (\Illuminate\Support\Str::startsWith($current, 'employee.')) {
        $area = 'employee';
    }
@endphp
```

#### **Rutas Dinámicas:**
```blade
<!-- Botón Crear (solo admin y owner) -->
@if($area !== 'employee')
<a href="{{ route($area.'.lotes.create') }}">Nuevo Lote</a>
@endif

<!-- Formulario de filtros -->
<form action="{{ route($area.'.lotes.index') }}">
    ...
</form>

<!-- Acciones en tabla -->
<a href="{{ route($area.'.lotes.show', $lote) }}">Ver</a>

@if($area !== 'employee')
<a href="{{ route($area.'.lotes.edit', $lote) }}">Editar</a>
<form action="{{ route($area.'.lotes.destroy', $lote) }}">
    <button>Eliminar</button>
</form>
@endif
```

---

## 🔒 Seguridad Implementada

### Nivel 1: Rutas con Middleware
```php
Route::middleware(['role:Propietario']) // Solo propietarios
```

### Nivel 2: Filtrado Automático en Controlador
El `LoteController` ya tiene implementado `FiltroFincasHelper`:

```php
use FiltroFincasHelper;

public function index() {
    $query = Lote::with('finca');
    $query = $this->aplicarFiltroFincas($query); // Filtra automáticamente
    ...
}

public function create() {
    $fincas = $this->getFincasAccesibles(); // Solo fincas del usuario
    ...
}

public function store(Request $request) {
    if (!$this->verificarAccesoFinca($validated['IDFinca'])) {
        abort(403, 'No tienes permiso.');
    }
    ...
}
```

### Nivel 3: Verificación en Edición/Eliminación
```php
public function edit(Lote $lote) {
    if (!$this->verificarAccesoLote($lote->IDLote)) {
        abort(403, 'No tienes permiso para editar este lote.');
    }
    ...
}

public function destroy(Lote $lote) {
    if (!$this->verificarAccesoLote($lote->IDLote)) {
        abort(403, 'No tienes permiso para eliminar este lote.');
    }
    ...
}
```

---

## 📊 Ejemplo Práctico

### Ana López (Propietaria - Fincas 3 y 4)

#### **1. Ver Lotes**
- URL: `/owner/lotes`
- Ve: Solo lotes de Avícola Los Pinos (3) y Finca La Esperanza (4)
- Acciones: Ver, Editar, Eliminar

#### **2. Crear Lote Nuevo**
- Click en "Crear Lote" en sidebar
- URL: `/owner/lotes/create`
- Dropdown de fincas: Solo muestra Fincas 3 y 4
- Al guardar: Verifica que la finca sea 3 o 4

#### **3. Editar Lote**
- Click en "Editar" de un lote
- URL: `/owner/lotes/{id}/edit`
- Puede modificar datos
- Solo puede asignar a Fincas 3 o 4

#### **4. Eliminar Lote**
- Click en "Eliminar"
- Confirmación: "¿Eliminar este lote?"
- Verifica permisos antes de eliminar

#### **5. Intentos No Permitidos**
- Intentar ver lote de Finca 1 → Error 403
- Intentar editar lote de Finca 2 → Error 403
- Intentar crear lote en Finca 5 → Error 403

---

### Empleado (Solo Lectura)

#### **Puede:**
- ✅ Ver listado de lotes de su finca
- ✅ Ver detalles de cada lote

#### **NO Puede:**
- ❌ Crear lotes (botón oculto)
- ❌ Editar lotes (botón oculto)
- ❌ Eliminar lotes (botón oculto)

---

## 🎯 Flujos de Trabajo

### Flujo 1: Propietario Crea Lote
```
1. Login como propietario
2. Sidebar → Click "Crear Lote"
3. Formulario muestra solo fincas 3 y 4
4. Llena datos del lote
5. Selecciona finca (solo 3 o 4 disponibles)
6. Click "Guardar"
7. Verificación de permisos
8. Lote creado ✅
9. Redirige a /owner/lotes
```

### Flujo 2: Propietario Edita Lote
```
1. En /owner/lotes
2. Click "Editar" en un lote
3. Verificación: ¿Es de sus fincas? ✅
4. Muestra formulario
5. Modifica datos
6. Click "Actualizar"
7. Verificación de permisos
8. Lote actualizado ✅
```

### Flujo 3: Propietario Intenta Editar Lote No Permitido
```
1. Intenta acceder: /owner/lotes/123/edit (Finca 1)
2. Verificación: ¿Es de sus fincas? ❌
3. Error 403: "No tienes permiso"
```

### Flujo 4: Empleado Ve Lotes
```
1. Login como empleado
2. Sidebar → Click "Mis Lotes"
3. Ve listado (solo lectura)
4. Click "Ver" para detalles
5. NO ve botones Editar/Eliminar
```

---

## 🧪 Pruebas

### Test 1: Propietario Crea Lote
```
1. Login: ana.lopez@geproavicola.com / ana123
2. Sidebar → "Crear Lote"
3. Verificar que dropdown solo muestra Fincas 3 y 4
4. Crear lote en Finca 3
5. Verificar que aparece en listado
```

### Test 2: Propietario Edita Lote
```
1. Como Ana en /owner/lotes
2. Click "Editar" en lote de Finca 3
3. Cambiar nombre del lote
4. Guardar
5. Verificar cambios
```

### Test 3: Propietario NO Puede Editar Lote Ajeno
```
1. Como Ana, obtener ID de lote de Finca 1
2. Intentar: /owner/lotes/123/edit
3. Verificar Error 403
```

### Test 4: Empleado Solo Ve (No Edita)
```
1. Login: empleado@geproavicola.com / empleado123
2. Ir a /employee/lotes
3. Verificar que NO hay botón "Crear Lote"
4. Verificar que NO hay botones "Editar" ni "Eliminar"
5. Solo botón "Ver" disponible
```

### Test 5: Propietario Elimina Lote
```
1. Como Ana en /owner/lotes
2. Click "Eliminar" en lote de Finca 3
3. Confirmar eliminación
4. Verificar que desapareció del listado
```

---

## 📋 Archivos Modificados

### Rutas:
- `routes/web.php`
  - Líneas 178-185: CRUD completo para propietarios
  - Líneas 209-211: Solo lectura para empleados

### Sidebar:
- `resources/views/layouts/sidebar.blade.php`
  - Líneas 234-257: Sección propietarios con "Crear Lote"
  - Líneas 339-350: Sección empleados sin "Crear"

### Vistas:
- `resources/views/admin/lotes/index.blade.php`
  - Líneas 4-11: Detección de contexto
  - Línea 37-44: Botón crear condicional
  - Líneas 47: Form action dinámico
  - Líneas 68: Botón limpiar dinámico
  - Líneas 102-110: Acciones dinámicas en tabla

### Controlador (ya existente, no modificado):
- `app/Http/Controllers/Admin/LoteController.php`
  - Ya tiene `FiltroFincasHelper`
  - Ya tiene verificaciones de acceso en todos los métodos

---

## ✅ Ventajas

1. **Control granular:** Propietarios solo gestionan sus lotes
2. **Seguridad multicapa:** Middleware + Filtrado + Verificación
3. **Flexibilidad:** Mismo controlador para admin, owner, employee
4. **UI clara:** Botones ocultos según permisos
5. **Rutas limpias:** URLs descriptivas por rol
6. **Mantenibilidad:** No duplicación de código

---

## 🚀 Funcionalidades Disponibles

### Para Propietarios:
✅ Ver todos los lotes de sus fincas
✅ Crear nuevos lotes en sus fincas
✅ Editar lotes de sus fincas
✅ Eliminar lotes de sus fincas
✅ Filtrar y buscar lotes
✅ Ver estadísticas por lote

### Para Empleados:
✅ Ver lotes de sus fincas (solo lectura)
✅ Ver detalles de lotes
✅ Filtrar y buscar lotes
❌ NO crear, editar ni eliminar

### Para Administradores:
✅ Acceso completo sin restricciones
✅ Gestionar lotes de todas las fincas

---

**Estado:** ✅ COMPLETAMENTE FUNCIONAL Y SEGURO
**Última actualización:** 2025-10-20
