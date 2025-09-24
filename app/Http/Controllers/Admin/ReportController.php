<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'finca' => $request->integer('finca'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ];

        $data = [
            'production' => $this->getProductionReport($filters),
            'feeding' => $this->getFeedingReport($filters),
            'health' => $this->getHealthReport($filters),
            'finance' => $this->getFinanceReport($filters),
            'filters' => $filters,
            'fincas' => Schema::hasTable('fincas') ? DB::table('fincas')->select('IDFinca','Nombre','Ubicacion')->orderBy('Nombre')->get() : collect(),
        ];

        return view('admin.reports.index', $data);
    }

    // EXPORTS
    public function exportProduction(Request $request): StreamedResponse
    {
        $rows = $this->getProductionReport($this->makeFilters($request))['daily'];
        return $this->streamCsv('produccion.csv', ['Fecha','CantidadHuevos'], function($out) use ($rows) {
            foreach ($rows as $r) fputcsv($out, [$r->date, $r->total]);
        });
    }

    public function exportFeeding(Request $request): StreamedResponse
    {
        $rows = $this->getFeedingReport($this->makeFilters($request))['by_type'];
        return $this->streamCsv('alimentacion.csv', ['TipoAlimento','CantidadKg'], function($out) use ($rows) {
            foreach ($rows as $r) fputcsv($out, [$r->feed_type, $r->total]);
        });
    }

    public function exportHealth(Request $request): StreamedResponse
    {
        $rows = $this->getHealthReport($this->makeFilters($request))['treatments'];
        return $this->streamCsv('salud.csv', ['Tratamiento','Total'], function($out) use ($rows) {
            foreach ($rows as $r) fputcsv($out, [$r->treatment, $r->total]);
        });
    }

    public function exportFinance(Request $request): StreamedResponse
    {
        $rows = $this->getFinanceReport($this->makeFilters($request))['movements'];
        return $this->streamCsv('finanzas.csv', ['Fecha','TipoMovimiento','Cantidad'], function($out) use ($rows) {
            foreach ($rows as $r) fputcsv($out, [$r->Fecha, $r->TipoMovimiento, $r->total]);
        });
    }

    // HELPERS
    private function makeFilters(Request $request): array
    {
        return [
            'finca' => $request->integer('finca'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ];
    }

    private function streamCsv(string $filename, array $headers, \Closure $writer): StreamedResponse
    {
        return response()->streamDownload(function() use ($headers, $writer) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            $writer($out);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    // REPORT QUERIES
    private function getProductionReport(array $filters): array
    {
        if (!Schema::hasTable('produccion_huevos')) return [
            'daily' => collect(),
            'by_lot' => collect(),
        ];

        $q = DB::table('produccion_huevos')->select('Fecha as date', DB::raw('SUM(CantidadHuevos) as total'));
        if ($filters['desde']) $q->where('Fecha', '>=', $filters['desde']);
        if ($filters['hasta']) $q->where('Fecha', '<=', $filters['hasta']);
        if ($filters['finca']) {
            $q->join('lotes','produccion_huevos.IDLote','=','lotes.IDLote')
              ->where('lotes.IDFinca', $filters['finca']);
        }
        $daily = $q->groupBy('Fecha')->orderBy('Fecha')->get();

        $byLot = DB::table('produccion_huevos as ph')
            ->join('lotes as l','ph.IDLote','=','l.IDLote')
            ->select('l.Nombre as lote', DB::raw('SUM(ph.CantidadHuevos) as total'))
            ->when($filters['desde'], fn($qq)=>$qq->where('ph.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($qq)=>$qq->where('ph.Fecha','<=',$filters['hasta']))
            ->when($filters['finca'], fn($qq)=>$qq->where('l.IDFinca',$filters['finca']))
            ->groupBy('l.Nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [ 'daily' => $daily, 'by_lot' => $byLot ];
    }

    private function getFeedingReport(array $filters): array
    {
        if (!Schema::hasTable('alimentacion')) return [
            'by_type' => collect(),
            'daily' => collect(),
        ];

        $byType = DB::table('alimentacion as a')
            ->join('tipo_alimentos as t','a.IDTipoAlimento','=','t.IDTipoAlimento')
            ->when($filters['desde'], fn($q)=>$q->where('a.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('a.Fecha','<=',$filters['hasta']))
            ->when($filters['finca'], function($q) use ($filters){
                $q->join('lotes as l','a.IDLote','=','l.IDLote')->where('l.IDFinca',$filters['finca']);
            })
            ->select('t.Nombre as feed_type', DB::raw('SUM(a.CantidadKg) as total'))
            ->groupBy('t.Nombre')
            ->orderByDesc('total')
            ->get();

        $daily = DB::table('alimentacion as a')
            ->when($filters['desde'], fn($q)=>$q->where('a.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('a.Fecha','<=',$filters['hasta']))
            ->when($filters['finca'], function($q) use ($filters){
                $q->join('lotes as l','a.IDLote','=','l.IDLote')->where('l.IDFinca',$filters['finca']);
            })
            ->select('a.Fecha as date', DB::raw('SUM(a.CantidadKg) as total'))
            ->groupBy('a.Fecha')
            ->orderBy('a.Fecha')
            ->get();

        return [ 'by_type' => $byType, 'daily' => $daily ];
    }

    private function getHealthReport(array $filters): array
    {
        if (!Schema::hasTable('sanidad')) return [
            'treatments' => collect(),
            'recent' => collect(),
        ];

        $treatments = DB::table('sanidad as s')
            ->when($filters['desde'], fn($q)=>$q->where('s.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('s.Fecha','<=',$filters['hasta']))
            ->when($filters['finca'], function($q) use ($filters){
                $q->join('lotes as l','s.IDLote','=','l.IDLote')->where('l.IDFinca',$filters['finca']);
            })
            ->select('s.TipoTratamiento as treatment', DB::raw('COUNT(*) as total'))
            ->groupBy('s.TipoTratamiento')
            ->orderByDesc('total')
            ->get();

        $recent = DB::table('sanidad as s')
            ->join('lotes as l','s.IDLote','=','l.IDLote')
            ->select('l.Nombre as lote','s.TipoTratamiento','s.Fecha')
            ->when($filters['finca'], fn($q)=>$q->where('l.IDFinca',$filters['finca']))
            ->orderByDesc('s.Fecha')
            ->limit(10)
            ->get();

        return [ 'treatments' => $treatments, 'recent' => $recent ];
    }

    private function getFinanceReport(array $filters): array
    {
        if (!Schema::hasTable('movimiento_lote')) return [
            'movements' => collect(),
            'totals' => ['ventas'=>0,'compras'=>0],
        ];

        $movements = DB::table('movimiento_lote as m')
            ->join('lotes as l','m.IDLote','=','l.IDLote')
            ->when($filters['desde'], fn($q)=>$q->where('m.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('m.Fecha','<=',$filters['hasta']))
            ->when($filters['finca'], fn($q)=>$q->where('l.IDFinca',$filters['finca']))
            ->select('m.Fecha','m.TipoMovimiento', DB::raw('COUNT(*) as total'))
            ->groupBy('m.Fecha','m.TipoMovimiento')
            ->orderBy('m.Fecha')
            ->get();

        $ventas = $movements->where('TipoMovimiento','Venta')->sum('total');
        $compras = $movements->where('TipoMovimiento','Compra')->sum('total');

        return [ 'movements' => $movements, 'totals' => ['ventas'=>$ventas, 'compras'=>$compras] ];
    }
}
