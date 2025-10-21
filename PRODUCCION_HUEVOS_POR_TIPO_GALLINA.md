# Sistema de Producción de Huevos por Tipo de Gallina

## ✅ Cambios Implementados

Se ha modificado completamente el sistema de producción de huevos para:
1. **Excluir lotes de engorde** (no producen huevos)
2. **Ajustar promedios según tipo de gallina**
3. **Validar cantidades según características de cada tipo**

---

## 🐔 Promedios de Producción por Tipo

### **1. Ponedoras**
- **Mínimo:** 0.28 huevos/ave/día (2 huevos/semana)
- **Promedio:** 0.36 huevos/ave/día (2.5 huevos/semana)  
- **Máximo:** 1.0 huevos/ave/día (máximo 1 huevo por ave por día)
- **Características:** Una sola gallina pone máximo 1 huevo al día, promedio 2-3 por semana

### **2. Criollas**
- **Mínimo:** 0.5 huevos/ave/día
- **Promedio:** 1.6 huevos/ave/día
- **Máximo:** 2.7 huevos/ave/día
- **Características:** Producción variable, muy productivas en picos

### **3. Doble Propósito**
- **Mínimo:** 0.7 huevos/ave/día
- **Promedio:** 0.8 huevos/ave/día
- **Máximo:** 0.9 huevos/ave/día
- **Características:** Producción moderada, balanceada carne-huevos

### **4. Reproductoras**
- **Mínimo:** 0.6 huevos/ave/día
- **Promedio:** 0.8 huevos/ave/día
- **Máximo:** 1.0 huevos/ave/día
- **Características:** Producción orientada a reproducción

### **5. Engorde** ❌ EXCLUIDAS
- **Producción:** 0 huevos/día
- **Motivo:** Son pollos de engorde, NO producen huevos
- **Sistema:** Automáticamente excluidos de listas de producción

---

## 🚫 Exclusión de Lotes de Engorde

### **Dónde se Aplica:**

1. **Lista de Lotes en Registro:**
   - No aparecen en dropdown de "Seleccionar Lote"
   - Mensaje: "Los lotes de engorde no aparecen porque no producen huevos"

2. **Listado de Producción:**
   - No aparecen en filtros de lotes
   - Solo lotes que pueden producir huevos

3. **Validación Backend:**
   - API rechaza peticiones para lotes de engorde
   - Modelo valida tipo antes de aceptar cantidad

---

## 📊 Ejemplos Prácticos

### Ejemplo 1: Lote Ponedoras
```
Lote: Ponedoras A1
Tipo: Ponedora
Aves activas: 100

Producción esperada:
├─ Mínimo: 28 huevos/día (100 × 0.28)
├─ Promedio: 36 huevos/día (100 × 0.36)
└─ Máximo: 100 huevos/día (100 × 1.0) [1 huevo/ave máximo]

Validación:
✅ 36 huevos → VÁLIDO (dentro del promedio)
✅ 50 huevos → VÁLIDO (dentro del rango)
❌ 105 huevos → ERROR (excede máximo de 100)
⚠️ 20 huevos → WARNING (debajo del mínimo de 28)
```

### Ejemplo 2: Lote Criollas
```
Lote: Criollas B2
Tipo: Criolla
Aves activas: 50

Producción esperada:
├─ Mínimo: 25 huevos/día (50 × 0.5)
├─ Promedio: 80 huevos/día (50 × 1.6)
└─ Máximo: 135 huevos/día (50 × 2.7)

Validación:
✅ 80 huevos → VÁLIDO (promedio esperado)
✅ 120 huevos → VÁLIDO (dentro del rango)
❌ 140 huevos → ERROR (excede máximo de 135)
⚠️ 20 huevos → WARNING (debajo del mínimo de 25)
```

### Ejemplo 3: Lote Doble Propósito
```
Lote: Doble Propósito C3
Tipo: Doble Propósito
Aves activas: 75

Producción esperada:
├─ Mínimo: 52.5 huevos/día (75 × 0.7)
├─ Promedio: 60 huevos/día (75 × 0.8)
└─ Máximo: 67.5 huevos/día (75 × 0.9)

Validación:
✅ 60 huevos → VÁLIDO (promedio esperado)
✅ 65 huevos → VÁLIDO (dentro del rango)
❌ 70 huevos → ERROR (excede máximo de 67.5)
⚠️ 45 huevos → WARNING (debajo del mínimo de 52.5)
```

### Ejemplo 4: Lote Engorde ❌
```
Lote: Pollos Engorde D4
Tipo: Engorde
Aves activas: 200

Resultado:
❌ NO APARECE en lista de lotes
❌ NO se puede seleccionar
❌ Si se intenta acceder por API:
   "Este lote es de aves de engorde y no producen huevos"
```

---

## 🔧 Componentes Modificados

### **1. Modelo Lote**
**Archivo:** `app/Models/Lote.php`

**Nuevos Atributos:**
```php
// Detecta el tipo predominante de gallinas
$lote->tipo_predominante // TipoGallina object

// Verifica si es lote de engorde
$lote->es_lote_de_engorde // boolean

// Verifica si puede producir huevos
$lote->puede_producir_huevos // boolean
```

**Nuevos Métodos:**
```php
// Obtiene promedios según tipo
private function getPromediosPorTipo()

// Calcula producción esperada según tipo
$lote->produccion_minima_esperada
$lote->produccion_promedio_esperada
$lote->produccion_maxima_esperada

// Valida cantidad considerando tipo
$lote->validarCantidadHuevos($cantidad)
```

---

### **2. Controlador Producción**
**Archivo:** `app/Http/Controllers/Admin/ProduccionHuevosController.php`

**Método `index()`:**
```php
// Filtra lotes excluyendo engorde
$lotes = Lote::with('gallinas.tipoGallina')
    ->get()
    ->filter(function($lote) {
        return $lote->puede_producir_huevos;
    })
```

**Método `create()`:**
```php
// Solo lotes que pueden producir + info del tipo
->map(function($lote) {
    return [
        'IDLote' => $lote->IDLote,
        'Nombre' => $lote->Nombre,
        'tipo' => $lote->tipo_predominante->Nombre
    ];
});
```

---

### **3. API Lote**
**Archivo:** `app/Http/Controllers/Api/LoteApiController.php`

**Método `getProduccionInfo()`:**
```php
// Verifica si es engorde
if ($lote->es_lote_de_engorde) {
    return response()->json([
        'success' => false,
        'message' => 'Este lote es de aves de engorde...',
        'es_engorde' => true
    ], 422);
}

// Retorna promedios específicos por tipo
'tipo_gallina' => $nombreTipo,
'huevos_por_ave_min' => $promedios['min'],
'huevos_por_ave_promedio' => $promedios['promedio'],
'huevos_por_ave_max' => $promedios['max']
```

---

### **4. Vista Formulario**
**Archivo:** `resources/views/admin/produccion_huevos/create.blade.php`

**Dropdown de Lotes:**
```blade
<label>Lote (Solo lotes ponedores)</label>
<option>{{ $lote['Nombre'] }} ({{ $lote['tipo'] }})</option>
<p>Los lotes de engorde no aparecen porque no producen huevos</p>
```

**Tarjeta Informativa:**
```blade
Tipo de Gallina: [Ponedora/Criolla/etc]
Aves Activas: 100
Promedio Esperado: 85 huevos/día
Huevos/Ave: 0.85 por ave
Rango Esperado: 70 - 100 huevos (0.7-1.0 huevos/ave según tipo)
```

**Validación en Tiempo Real:**
```javascript
// Detecta lotes de engorde
if (data.es_engorde) {
    alert('⚠️ Este lote es de aves de engorde...');
    this.loteSeleccionado = '';
}
```

---

## 🔍 Flujo de Validación

### **1. Selección de Lote**
```
Usuario → Selecciona lote
     ↓
Sistema → Verifica tipo predominante
     ↓
¿Es engorde? → SÍ → ❌ Rechazar y limpiar selección
     ↓ NO
Cargar información del lote
```

### **2. Ingreso de Cantidad**
```
Usuario → Ingresa cantidad de huevos
     ↓
Sistema → Calcula rangos según tipo de gallina
     ↓
Validación en tiempo real:
├─ > Máximo → ❌ ERROR (rojo)
├─ < Mínimo → ⚠️ WARNING (amarillo)
└─ En rango → ✅ VÁLIDO (verde)
```

### **3. Envío de Formulario**
```
Usuario → Submit
     ↓
Backend → Valida lote no sea engorde
     ↓
Backend → Valida cantidad vs máximo permitido
     ↓
¿Válido? → SÍ → ✅ Guardar registro
     ↓ NO
❌ Rechazar con mensaje específico
```

---

## 📱 Interfaz de Usuario

### **Formulario de Registro**

```
┌─────────────────────────────────────────────────────┐
│ Registrar Producción de Huevos (Hoy)              │
├─────────────────────────────────────────────────────┤
│ Fecha: [2025-01-20]                                │
│                                                     │
│ Lote (Solo lotes ponedores): [▼]                   │
│ ├─ Ponedoras A1 (Ponedora)                        │
│ ├─ Criollas B2 (Criolla)                          │
│ ├─ Doble Propósito C3 (Doble Propósito)          │
│ └─ [Engorde D4 NO APARECE]                        │
│ ℹ️ Los lotes de engorde no aparecen porque no     │
│    producen huevos                                 │
│                                                     │
│ ┌─────────────────────────────────────────────┐  │
│ │ 📊 Información del Lote                      │  │
│ │ Tipo de Gallina: Criolla                     │  │
│ │ Aves Activas: 50                             │  │
│ │ Promedio Esperado: 80 huevos/día             │  │
│ │ Huevos/Ave: 1.6 por ave                      │  │
│ │ Rango Esperado: 25 - 135 huevos              │  │
│ │                (0.5-2.7 huevos/ave según tipo)│  │
│ └─────────────────────────────────────────────┘  │
│                                                     │
│ Cantidad de Huevos: [85]                           │
│ ✓ Cantidad dentro del rango promedio esperado     │
│                                                     │
│ [Cancelar]  [Guardar]                              │
└─────────────────────────────────────────────────────┘
```

---

## 🧪 Casos de Prueba

### Test 1: Lote Ponedoras
```
1. Ir a Registrar Producción
2. Seleccionar "Ponedoras A1 (Ponedora)"
3. ✅ Muestra: Tipo: Ponedora, Rango: 0.7-1.0 huevos/ave
4. Ingresar 85 huevos (100 aves × 0.85)
5. ✅ Validación: "Cantidad dentro del rango promedio"
6. Guardar
7. ✅ Registro exitoso
```

### Test 2: Lote Criollas
```
1. Seleccionar "Criollas B2 (Criolla)"
2. ✅ Muestra: Tipo: Criolla, Rango: 0.5-2.7 huevos/ave
3. Ingresar 120 huevos (50 aves × 2.4)
4. ✅ Validación: "Cantidad válida"
5. Ingresar 140 huevos (excede 135 máximo)
6. ❌ Validación: "La cantidad excede el máximo posible"
```

### Test 3: Lote Doble Propósito
```
1. Seleccionar "Doble Propósito C3 (Doble Propósito)"
2. ✅ Muestra: Tipo: Doble Propósito, Rango: 0.7-0.9
3. Ingresar 60 huevos (75 aves × 0.8)
4. ✅ Validación: "Cantidad dentro del rango promedio"
5. Guardar
6. ✅ Registro exitoso
```

### Test 4: Intento con Lote Engorde
```
1. Ir a Registrar Producción
2. ❌ "Engorde D4" NO aparece en lista
3. ✅ Solo aparecen lotes ponedores
4. Mensaje visible: "Los lotes de engorde no aparecen..."
```

### Test 5: Acceso Directo API (Engorde)
```
1. Intento manual: GET /api/lotes/10/produccion-info
2. (Lote 10 = Engorde)
3. ❌ Response 422:
   {
     "success": false,
     "message": "Este lote es de aves de engorde...",
     "es_engorde": true
   }
```

---

## 📊 Comparativa Antes/Después

### **ANTES ❌**
```
Todos los lotes:
├─ Promedio fijo: 4 huevos/ave
├─ Rango fijo: 3-5 huevos/ave
└─ Incluía lotes de engorde

Ejemplo (100 aves Criollas):
├─ Mínimo: 300 huevos (100 × 3)
├─ Promedio: 400 huevos (100 × 4)
└─ Máximo: 500 huevos (100 × 5)
❌ No realista para Criollas
```

### **AHORA ✅**
```
Según tipo de gallina:
- Ponedoras: 0.28-1.0 huevos/ave (promedio 2-3 por semana, máx 1 por día)
- Criollas: 0.5-2.7 huevos/ave
- Doble Propósito: 0.7-0.9 huevos/ave
- Reproductoras: 0.6-1.0 huevos/ave
- Engorde: EXCLUIDAS

Ejemplo (100 aves Criollas):
├─ Mínimo: 50 huevos (100 × 0.5)
├─ Promedio: 160 huevos (100 × 1.6)
└─ Máximo: 270 huevos (100 × 2.7)
✅ Realista según comportamiento de Criollas
```

---

## ⚡ Beneficios

1. **Precisión:** Cálculos realistas según características de cada tipo
2. **Prevención:** No se pueden registrar huevos para engorde
3. **Validación Inteligente:** Rangos ajustados a realidad avícola
4. **Transparencia:** Usuario ve tipo y promedios específicos
5. **Seguridad:** Validación frontend y backend

---

## 🔐 Seguridad

### **Capas de Validación:**

1. **Frontend - Vista:**
   - Lotes de engorde no aparecen en lista
   - Alerta si se intenta seleccionar engorde

2. **Frontend - API:**
   - Respuesta 422 si lote es de engorde
   - JavaScript resetea selección

3. **Backend - Modelo:**
   - Método `puede_producir_huevos`
   - Validación en `validarCantidadHuevos()`

4. **Backend - Request:**
   - Validación de cantidad vs máximo
   - Rechazo de lotes de engorde

---

## 📝 Archivos Modificados

1. ✅ `app/Models/Lote.php` - Métodos de tipo y promedios
2. ✅ `app/Http/Controllers/Admin/ProduccionHuevosController.php` - Filtrado de lotes
3. ✅ `app/Http/Controllers/Api/LoteApiController.php` - API con validación
4. ✅ `resources/views/admin/produccion_huevos/create.blade.php` - Vista actualizada

---

## ✅ Estado

**COMPLETAMENTE IMPLEMENTADO Y FUNCIONAL**

**Fecha:** 2025-01-20

**Características:**
- ✅ Lotes de engorde excluidos
- ✅ Promedios específicos por tipo
- ✅ Validación frontend y backend
- ✅ Mensajes informativos claros
- ✅ Interfaz actualizada con tipo de gallina

---

**¡Sistema ahora refleja la realidad avícola de cada tipo de gallina!** 🐔📊
