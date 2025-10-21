# Sistema de Permisos Basado en Fincas

## Objetivo
Implementar un sistema de permisos donde cada usuario solo puede ver y gestionar datos de las fincas a las que está asignado. Los administradores tienen acceso total a todas las fincas.

## Componentes Implementados

### 1. Modelo User (`app/Models/User.php`)
**Métodos agregados:**
- `hasFincasAsignadas()`: Verifica si el usuario tiene fincas asignadas
- `getFincaIds()`: Obtiene array de IDs de fincas del usuario
- `hasAccessToFinca($fincaId)`: Verifica acceso a finca específica
- `hasAccessToLote($loteId)`: Verifica acceso a lote específico
- `getAccessibleLotes()`: Obtiene lotes accesibles para el usuario

**Relación existente:**
```php
public function fincas()
{
    return $this->belongsToMany(Finca::class, 'usuario_finca', 'IDUsuario', 'IDFinca')
                ->withPivot('RolEnFinca')
                ->withTimestamps();
}
```

### 2. Trait HasFincaScope (`app/Traits/HasFincaScope.php`)
**Scopes de consulta:**
- `scopeDeUsuarioFincas($query, $userId)`: Filtra registros por fincas del usuario
- `scopeDeFincas($query, array $fincaIds)`: Filtra por IDs de fincas específicas

**Modelos que usan este trait:**
- ✅ Lote
- ✅ Gallina
- ✅ ProduccionHuevos
- ✅ Alimentacion
- ✅ Sanidad
- ✅ Mortalidad
- ✅ MovimientoLote

### 3. Trait FiltroFincasHelper (`app/Traits/FiltroFincasHelper.php`)
**Métodos para controladores:**
- `aplicarFiltroFincas($query, $user)`: Aplica filtro automático al query
- `getFincasAccesibles($user)`: Obtiene colección de fincas accesibles
- `getLotesAccesibles($user)`: Obtiene colección de lotes accesibles
- `verificarAccesoFinca($fincaId, $user)`: Verifica acceso específico
- `verificarAccesoLote($loteId, $user)`: Verifica acceso específico

**Controladores que usan este trait:**
- ✅ LoteController
- ✅ AdminDashboardController

### 4. Middleware CheckFincaAssignment (`app/Http/Middleware/CheckFincaAssignment.php`)
**Funcionalidad:**
- Verifica que usuarios (excepto admins) tengan fincas asignadas
- Redirige a `/sin-fincas` si no tienen asignación
- Registrado como alias: `check.finca`

**Registro en `bootstrap/app.php`:**
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'check.finca' => \App\Http\Middleware\CheckFincaAssignment::class,
    ]);
})
```

### 5. Vista Sin Fincas (`resources/views/sin-fincas.blade.php`)
**Características:**
- Mensaje amigable explicando la situación
- Información del usuario actual
- Instrucciones para contactar al administrador
- Opciones para volver o cerrar sesión
- Soporte modo oscuro completo

**Ruta:** `GET /sin-fincas` (protegida con `auth`)

## Lógica de Permisos

### Roles y Acceso
1. **Administrador**
   - Acceso total a todas las fincas
   - Puede asignar usuarios a fincas
   - Ve todas las estadísticas del sistema

2. **Propietario / Empleado / Otros**
   - Solo ve fincas asignadas en tabla `usuario_finca`
   - Solo puede crear/editar/eliminar en sus fincas
   - Estadísticas filtradas por sus fincas

### Tabla de Asignación
**`usuario_finca`**
```sql
- IDUsuarioFinca (PK)
- IDUsuario (FK a usuarios)
- IDFinca (FK a fincas)
- RolEnFinca (varchar nullable)
- timestamps
```

### Flujo de Filtrado

#### En Consultas
```php
// Ejemplo en LoteController
$query = Lote::with('finca');
$query = $this->aplicarFiltroFincas($query); // Aplica filtro automático
$lotes = $query->paginate(12);
```

#### En Creación/Edición
```php
// Verificar acceso antes de guardar
if (!$this->verificarAccesoFinca($validated['IDFinca'])) {
    abort(403, 'No tienes permiso para crear lotes en esta finca.');
}
```

#### En Formularios
```php
// Solo mostrar fincas accesibles
$fincas = $this->getFincasAccesibles(); // Filtra automáticamente
```

## Ejemplos de Uso

### En Controlador CRUD

```php
use App\Traits\FiltroFincasHelper;

class MiController extends Controller
{
    use FiltroFincasHelper;

    public function index(Request $request)
    {
        // Filtrar automáticamente
        $query = MiModelo::query();
        $query = $this->aplicarFiltroFincas($query);
        $registros = $query->paginate(15);

        // Obtener fincas para filtros
        $fincas = $this->getFincasAccesibles();

        return view('mi.vista', compact('registros', 'fincas'));
    }

    public function create()
    {
        // Solo fincas accesibles en dropdown
        $fincas = $this->getFincasAccesibles();
        
        if ($fincas->isEmpty() && !auth()->user()->hasRole('Administrador')) {
            return redirect()->route('sin-fincas');
        }
        
        return view('mi.create', compact('fincas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        
        // Verificar acceso
        if (!$this->verificarAccesoFinca($validated['IDFinca'])) {
            abort(403, 'No tienes permiso.');
        }

        MiModelo::create($validated);
        return redirect()->back()->with('success', 'Creado!');
    }
}
```

### En Modelo con Relaciones a Lotes

```php
use App\Traits\HasFincaScope;

class MiModelo extends Model
{
    use HasFincaScope;

    protected $fillable = ['IDLote', 'campo1', 'campo2'];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'IDLote', 'IDLote');
    }
}

// Uso en consultas
$registros = MiModelo::deUsuarioFincas(auth()->id())->get();
```

### En Dashboards

```php
public function index()
{
    $user = auth()->user();
    
    // Verificar asignación
    if (!$user->hasRole('Administrador') && !$user->hasFincasAsignadas()) {
        return redirect()->route('sin-fincas');
    }

    // Obtener IDs para filtrado
    $fincaIds = $user->hasRole('Administrador') ? [] : $user->getFincaIds();

    // Estadísticas filtradas
    $stats = [
        'lotes' => Lote::when(!empty($fincaIds), function($q) use ($fincaIds) {
            $q->whereIn('IDFinca', $fincaIds);
        })->count(),
        // ... más estadísticas
    ];

    return view('dashboard', compact('stats'));
}
```

## Aplicando a Nuevos Módulos

### Checklist para Implementar Permisos

1. **En el Modelo:**
   ```php
   use App\Traits\HasFincaScope;
   class NuevoModelo extends Model {
       use HasFincaScope;
   }
   ```

2. **En el Controlador:**
   ```php
   use App\Traits\FiltroFincasHelper;
   class NuevoController extends Controller {
       use FiltroFincasHelper;
   }
   ```

3. **En método `index()`:**
   - Aplicar `$this->aplicarFiltroFincas($query)`
   - Usar `$this->getFincasAccesibles()` para filtros

4. **En método `create()`:**
   - Verificar fincas con `$this->getFincasAccesibles()`
   - Redirigir a `/sin-fincas` si está vacío

5. **En método `store()`:**
   - Validar con `$this->verificarAccesoFinca()`
   - Retornar 403 si no tiene acceso

6. **En métodos `show()`, `edit()`, `update()`, `destroy()`:**
   - Verificar acceso antes de realizar acción
   - Usar `$this->verificarAccesoLote()` o `$this->verificarAccesoFinca()`

## Ventajas del Sistema

✅ **Seguridad por diseño:** Filtrado automático en modelos
✅ **Fácil de aplicar:** Traits reutilizables
✅ **Flexible:** Admins ven todo, usuarios solo lo suyo
✅ **Consistente:** Misma lógica en todos los módulos
✅ **Granular:** Control a nivel de finca y lote
✅ **Auditable:** Registros en pivot con timestamps

## Tablas Relacionadas

```
usuarios (IDUsuario)
    ↓
usuario_finca (IDUsuario, IDFinca)
    ↓
fincas (IDFinca)
    ↓
lotes (IDLote, IDFinca)
    ↓
gallinas, produccion_huevos, alimentacion, etc. (IDLote)
```

## Testing

### Casos de Prueba

1. **Admin sin restricciones:**
   - Login como admin
   - Ver todas las fincas y lotes
   - Crear en cualquier finca

2. **Usuario con 1 finca:**
   - Login como usuario
   - Ver solo datos de su finca
   - Intentar acceder a otra finca → 403

3. **Usuario sin fincas:**
   - Login como usuario nuevo
   - Redirigido automáticamente a `/sin-fincas`
   - No puede acceder a módulos

4. **Cambio de finca:**
   - Usuario intenta cambiar lote a finca no asignada
   - Validación rechaza con 403

## Estado Actual

✅ **Implementado:**
- Traits HasFincaScope y FiltroFincasHelper
- Middleware CheckFincaAssignment
- Métodos en User model
- LoteController completamente actualizado
- AdminDashboardController con filtros
- Vista sin-fincas.blade.php
- Documentación completa

⏳ **Pendiente (para otros módulos):**
- BirdsController/GallinasController
- ProduccionHuevosController
- AlimentacionController
- SanidadController
- Otros módulos operacionales

## Próximos Pasos

1. Aplicar trait y filtros a controladores restantes
2. Actualizar seeders para asignar usuarios a fincas
3. Crear interfaz admin para gestionar asignaciones usuario-finca
4. Agregar notificaciones cuando usuario pierde acceso a finca
5. Implementar logs de auditoría para cambios de permisos

---

**Última actualización:** Sistema base completamente funcional
**Autor:** Implementación GeproAvicola
