# Filtro por Lote en Reportes - Implementado

## ✅ Funcionalidad Agregada

Se ha agregado la capacidad de **filtrar por lote** en todos los reportes del sistema, además del filtro existente por finca y fechas.

---

## 🎯 ¿Qué se puede hacer ahora?

### Antes ❌
- Solo se podía filtrar por:
  - Finca
  - Rango de fechas (Desde - Hasta)

### Ahora ✅
- Se puede filtrar por:
  - **Finca**
  - **Lote** ⭐ NUEVO
  - **Rango de fechas**

---

## 📊 Filtros Disponibles

### 1. **Filtro por Finca**
- Muestra solo lotes de la finca seleccionada
- Al seleccionar, actualiza automáticamente el dropdown de lotes

### 2. **Filtro por Lote** ⭐ NUEVO
- Dropdown con todos los lotes disponibles
- Se actualiza según la finca seleccionada
- Permite analizar un lote específico

### 3. **Filtro por Fechas**
- Desde: Fecha inicial
- Hasta: Fecha final

---

## 🔄 Flujo de Uso

### Caso 1: Filtrar Solo por Finca
```
1. Seleccionar "Avícola Los Pinos" en Finca
2. Dejar "Todos los lotes" en Lote
3. Click "Aplicar"
4. ✅ Muestra todos los datos de todos los lotes de Finca 3
```

### Caso 2: Filtrar por Lote Específico
```
1. Seleccionar "Avícola Los Pinos" en Finca
2. Seleccionar "Ponedoras A1" en Lote
3. Click "Aplicar"
4. ✅ Muestra solo datos del lote Ponedoras A1
```

### Caso 3: Filtrar con Todo
```
1. Finca: Avícola Los Pinos
2. Lote: Ponedoras A1
3. Desde: 2025-01-01
4. Hasta: 2025-01-31
5. Click "Aplicar"
6. ✅ Muestra datos de Ponedoras A1 solo en enero 2025
```

---

## 🛠️ Implementación Técnica

### **1. Controlador Actualizado**
**`app/Http/Controllers/Admin/ReportController.php`**

#### Método `index()`:
```php
$filters = [
    'finca' => $request->integer('finca'),
    'lote' => $request->integer('lote'),  // ⭐ NUEVO
    'desde' => $request->input('desde'),
    'hasta' => $request->input('hasta'),
];

// Obtener lotes según finca seleccionada
$lotes = collect();
if ($filters['finca']) {
    $lotes = DB::table('lotes')->where('IDFinca', $filters['finca'])
        ->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
} elseif ($ownerFincas) {
    $lotes = DB::table('lotes')->whereIn('IDFinca', $ownerFincas)
        ->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
} else {
    $lotes = DB::table('lotes')->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
}
```

#### Métodos de Reportes Actualizados:
```php
// getProductionReport()
if ($filters['lote']) {
    $q->where('produccion_huevos.IDLote', $filters['lote']);
}

// getFeedingReport()
->when($filters['lote'], fn($q)=>$q->where('a.IDLote',$filters['lote']))

// getHealthReport()
->when($filters['lote'], fn($q)=>$q->where('s.IDLote',$filters['lote']))

// getFinanceReport()
->when($filters['lote'], fn($q)=>$q->where('m.IDLote',$filters['lote']))
```

---

### **2. Vista Actualizada**
**`resources/views/admin/reports/index.blade.php`**

#### Formulario de Filtros:
```blade
<form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Finca -->
    <select name="finca" id="fincaSelect">
        <option value="">Todas las fincas</option>
        @foreach($fincas as $f)
            <option value="{{ $f->IDFinca }}">{{ $f->Nombre }}</option>
        @endforeach
    </select>
    
    <!-- Lote ⭐ NUEVO -->
    <select name="lote" id="loteSelect">
        <option value="">Todos los lotes</option>
        @foreach($lotes as $l)
            <option value="{{ $l->IDLote }}">{{ $l->Nombre }}</option>
        @endforeach
    </select>
    
    <!-- Fechas -->
    <input type="date" name="desde">
    <input type="date" name="hasta">
    
    <!-- Botones -->
    <button type="submit">Aplicar</button>
</form>
```

---

## 📈 Ejemplos de Uso

### Ejemplo 1: Analizar Producción de un Lote
```
Objetivo: Ver cuántos huevos produjo "Ponedoras A1" en enero

Pasos:
1. Ir a Reportes
2. Finca: Avícola Los Pinos
3. Lote: Ponedoras A1
4. Desde: 2025-01-01
5. Hasta: 2025-01-31
6. Click "Aplicar"

Resultado:
✅ Gráfico de producción diaria solo de Ponedoras A1
✅ Total de huevos del lote en el mes
```

### Ejemplo 2: Analizar Alimentación de un Lote
```
Objetivo: Ver cuánto alimento consumió un lote específico

Pasos:
1. Ir a Reportes
2. Lote: Ponedoras B2
3. Click "Aplicar"

Resultado:
✅ Tipos de alimento consumidos por ese lote
✅ Cantidad en kg por tipo
✅ Consumo diario
```

### Ejemplo 3: Tratamientos de Salud de un Lote
```
Objetivo: Ver qué tratamientos se aplicaron a un lote

Pasos:
1. Ir a Reportes
2. Lote: Ponedoras C3
3. Desde: 2025-01-01
4. Hasta: 2025-01-31
5. Click "Aplicar"

Resultado:
✅ Tipos de tratamientos aplicados
✅ Fechas de aplicación
✅ Historial completo
```

---

## 🔐 Seguridad

### **Filtrado Automático por Permisos:**

**Administrador:**
- ✅ Ve todos los lotes de todas las fincas
- ✅ Puede filtrar por cualquier lote

**Propietario (Ana - Fincas 3 y 4):**
- ✅ Ve solo lotes de Fincas 3 y 4
- ✅ Puede filtrar solo por sus lotes
- ❌ NO puede seleccionar lotes de otras fincas

**Empleado (José - Finca 1):**
- ✅ Ve solo lotes de Finca 1
- ✅ Puede filtrar solo por lotes de Finca 1
- ❌ NO puede seleccionar lotes de otras fincas

---

## 📊 Impacto en Exportaciones a Excel

Cuando se exporta a Excel con filtro de lote:

**Sin filtro de lote:**
```
Producción de todos los lotes de la finca seleccionada
```

**Con filtro de lote:**
```
Producción solo del lote específico seleccionado
```

**Ejemplo:**
```
URL: /admin/reports?finca=3&lote=5&desde=2025-01-01
Excel descargado contiene:
✅ Solo datos del Lote ID 5
✅ Solo en rango de fechas especificado
✅ Formato profesional mantenido
```

---

## 🎨 Interfaz Visual

### **Layout del Formulario:**
```
┌─────────────┬─────────────┬─────────────┬─────────────┬──────────┐
│   Finca     │    Lote     │   Desde     │    Hasta    │ Botones  │
│  [Select]   │  [Select]   │   [Date]    │   [Date]    │ [Aplicar]│
└─────────────┴─────────────┴─────────────┴─────────────┴──────────┘
```

### **Grid Responsive:**
- **Desktop (lg):** 5 columnas (todos los filtros en una fila)
- **Tablet (sm):** 2 columnas
- **Mobile:** 1 columna (apilado)

---

## 🧪 Pruebas

### Test 1: Filtrar por Lote Específico
```
1. Login como Admin
2. Ir a /admin/reports
3. Seleccionar Lote: "Ponedoras A1"
4. Click "Aplicar"
5. ✅ URL: /admin/reports?lote=5
6. ✅ Gráficos muestran solo datos de ese lote
7. ✅ Top lotes muestra solo ese lote
```

### Test 2: Combinar Finca + Lote
```
1. Seleccionar Finca: Avícola Los Pinos
2. Seleccionar Lote: Ponedoras A1
3. Desde: 2025-01-01
4. Hasta: 2025-01-15
5. Click "Aplicar"
6. ✅ URL: /admin/reports?finca=3&lote=5&desde=2025-01-01&hasta=2025-01-15
7. ✅ Datos filtrados correctamente
```

### Test 3: Exportar con Filtro de Lote
```
1. Aplicar filtros: Lote = Ponedoras A1
2. Click "Excel Producción"
3. ✅ Descarga Excel
4. Abrir archivo
5. ✅ Solo contiene datos de Ponedoras A1
6. ✅ Headers formateados
```

### Test 4: Limpiar Filtros
```
1. Con filtros aplicados
2. Click botón "X" (limpiar)
3. ✅ Redirige sin parámetros
4. ✅ Muestra todos los datos
5. ✅ Dropdown de lotes resetea
```

---

## 📝 Archivos Modificados

✅ **Controlador:**
- `app/Http/Controllers/Admin/ReportController.php`
  - Método `index()`: Agrega lotes a la vista
  - Método `makeFilters()`: Incluye lote
  - Métodos `getProductionReport()`, `getFeedingReport()`, `getHealthReport()`, `getFinanceReport()`: Filtran por lote

✅ **Vista:**
- `resources/views/admin/reports/index.blade.php`
  - Grid cambiado de 4 a 5 columnas
  - Dropdown de lote agregado
  - Texto descriptivo actualizado
  - Script JavaScript para actualización dinámica

---

## 🚀 Mejoras Futuras Sugeridas

1. **AJAX para Lotes:**
   - Cuando se cambie la finca, cargar lotes vía AJAX sin recargar página

2. **Indicador de Filtros Activos:**
   - Badge que muestre cuántos filtros están aplicados

3. **Guardado de Filtros:**
   - Recordar última selección del usuario

4. **Exportar con Nombre Personalizado:**
   - Incluir nombre del lote en nombre del archivo Excel
   - Ejemplo: `Reporte_Produccion_PonedorasA1_2025-01-20.xlsx`

---

## ✅ Resumen

**ANTES:**
- Filtros: Finca, Fechas
- 4 columnas en formulario

**AHORA:**
- Filtros: Finca, **Lote** ⭐, Fechas
- 5 columnas en formulario
- Filtrado en los 4 reportes
- Funciona en exportaciones Excel
- Respeta permisos de usuario

---

**Estado:** ✅ COMPLETAMENTE FUNCIONAL
**Fecha:** 2025-01-20
