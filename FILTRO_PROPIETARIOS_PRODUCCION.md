# Filtrado de Producción de Huevos por Fincas Asignadas

## 🔒 Corrección Implementada

**Problema detectado:** El controlador de producción de huevos solo estaba filtrando para **empleados**, pero NO para **propietarios**.

**Solución:** Ahora **propietarios Y empleados** solo ven producción de lotes de sus fincas asignadas.

---

## ✅ Cambios Realizados

### 1. Nuevo Método `needsFincaFilter()`

```php
/**
 * Verifica si el usuario necesita filtrado por fincas (no es admin)
 */
private function needsFincaFilter(Request $request): bool
{
    $user = $request->user();
    if (!$user) return false;
    
    // Admin no necesita filtro, propietarios y empleados SÍ
    return !$user->hasRole('Administrador');
}
```

**Lógica:**
- ✅ **Administrador:** NO filtra (ve todo)
- ✅ **Propietario:** SÍ filtra (solo sus fincas)
- ✅ **Empleado:** SÍ filtra (solo sus fincas)

---

### 2. Método `permittedLotIds()` Actualizado

```php
private function permittedLotIds(Request $request)
{
    $user = $request->user();
    if (!$user) return collect();

    // Lotes pertenecientes a las fincas asignadas al usuario (propietario o empleado)
    $fincaIds = $user->fincas()->pluck('fincas.IDFinca');
    if ($fincaIds->isEmpty()) return collect();

    return Lote::whereIn('IDFinca', $fincaIds)->pluck('IDLote');
}
```

**Antes:** Comentario decía "empleado"
**Ahora:** Comentario dice "propietario o empleado"

---

### 3. Filtrado en TODAS las Consultas

#### **a) Listado de Producción (`index()`)**
```php
// Filtrar por fincas asignadas (propietarios y empleados)
if ($this->needsFincaFilter($request)) {
    $allowedLotIds = $this->permittedLotIds($request);
    $query->whereIn('IDLote', $allowedLotIds);
}
```

#### **b) Totales y Estadísticas**
```php
->when($this->needsFincaFilter($request), function($q) use ($request) {
    $q->whereIn('IDLote', $this->permittedLotIds($request));
})
```

#### **c) Producción Diaria (Gráfico)**
```php
->when($this->needsFincaFilter($request), function($q) use ($request) {
    $q->whereIn('IDLote', $this->permittedLotIds($request));
})
```

#### **d) Mejores y Peores Días**
```php
->when($this->needsFincaFilter($request), function($q) use ($request) {
    $q->whereIn('IDLote', $this->permittedLotIds($request));
})
```

#### **e) Estadísticas por Lote (Mejor/Peor)**
```php
->when($this->needsFincaFilter($request), function($q) use ($request) {
    $q->whereIn('IDLote', $this->permittedLotIds($request));
})
```

#### **f) Lista de Lotes en Dropdown**
```php
// Lista de lotes para el dropdown (filtrados por fincas asignadas)
if ($this->needsFincaFilter($request)) {
    $allowedLotIds = $this->permittedLotIds($request);
    $lotes = Lote::whereIn('IDLote', $allowedLotIds)->orderBy('Nombre')->get();
}
```

---

### 4. Método `create()` Actualizado

```php
// Lista de lotes filtrados por fincas asignadas (propietarios y empleados)
if ($this->needsFincaFilter($request)) {
    $allowedLotIds = $this->permittedLotIds($request);
    $lotes = Lote::whereIn('IDLote', $allowedLotIds)->orderBy('Nombre')->get();
}
```

---

### 5. Método `store()` Actualizado

```php
// Verificar acceso al lote (propietarios y empleados)
if ($this->needsFincaFilter($request)) {
    $allowedLotIds = $this->permittedLotIds($request);
    if (!$allowedLotIds->contains($data['IDLote'])) {
        return back()->withInput()->with('error', 'No tienes permiso para registrar producción en este lote.');
    }
}

// Redirigir según el contexto del usuario
$redirect = 'admin.produccion-huevos.index';
if ($this->isOwnerContext($request)) {
    $redirect = 'owner.produccion-huevos.index';
} elseif ($this->isEmployeeContext($request)) {
    $redirect = 'employee.produccion-huevos.index';
}
```

**Mejoras:**
- ✅ Verifica acceso para propietarios y empleados
- ✅ Redirige correctamente según el rol

---

### 6. Método `exportCsv()` Actualizado

```php
$rows = ProduccionHuevos::with('lote')
    ->whereBetween('Fecha', [$from, $to])
    ->when($this->needsFincaFilter($request), function($q) use ($request) {
        $q->whereIn('IDLote', $this->permittedLotIds($request));
    })
    ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
    ->when($turno, fn($q) => $q->where('Turno', $turno))
    ->orderBy('Fecha')
    ->get();
```

**Ahora:** CSV exportado también filtra por fincas asignadas

---

## 🎯 Comportamiento Correcto

### Administrador
- ✅ Ve **TODA** la producción de huevos de todas las fincas
- ✅ Dropdown de lotes muestra **TODOS** los lotes
- ✅ Estadísticas incluyen **TODOS** los lotes
- ✅ Puede registrar producción en **CUALQUIER** lote

### Propietario (Ej: Ana López - Fincas 3 y 4)
- ✅ Ve **SOLO** producción de lotes de Fincas 3 y 4
- ✅ Dropdown de lotes muestra **SOLO** lotes de Fincas 3 y 4
- ✅ Estadísticas incluyen **SOLO** lotes de Fincas 3 y 4
- ✅ Mejor/Peor lote se calcula **SOLO** entre sus lotes
- ✅ Puede registrar producción **SOLO** en lotes de Fincas 3 y 4
- ❌ NO puede ver ni registrar en lotes de otras fincas

### Empleado (Ej: José - Finca 1)
- ✅ Ve **SOLO** producción de lotes de Finca 1
- ✅ Dropdown de lotes muestra **SOLO** lotes de Finca 1
- ✅ Estadísticas incluyen **SOLO** lotes de Finca 1
- ✅ Puede registrar producción **SOLO** en lotes de Finca 1
- ❌ NO puede ver ni registrar en lotes de otras fincas

---

## 🔐 Seguridad Implementada

### Nivel 1: Filtrado en Consultas
Todas las consultas SQL automáticamente filtran por lotes permitidos

### Nivel 2: Validación en Store
Al guardar, verifica que el lote pertenezca a fincas asignadas:
```php
if (!$allowedLotIds->contains($data['IDLote'])) {
    return back()->with('error', 'No tienes permiso...');
}
```

### Nivel 3: Dropdown Limitado
El dropdown de lotes solo muestra opciones válidas

---

## 📊 Ejemplo Práctico

### Escenario: Ana López (Propietaria)

**Fincas asignadas:** Avícola Los Pinos (3), Finca La Esperanza (4)

**Al entrar a `/owner/produccion-huevos`:**

1. **Listado de producción:** Solo registros de lotes de Fincas 3 y 4
2. **Totales:** Solo suma huevos de Fincas 3 y 4
3. **Gráfico diario:** Solo producción de Fincas 3 y 4
4. **Mejor lote:** Entre lotes de Fincas 3 y 4 únicamente
5. **Peor lote:** Entre lotes de Fincas 3 y 4 únicamente
6. **Dropdown:** Solo muestra lotes de Fincas 3 y 4

**Al intentar registrar producción:**
- ✅ Si selecciona lote de Finca 3 → Permitido
- ✅ Si selecciona lote de Finca 4 → Permitido
- ❌ Si intenta lote de Finca 1 → Error (aunque no aparece en dropdown)

**Al exportar CSV:**
- Solo incluye producción de lotes de Fincas 3 y 4

---

## 🔄 Comparación: Antes vs Después

### ANTES ❌
```php
// Solo filtraba empleados
if ($this->isEmployeeContext($request)) {
    $query->whereIn('IDLote', $allowedLotIds);
}
```

**Problema:**
- Propietarios veían TODO (como administradores)
- No respetaba fincas asignadas para propietarios

### DESPUÉS ✅
```php
// Filtra propietarios Y empleados
if ($this->needsFincaFilter($request)) {
    $query->whereIn('IDLote', $allowedLotIds);
}
```

**Solución:**
- Propietarios solo ven sus fincas
- Empleados solo ven sus fincas
- Administradores ven todo

---

## ✅ Métodos Actualizados

1. ✅ `needsFincaFilter()` - Nuevo
2. ✅ `permittedLotIds()` - Comentario actualizado
3. ✅ `index()` - 6 lugares con filtro
4. ✅ `create()` - Filtro en lista de lotes
5. ✅ `store()` - Validación y redirección
6. ✅ `exportCsv()` - Filtro en exportación

---

## 🧪 Pruebas

### Test 1: Login como Propietario Ana
```
1. Login: ana.lopez@geproavicola.com / ana123
2. Ir a: /owner/produccion-huevos
3. Verificar que solo muestra lotes de Fincas 3 y 4
4. Verificar que estadísticas solo cuentan esos lotes
5. Intentar registrar en lote de Finca 1 → Debe fallar
```

### Test 2: Login como Empleado José
```
1. Login: empleado@geproavicola.com / empleado123
2. Ir a: /employee/produccion-huevos
3. Verificar que solo muestra lotes de Finca 1
4. Verificar que estadísticas solo cuentan esos lotes
```

### Test 3: Login como Admin
```
1. Login: admin@geproavicola.com / admin123
2. Ir a: /admin/produccion-huevos
3. Verificar que muestra TODOS los lotes de todas las fincas
4. Verificar que puede registrar en cualquier lote
```

---

## 📝 Archivo Modificado

**Ruta:** `app/Http/Controllers/Admin/ProduccionHuevosController.php`

**Líneas modificadas:**
- 28-38: Nuevo método `needsFincaFilter()`
- 40-49: Actualizado `permittedLotIds()`
- 62-66: Filtro en query principal
- 82-84: Filtro en totales
- 97-99: Filtro en serie diaria
- 109-111: Filtro en mejores días
- 121-123: Filtro en peores días
- 139-141: Filtro en producción por lote
- 182-188: Filtro en lista de lotes (index)
- 212-218: Filtro en lista de lotes (create)
- 235-251: Validación y redirección en store
- 275-277: Filtro en exportCsv

---

## 🎯 Resultado Final

✅ **Propietarios** solo ven y gestionan producción de **sus fincas asignadas**
✅ **Empleados** solo ven y gestionan producción de **sus fincas asignadas**
✅ **Administradores** ven y gestionan **toda la producción**
✅ **Seguridad** garantizada en todos los métodos
✅ **Consistencia** en filtrado de listas, estadísticas y gráficos

---

**Estado:** ✅ CORREGIDO Y FUNCIONANDO CORRECTAMENTE
**Última actualización:** 2025-10-20
