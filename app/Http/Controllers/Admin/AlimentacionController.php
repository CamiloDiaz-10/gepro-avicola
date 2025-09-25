<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAlimentacionRequest;
use App\Models\Alimentacion;
use App\Models\Lote;
use App\Models\TipoAlimento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlimentacionController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', Carbon::now()->subDays(7)->toDateString());
        $to = $request->input('to', Carbon::now()->toDateString());
        $loteId = $request->input('lote');
        $tipoId = $request->input('tipo');

        $query = Alimentacion::with(['lote','tipoAlimento'])
            ->whereBetween('Fecha', [$from, $to]);
        if ($loteId) { $query->where('IDLote', $loteId); }
        if ($tipoId) { $query->where('IDTipoAlimento', $tipoId); }

        $registros = $query->orderByDesc('Fecha')->paginate(15)->withQueryString();

        // Totales (solo cantidad, no hay precio en tipo_alimentos)
        $totales = Alimentacion::whereBetween('Fecha', [$from, $to])
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($tipoId, fn($q) => $q->where('IDTipoAlimento', $tipoId))
            ->select(DB::raw('SUM(CantidadKg) as total_cantidad'))
            ->first();

        // Series para gráficos pequeños
        $serieDiaria = Alimentacion::select('Fecha', DB::raw('SUM(CantidadKg) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($tipoId, fn($q) => $q->where('IDTipoAlimento', $tipoId))
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();

        $porTipo = Alimentacion::with('tipoAlimento')
            ->select('IDTipoAlimento', DB::raw('SUM(CantidadKg) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->groupBy('IDTipoAlimento')
            ->orderByDesc('total')
            ->get();

        $porLote = Alimentacion::with('lote')
            ->select('IDLote', DB::raw('SUM(CantidadKg) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($tipoId, fn($q) => $q->where('IDTipoAlimento', $tipoId))
            ->groupBy('IDLote')
            ->orderByDesc('total')
            ->get();

        // No hay cálculo de costo ni inventario por falta de columnas en migración

        $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        $tipos = TipoAlimento::orderBy('Nombre')->get(['IDTipoAlimento','Nombre']);

        return view('admin.alimentacion.index', [
            'registros' => $registros,
            'lotes' => $lotes,
            'tipos' => $tipos,
            'filters' => compact('from','to','loteId','tipoId'),
            'totales' => $totales,
            'charts' => [
                'daily' => $serieDiaria,
                'by_type' => $porTipo,
                'by_lote' => $porLote,
            ],
        ]);
    }

    public function create()
    {
        $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        $tipos = TipoAlimento::orderBy('Nombre')->get(['IDTipoAlimento','Nombre']);
        $hoy = Carbon::now()->toDateString();

        return view('admin.alimentacion.create', compact('lotes','tipos','hoy'));
    }

    public function store(StoreAlimentacionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['Fecha'] = $data['Fecha'] ?? Carbon::now()->toDateString();
            $data['IDUsuario'] = auth()->id();
            Alimentacion::create($data);
            return redirect()->route('admin.alimentacion.index')->with('success','Registro de alimentación guardado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error guardando alimentación', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error','Ocurrió un error al guardar.');
        }
    }

    public function exportCsv(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $loteId = $request->input('lote');
        $tipoId = $request->input('tipo');

        $rows = Alimentacion::with(['lote','tipoAlimento'])
            ->when($from, fn($q) => $q->whereDate('Fecha','>=',$from))
            ->when($to, fn($q) => $q->whereDate('Fecha','<=',$to))
            ->when($loteId, fn($q) => $q->where('IDLote',$loteId))
            ->when($tipoId, fn($q) => $q->where('IDTipoAlimento',$tipoId))
            ->orderBy('Fecha')
            ->get(['Fecha','IDLote','IDTipoAlimento','CantidadKg','Observaciones']);

        $filename = 'alimentacion_'.Carbon::now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($rows) {
            $out = fopen('php://output','w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Fecha','Lote','TipoAlimento','CantidadKg','Observaciones']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    Carbon::parse($r->Fecha)->format('Y-m-d'),
                    optional($r->lote)->Nombre ?? $r->IDLote,
                    optional($r->tipoAlimento)->Nombre ?? $r->IDTipoAlimento,
                    $r->CantidadKg,
                    $r->Observaciones,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
