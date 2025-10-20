# 🥚 Sistema de Validación Realista de Producción de Huevos

## 📋 Descripción General

Sistema implementado para validar que la cantidad de huevos registrada sea realista en función del número de aves activas en cada lote. El sistema calcula automáticamente los rangos esperados de producción basándose en un promedio de **4 huevos por ave al día**, con un rango de **3-5 huevos por ave**.

---

## 🎯 Características Implementadas

### 1. **Modelo Lote Actualizado**
Archivo: `app/Models/Lote.php`

**Métodos agregados:**

- `getAvesActivasCountAttribute()`: Cuenta las aves activas del lote
- `getProduccionMinimaEsperadaAttribute()`: Calcula mínimo (3 huevos/ave)
- `getProduccionPromedioEsperadaAttribute()`: Calcula promedio (4 huevos/ave)
- `getProduccionMaximaEsperadaAttribute()`: Calcula máximo (5 huevos/ave)
- `validarCantidadHuevos($cantidad)`: Valida si la cantidad es realista

**Ejemplo de uso:**
```php
$lote = Lote::find(1);
echo $lote->aves_activas_count; // 10 aves
echo $lote->produccion_promedio_esperada; // 40 huevos
echo $lote->produccion_maxima_esperada; // 50 huevos
```

---

### 2. **API Endpoint para Información del Lote**
Archivo: `app/Http/Controllers/Api/LoteApiController.php`

**Rutas creadas:**
- `GET /api/lotes/{lote}/produccion-info`: Obtiene información de producción del lote
- `POST /api/lotes/validar-cantidad`: Valida una cantidad específica

**Respuesta de ejemplo:**
```json
{
  "success": true,
  "data": {
    "lote_id": 1,
    "lote_nombre": "Lote Ponedoras A",
    "aves_activas": 10,
    "produccion_minima": 30,
    "produccion_promedio": 40,
    "produccion_maxima": 50,
    "huevos_por_ave_min": 3,
    "huevos_por_ave_promedio": 4,
    "huevos_por_ave_max": 5
  }
}
```

---

### 3. **Validación Backend**
Archivo: `app/Http/Requests/Admin/StoreProduccionHuevosRequest.php`

**Validación personalizada implementada:**
```php
public function withValidator(Validator $validator): void
{
    $validator->after(function ($validator) {
        $lote = Lote::find($this->input('IDLote'));
        $validacion = $lote->validarCantidadHuevos($this->input('CantidadHuevos'));
        
        if (!$validacion['valido']) {
            $validator->errors()->add('CantidadHuevos', $validacion['mensaje']);
        }
    });
}
```

**Validación automática:**
- Si se ingresan más de 5 huevos por ave, el formulario no se envía
- Se muestra mensaje de error específico
- Se indica el máximo permitido basado en las aves activas

---

### 4. **Formulario Dinámico con Alpine.js**
Archivo: `resources/views/admin/produccion_huevos/create.blade.php`

**Funcionalidades:**

#### A. Información del Lote en Tiempo Real
Al seleccionar un lote, se muestra:
- ✅ Número de aves activas
- ✅ Producción promedio esperada (4 huevos/ave)
- ✅ Rango esperado (3-5 huevos/ave × cantidad de aves)

#### B. Validación en Tiempo Real
Mientras el usuario escribe la cantidad de huevos:
- ❌ **Error (rojo)**: Cantidad excede el máximo posible
- ⚠️ **Advertencia (amarillo)**: Cantidad por debajo del mínimo esperado
- ✓ **Éxito (verde)**: Cantidad dentro del rango válido

#### C. Mensajes Visuales
```
⚠️ La cantidad excede el máximo posible (50 huevos)
⚠️ Cantidad por debajo del mínimo esperado (30 huevos)
✓ Cantidad dentro del rango promedio esperado
✓ Cantidad válida (30 - 50 huevos)
```

---

## 📊 Lógica de Cálculo

### Fórmulas utilizadas:
```
Aves Activas = COUNT(gallinas WHERE Estado = 'Activa' AND IDLote = X)

Producción Mínima = Aves Activas × 3
Producción Promedio = Aves Activas × 4
Producción Máxima = Aves Activas × 5
```

### Ejemplo práctico:
```
Lote con 10 aves activas:
├─ Mínimo esperado: 30 huevos/día
├─ Promedio esperado: 40 huevos/día
└─ Máximo permitido: 50 huevos/día

✓ Registro de 35 huevos → VÁLIDO (dentro del rango)
✓ Registro de 45 huevos → VÁLIDO (dentro del rango)
⚠️ Registro de 25 huevos → ADVERTENCIA (por debajo del mínimo)
❌ Registro de 55 huevos → ERROR (excede el máximo)
```

---

## 🔄 Flujo de Validación

### Frontend (Tiempo Real)
```
Usuario selecciona lote
    ↓
Fetch a API: /api/lotes/{id}/produccion-info
    ↓
Muestra información del lote
    ↓
Usuario ingresa cantidad de huevos
    ↓
Validación JavaScript en tiempo real
    ↓
Feedback visual inmediato (rojo/amarillo/verde)
```

### Backend (Al enviar)
```
Usuario envía formulario
    ↓
Validaciones básicas (required, integer, etc.)
    ↓
Validación personalizada (withValidator)
    ↓
Lote::validarCantidadHuevos($cantidad)
    ↓
Si es válido → Guarda registro
Si no es válido → Retorna error
```

---

## 🎨 Estados Visuales

### Tarjeta de Información del Lote
```blade
<!-- Fondo azul claro con borde -->
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200">
    📊 Información del Lote
    Aves Activas: 10
    Promedio Esperado: 40 huevos/día
    Rango Esperado: 30 - 50 huevos (3-5 huevos/ave)
</div>
```

### Input de Cantidad con Feedback
```blade
<!-- Borde cambia según validación -->
<input 
    :class="{
        'border-red-500': validacionCantidad === 'error',
        'border-yellow-500': validacionCantidad === 'warning',
        'border-green-500': validacionCantidad === 'success'
    }">
```

---

## 🚀 Cómo Usar

### Para Usuarios:
1. Selecciona un lote en el formulario de producción
2. Observa la información que aparece automáticamente
3. Ingresa la cantidad de huevos recolectados
4. El sistema te indicará si la cantidad es realista
5. Si excede el máximo, no podrás enviar el formulario

### Para Desarrolladores:
```php
// Obtener información de un lote
$lote = Lote::find(1);
$avesActivas = $lote->aves_activas_count;
$promedioEsperado = $lote->produccion_promedio_esperada;

// Validar cantidad
$validacion = $lote->validarCantidadHuevos(45);
if ($validacion['valido']) {
    // Procesar registro
} else {
    // Mostrar error
    echo $validacion['mensaje'];
}
```

---

## 📌 Archivos Modificados/Creados

### Creados:
1. `app/Http/Controllers/Api/LoteApiController.php`
2. `VALIDACION_PRODUCCION_HUEVOS.md` (este archivo)

### Modificados:
1. `app/Models/Lote.php` - Agregados métodos de cálculo y validación
2. `app/Http/Requests/Admin/StoreProduccionHuevosRequest.php` - Validación personalizada
3. `resources/views/admin/produccion_huevos/create.blade.php` - UI dinámica
4. `routes/web.php` - Rutas API agregadas

---

## ✅ Beneficios del Sistema

1. **Prevención de Errores**: Evita registros irrealistas
2. **Feedback Inmediato**: Usuario sabe al instante si la cantidad es válida
3. **Educativo**: Muestra rangos esperados para cada lote
4. **Flexible**: Se adapta automáticamente según aves activas
5. **Validación Doble**: Frontend (UX) + Backend (seguridad)

---

## 🔮 Posibles Mejoras Futuras

- [ ] Considerar edad del lote (aves jóvenes producen menos)
- [ ] Historial de producción del lote para promedios personalizados
- [ ] Alertas cuando la producción baje significativamente
- [ ] Gráficos de tendencia de producción
- [ ] Exportar reportes de producción vs. esperado

---

## 📞 Soporte

Para dudas o problemas con el sistema de validación, revisar:
- Logs de Laravel en `storage/logs/laravel.log`
- Consola del navegador para errores de JavaScript
- Respuesta de API en Network tab (DevTools)

---

**Estado:** ✅ COMPLETAMENTE IMPLEMENTADO Y FUNCIONAL

**Fecha de Implementación:** Octubre 2024

**Autor:** Sistema GeproAvicola
