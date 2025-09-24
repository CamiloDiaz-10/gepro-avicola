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
use Carbon\Carbon;

class BirdsController extends Controller
{
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

        $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
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
        $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
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
            Bird::create($data);

            return redirect()->route('admin.aves.index')
                ->with('success', 'Ave registrada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error creando ave', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'No se pudo registrar el ave.');
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
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($tipoId, fn($q) => $q->where('IDTipoGallina', $tipoId))
            ->when($estado, fn($q) => $q->where('Estado', $estado))
            ->when($bornFrom, fn($q) => $q->whereDate('FechaNacimiento', '>=', $bornFrom))
            ->when($bornTo, fn($q) => $q->whereDate('FechaNacimiento', '<=', $bornTo))
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
}
