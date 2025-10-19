<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBirdRequest;
use App\Models\Bird;
use App\Models\Lote;
use App\Models\TipoGallina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class BirdsController extends Controller
{
    private function isOwnerContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('owner.*')) || ($user && $user->role && $user->role->NombreRol === 'Propietario');
    }

    /**
     * Display a gallery of bird images
     *
     * @return \Illuminate\View\View
     */
    public function gallery()
    {
        $birds = Bird::whereNotNull('UrlImagen')
            ->where('UrlImagen', '!=', '')
            ->orderBy('IDLote')
            ->paginate(20); // Show 20 birds per page

        return view('admin.birds.gallery', [
            'birds' => $birds,
            'isOwnerContext' => $this->isOwnerContext(request())
        ]);
    }

    private function permittedLotIds(Request $request)
    {
        $user = $request->user();
        if (!$user) return collect();
        $fincaIds = $user->fincas()->pluck('fincas.IDFinca');
        return Lote::whereIn('IDFinca', $fincaIds)->pluck('IDLote');
    }

    public function index(Request $request)
    {
        $loteId = $request->input('lote');
        $tipoId = $request->input('tipo');
        $estado = $request->input('estado');
        $bornFrom = $request->input('born_from');
        $bornTo = $request->input('born_to');

        // Rango por defecto para gráficos si no hay filtros de nacimiento
        if (!$bornFrom || !$bornTo) {
            $bornFrom = $bornFrom ?: Carbon::now()->subDays(30)->toDateString();
            $bornTo = $bornTo ?: Carbon::now()->toDateString();
        }

        $query = Bird::with(['lote', 'tipoGallina']);

        // Scope for propietario context
        if ($this->isOwnerContext($request)) {
            $permittedLots = $this->permittedLotIds($request);
            $query->whereIn('IDLote', $permittedLots);
        }

        if ($loteId) { $query->where('IDLote', $loteId); }
        if ($tipoId) { $query->where('IDTipoGallina', $tipoId); }
        if ($estado) { $query->where('Estado', $estado); }
        if ($bornFrom) { $query->whereDate('FechaNacimiento', '>=', $bornFrom); }
        if ($bornTo) { $query->whereDate('FechaNacimiento', '<=', $bornTo); }

        $birds = $query->orderByDesc('IDGallina')->paginate(15)->withQueryString();

        // Stats
        $total = Bird::count();
        $byStatus = Bird::getBirdsByStatus();
        $recent = Bird::getRecentAcquisitions(7);
        // Conteo por tipo con nombre
        $byType = Bird::with('tipoGallina')
            ->select('IDTipoGallina', DB::raw('COUNT(*) as total'))
            ->groupBy('IDTipoGallina')
            ->get();

        // Serie: nacimientos por día en el rango
        $birthsSeries = Bird::select('FechaNacimiento as date', DB::raw('COUNT(*) as total'))
            ->whereBetween('FechaNacimiento', [$bornFrom, $bornTo])
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($tipoId, fn($q) => $q->where('IDTipoGallina', $tipoId))
            ->when($estado, fn($q) => $q->where('Estado', $estado))
            ->groupBy('FechaNacimiento')
            ->orderBy('FechaNacimiento')
            ->get();

        // Limit lotes list when propietario
        if ($this->isOwnerContext($request)) {
            $permittedLots = $this->permittedLotIds($request);
            $lotes = Lote::whereIn('IDLote', $permittedLots)->orderBy('Nombre')->get(['IDLote','Nombre']);
        } else {
            $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        }
        $tipos = TipoGallina::orderBy('Nombre')->get(['IDTipoGallina','Nombre']);

        return view('admin.aves.index', [
            'birds' => $birds,
            'lotes' => $lotes,
            'tipos' => $tipos,
            'filters' => [
                'lote' => $loteId,
                'tipo' => $tipoId,
                'estado' => $estado,
                'born_from' => $bornFrom,
                'born_to' => $bornTo,
            ],
            'stats' => [
                'total' => $total,
                'by_status' => $byStatus,
                'recent' => $recent,
                'by_type' => $byType,
            ],
            'charts' => [
                'births_series' => $birthsSeries,
                'status' => $byStatus,
                'types' => $byType,
            ],
        ]);
    }

    public function create()
    {
        $request = request();
        if ($this->isOwnerContext($request)) {
            $permittedLots = $this->permittedLotIds($request);
            $lotes = Lote::whereIn('IDLote', $permittedLots)->orderBy('Nombre')->get(['IDLote','Nombre']);
        } else {
            $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        }
        $tipos = TipoGallina::orderBy('Nombre')->get(['IDTipoGallina','Nombre']);
        $hoy = Carbon::now()->toDateString();

        return view('admin.aves.create', [
            'lotes' => $lotes,
            'tipos' => $tipos,
            'hoy' => $hoy,
        ]);
    }

    public function store(StoreBirdRequest $request)
    {
        try {
            $data = $request->validated();
            // If propietario, ensure target lot is permitted
            if ($this->isOwnerContext($request)) {
                $permittedLots = $this->permittedLotIds($request);
                abort_unless($permittedLots->contains((int)($data['IDLote'] ?? 0)), 403);
            }
            // Handle optional photo upload
            if ($request->hasFile('Foto')) {
                $path = $request->file('Foto')->store('birds', 'public');
                $data['UrlImagen'] = $path; // stored relative path in public disk
            }

            // Generate QR token
            $data['qr_token'] = (string) Str::uuid();

            $created = Bird::create($data);

            // Generate and store QR SVG on server (no GD/Imagick dependency)
            try {
                $qrUrl = route('admin.aves.show.byqr', $created->qr_token);
                $svg = QrCode::format('svg')->size(256)->margin(1)->generate($qrUrl);
                $qrPath = 'qrs/ave_'.$created->IDGallina.'_qr.svg';
                Storage::disk('public')->put($qrPath, $svg);
                $created->qr_image_path = $qrPath;
                $created->save();
            } catch (\Throwable $qe) {
                Log::warning('No se pudo generar PNG del QR', ['id' => $created->IDGallina, 'err' => $qe->getMessage()]);
            }

            return redirect()->route('admin.aves.show.byqr', $created->qr_token)
                ->with('success', 'Ave registrada correctamente. Aquí está su código QR.');
        } catch (\Throwable $e) {
            Log::error('Error creando ave', ['message' => $e->getMessage()]);
            $msg = 'No se pudo registrar el ave.';
            if (app()->isLocal()) {
                $msg .= ' Detalle: '.$e->getMessage();
            }
            return back()->withInput()->with('error', $msg);
        }
    }

    public function scan()
    {
        return view('admin.aves.scan');
    }

    public function showByQr(string $token)
    {
        $bird = Bird::with(['lote', 'tipoGallina'])->where('qr_token', $token)->first();
        
        if (!$bird) {
            return view('admin.aves.qr-not-found', compact('token'));
        }
        
        return view('admin.aves.show-by-qr', compact('bird'));
    }

    public function regenerateByQr(string $token)
    {
        $bird = Bird::where('qr_token', $token)->firstOrFail();
        try {
            $qrUrl = route('admin.aves.show.byqr', $bird->qr_token);
            $svg = QrCode::format('svg')->size(512)->margin(1)->generate($qrUrl);
            $qrPath = 'qrs/ave_'.$bird->IDGallina.'_qr.svg';
            Storage::disk('public')->put($qrPath, $svg);
            $bird->qr_image_path = $qrPath;
            $bird->save();
            return back()->with('success', 'QR regenerado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error regenerando QR', ['id' => $bird->IDGallina, 'err' => $e->getMessage()]);
            return back()->with('error', 'No se pudo regenerar el QR.');
        }
    }
    public function exportCsv(Request $request)
    {
        $loteId = $request->input('lote');
        $tipoId = $request->input('tipo');
        $estado = $request->input('estado');
        $bornFrom = $request->input('born_from');
        $bornTo = $request->input('born_to');

        $rows = Bird::with(['lote','tipoGallina'])
            ->when($this->isOwnerContext($request), function($q) use ($request) {
                $permitted = $this->permittedLotIds($request);
                $q->whereIn('IDLote', $permitted);
            })
            ->when($loteId, function($q) use ($loteId) { return $q->where('IDLote', $loteId); })
            ->when($tipoId, function($q) use ($tipoId) { return $q->where('IDTipoGallina', $tipoId); })
            ->when($estado, function($q) use ($estado) { return $q->where('Estado', $estado); })
            ->when($bornFrom, function($q) use ($bornFrom) { return $q->whereDate('FechaNacimiento', '>=', $bornFrom); })
            ->when($bornTo, function($q) use ($bornTo) { return $q->whereDate('FechaNacimiento', '<=', $bornTo); })
            ->orderBy('IDGallina')
            ->get(['IDGallina','IDLote','IDTipoGallina','FechaNacimiento','Estado']);

        $filename = 'aves_'.Carbon::now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 para compatibilidad con Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['ID','Lote','Tipo','FechaNacimiento','Estado']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->IDGallina,
                    optional($r->lote)->Nombre ?? $r->IDLote,
                    optional($r->tipoGallina)->Nombre ?? $r->IDTipoGallina,
                    $r->FechaNacimiento ? Carbon::parse($r->FechaNacimiento)->format('Y-m-d') : '',
                    $r->Estado,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateEstado(Request $request, \App\Models\Bird $bird)
    {
        try {
            // If propietario, only allow updating birds in permitted lots
            if ($this->isOwnerContext($request)) {
                $permittedLots = $this->permittedLotIds($request);
                abort_unless($permittedLots->contains((int) $bird->IDLote), 403);
            }

            $data = $request->validate([
                'Estado' => 'required|in:Activa,Muerta,Vendida',
            ]);

            $bird->Estado = $data['Estado'];
            $bird->save();

            return back()->with('success', 'Estado actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error actualizando estado de ave', ['id' => $bird->IDGallina, 'err' => $e->getMessage()]);
            return back()->with('error', 'No se pudo actualizar el estado.');
        }
    }

    public function destroy(Request $request, \App\Models\Bird $bird)
    {
        try {
            // Si es propietario, validar que el ave pertenezca a un lote permitido
            if ($this->isOwnerContext($request)) {
                $permittedLots = $this->permittedLotIds($request);
                abort_unless($permittedLots->contains((int) $bird->IDLote), 403);
            }

            // Guardar el ID del lote antes de eliminar
            $loteId = $bird->IDLote;

            // Eliminar imagen de QR si existe
            if (!empty($bird->qr_image_path)) {
                try {
                    Storage::disk('public')->delete($bird->qr_image_path);
                } catch (\Throwable $fe) {
                    Log::warning('No se pudo eliminar el archivo QR', ['path' => $bird->qr_image_path, 'err' => $fe->getMessage()]);
                }
            }

            // Eliminar imagen si existe
            if (!empty($bird->UrlImagen)) {
                try {
                    Storage::disk('public')->delete($bird->UrlImagen);
                } catch (\Throwable $fe) {
                    Log::warning('No se pudo eliminar la imagen del ave', ['path' => $bird->UrlImagen, 'err' => $fe->getMessage()]);
                }
            }

            $bird->delete();

            // Si viene del referer de lotes, redirigir al lote
            $referer = $request->headers->get('referer');
            if ($referer && str_contains($referer, '/lotes/')) {
                return redirect()->route('admin.lotes.show', $loteId)->with('success', 'Ave eliminada correctamente del lote.');
            }

            $route = $this->isOwnerContext($request) ? 'owner.aves.index' : 'admin.aves.index';
            return redirect()->route($route)->with('success', 'Ave eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error eliminando ave', ['id' => $bird->IDGallina, 'err' => $e->getMessage()]);
            return back()->with('error', 'No se pudo eliminar el ave.');
        }
    }
}
