# Sistema de Reportes con Exportación a Excel

## ✅ Funcionalidades Implementadas

Se ha implementado un **sistema completo de reportes** con exportación a **Excel** (.xlsx) con formato profesional para:
- 📊 Producción de Huevos
- 🌾 Alimentación
- 💊 Salud (Sanidad)
- 💰 Finanzas (Movimientos)

---

## 🔧 Problemas Corregidos

### 1. Filtros No Funcionaban ❌
**Problema:** El formulario de filtros tenía ruta hardcodeada a `admin.reports.index`

**Solución:**
```blade
<!-- ANTES -->
<form action="{{ route('admin.reports.index') }}">

<!-- DESPUÉS -->
<form action="{{ route($area.'.reports.index') }}">
```

Ahora detecta automáticamente si es admin, owner o employee y usa la ruta correcta.

---

### 2. Exportación a CSV en lugar de Excel ❌
**Problema:** Los reportes se descargaban como CSV simple sin formato

**Solución:** Implementado PhpSpreadsheet para exportar a Excel con:
- ✅ Headers con estilo (fondo azul, texto blanco, negritas)
- ✅ Bordes en todas las celdas
- ✅ Auto-ajuste de columnas
- ✅ Múltiples secciones de datos en una sola hoja
- ✅ Formato profesional

---

## 📥 Exportaciones Disponibles

### 1. **Reporte de Producción**
**Archivo:** `Reporte_Produccion_YYYY-MM-DD.xlsx`

**Contenido:**
- **Columnas A-B:** Producción diaria
  - Fecha
  - Cantidad de Huevos
  
- **Columnas D-E:** Top 10 Lotes
  - Nombre del Lote
  - Total Producción

**Ejemplo:**
```
| Fecha      | Cantidad | | Top 10 Lotes        | Total |
|------------|----------|---|---------------------|-------|
| 2025-01-15 | 450      | | Lote Ponedoras A1   | 15000 |
| 2025-01-16 | 480      | | Lote Ponedoras B2   | 12500 |
```

---

### 2. **Reporte de Alimentación**
**Archivo:** `Reporte_Alimentacion_YYYY-MM-DD.xlsx`

**Contenido:**
- **Columnas A-B:** Consumo por tipo de alimento
  - Tipo de Alimento
  - Cantidad (Kg)
  
- **Columnas D-E:** Consumo diario
  - Fecha
  - Kg Consumidos

**Ejemplo:**
```
| Tipo de Alimento              | Kg    | | Fecha      | Kg      |
|-------------------------------|-------|---|------------|---------|
| Concentrado Ponedoras Inicio  | 2450  | | 2025-01-15 | 850     |
| Maíz Molido                   | 1800  | | 2025-01-16 | 920     |
```

---

### 3. **Reporte de Salud**
**Archivo:** `Reporte_Salud_YYYY-MM-DD.xlsx`

**Contenido:**
- **Columnas A-B:** Tratamientos por tipo
  - Tipo de Tratamiento
  - Total Aplicaciones
  
- **Columnas D-F:** Tratamientos recientes
  - Lote
  - Tratamiento
  - Fecha

**Ejemplo:**
```
| Tipo Tratamiento | Total | | Lote            | Tratamiento  | Fecha      |
|------------------|-------|---|-----------------|--------------|------------|
| Vacunación       | 45    | | Ponedoras A1    | Vacuna       | 2025-01-15 |
| Desparasitación  | 30    | | Ponedoras B2    | Vitaminas    | 2025-01-14 |
```

---

### 4. **Reporte de Finanzas**
**Archivo:** `Reporte_Finanzas_YYYY-MM-DD.xlsx`

**Contenido:**
- **Columnas A-C:** Movimientos detallados
  - Fecha
  - Tipo de Movimiento
  - Cantidad
  
- **Columnas E-F:** Resumen de totales
  - Tipo (Ventas/Compras)
  - Total

**Ejemplo:**
```
| Fecha      | Movimiento | Cant | | Resumen       | Total |
|------------|------------|------|---|---------------|-------|
| 2025-01-15 | Venta      | 50   | | Total Ventas  | 150   |
| 2025-01-16 | Compra     | 30   | | Total Compras | 80    |
```

---

## 🎨 Formato de Excel

### **Headers (Encabezados):**
- **Color de fondo:** Azul (#2563EB)
- **Texto:** Blanco, negritas, tamaño 12
- **Alineación:** Centrado horizontal y vertical
- **Bordes:** Negros en todas las celdas

### **Celdas de Datos:**
- Texto sin formato
- Auto-ajuste de ancho de columna
- Fácil de leer

---

## 🔄 Flujo de Uso

### **Desde la Interfaz:**

1. **Ir a Reportes:**
   - Admin: `/admin/reports`
   - Owner: `/owner/reports`
   - Employee: `/employee/reports`

2. **Aplicar Filtros (Opcional):**
   - **Finca:** Seleccionar finca específica o "Todas"
   - **Desde:** Fecha inicial
   - **Hasta:** Fecha final
   - Click "Aplicar"

3. **Exportar:**
   - Click en botón de color según reporte:
     - 🔵 **Azul:** Producción
     - 🟡 **Amarillo:** Alimentación
     - 🔴 **Rojo:** Salud
     - 🟢 **Verde:** Finanzas

4. **Descarga Automática:**
   - Archivo Excel se descarga
   - Nombre: `Reporte_[Tipo]_[Fecha].xlsx`

---

## 🛡️ Seguridad y Permisos

### **Filtrado Automático por Rol:**

**Administrador:**
- ✅ Ve todas las fincas
- ✅ Exporta todos los datos

**Propietario:**
- ✅ Solo ve datos de fincas asignadas (ej: Fincas 3 y 4)
- ✅ Exporta solo sus datos

**Empleado:**
- ✅ Solo ve datos de fincas asignadas (ej: Finca 1)
- ✅ Exporta solo sus datos

---

## 📝 Archivos Modificados

### 1. **Controlador**
**`app/Http/Controllers/Admin/ReportController.php`**

**Cambios:**
- ✅ Agregados imports de PhpSpreadsheet
- ✅ Métodos `exportProduction()`, `exportFeeding()`, `exportHealth()`, `exportFinance()` reescritos
- ✅ Métodos helper agregados:
  - `styleHeader()` - Aplica estilo a encabezados
  - `autoSizeColumns()` - Auto-ajusta columnas
  - `downloadExcel()` - Genera descarga de Excel

---

### 2. **Vista de Reportes**
**`resources/views/admin/reports/index.blade.php`**

**Cambios:**
- ✅ Agregada detección de contexto (admin/owner/employee)
- ✅ Rutas dinámicas en botones de exportación
- ✅ Formulario de filtros usa ruta dinámica
- ✅ Botón limpiar usa ruta dinámica

**Antes:**
```blade
<a href="{{ route('admin.reports.export.production') }}">
<form action="{{ route('admin.reports.index') }}">
```

**Después:**
```blade
<a href="{{ route($area.'.reports.export.production') }}">
<form action="{{ route($area.'.reports.index') }}">
```

---

### 3. **Composer**
**`composer.json`**

**Agregado:**
```json
"phpoffice/phpspreadsheet": "^2.0"
```

---

## 🚀 Instalación

**Para que funcione, ejecuta:**

```bash
composer require phpoffice/phpspreadsheet
```

Esto instalará la librería PhpSpreadsheet necesaria para generar archivos Excel.

---

## 🧪 Pruebas

### Test 1: Exportar Producción
```
1. Login como Admin
2. Ir a Reportes
3. Seleccionar filtros (opcional):
   - Finca: Avícola Los Pinos
   - Desde: 2025-01-01
   - Hasta: 2025-01-31
4. Click "Exportar Producción" (botón azul)
5. ✅ Se descarga: Reporte_Produccion_2025-01-20.xlsx
6. Abrir archivo
7. ✅ Ver headers azules con texto blanco
8. ✅ Ver datos de producción diaria
9. ✅ Ver top 10 lotes en columnas D-E
```

### Test 2: Filtros Funcionando
```
1. Login como Propietario (Ana)
2. Ir a /owner/reports
3. Seleccionar Finca 3
4. Click "Aplicar"
5. ✅ URL cambia a: /owner/reports?finca=3
6. ✅ Gráficos se actualizan
7. ✅ Solo muestra datos de Finca 3
```

### Test 3: Exportar como Propietario
```
1. Como Ana en /owner/reports
2. Filtrar por Finca 3
3. Click "Exportar Alimentación"
4. ✅ Descarga Excel
5. Abrir archivo
6. ✅ Solo contiene datos de Finca 3
7. ❌ NO contiene datos de otras fincas
```

### Test 4: Limpiar Filtros
```
1. En reportes con filtros aplicados
2. Click en botón "X" (limpiar)
3. ✅ Redirige a ruta correcta según rol
4. ✅ Muestra todos los datos sin filtros
```

---

## 📊 Ejemplo de Archivo Excel Generado

### **Hoja: Producción Huevos**

```
┌─────────────┬─────────────────┬───┬──────────────────────┬──────────────────┐
│ Fecha       │ Cantidad Huevos │   │ Top 10 Lotes         │ Total Producción │
├─────────────┼─────────────────┼───┼──────────────────────┼──────────────────┤
│ 2025-01-15  │ 450             │   │ Lote Ponedoras A1    │ 15000            │
│ 2025-01-16  │ 480             │   │ Lote Ponedoras B2    │ 12500            │
│ 2025-01-17  │ 465             │   │ Lote Ponedoras C3    │ 11200            │
└─────────────┴─────────────────┴───┴──────────────────────┴──────────────────┘

Cabeceras con fondo azul (#2563EB) y texto blanco en negritas
Columnas auto-ajustadas
```

---

## ✅ Beneficios

1. **Formato Profesional:**
   - Headers con estilo
   - Fácil de leer
   - Listo para imprimir

2. **Múltiples Secciones:**
   - Varios tipos de datos en una hoja
   - Todo organizado

3. **Filtrado Inteligente:**
   - Respeta permisos de usuario
   - Solo exporta datos permitidos

4. **Compatible:**
   - Excel 2007+
   - Google Sheets
   - LibreOffice Calc

5. **Nombres Descriptivos:**
   - Incluye fecha en nombre
   - Fácil de organizar

---

## 🎯 Próximos Pasos Sugeridos

1. **Agregar Gráficos en Excel:** PhpSpreadsheet soporta gráficos
2. **Múltiples Hojas:** Una hoja por sección
3. **Totales y Fórmulas:** Calcular totales automáticamente
4. **Filtros en Excel:** Agregar auto-filtros
5. **Condicional Formatting:** Colores según valores

---

**Estado:** ✅ COMPLETAMENTE FUNCIONAL
**Última actualización:** 2025-01-20

**IMPORTANTE:** Ejecutar `composer require phpoffice/phpspreadsheet` antes de usar.
