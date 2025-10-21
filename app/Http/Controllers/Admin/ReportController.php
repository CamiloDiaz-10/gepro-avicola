<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController extends Controller
{
    private function isOwnerContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('owner.*')) || ($user && $user->role && $user->role->NombreRol === 'Propietario');
    }

    private function userFincaIds(Request $request)
    {
        return $request->user() ? $request->user()->fincas()->pluck('fincas.IDFinca') : collect();
    }

    public function index(Request $request)
    {
        $filters = [
            'finca' => $request->integer('finca'),
            'lote' => $request->integer('lote'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ];
        $ownerFincas = $this->isOwnerContext($request) ? $this->userFincaIds($request) : null;
        if ($ownerFincas && $filters['finca'] && !$ownerFincas->contains($filters['finca'])) {
            $filters['finca'] = null; // evitar filtrar por una finca no permitida
        }

        // Obtener lotes según finca seleccionada o fincas del usuario
        $lotes = collect();
        if (Schema::hasTable('lotes')) {
            if ($filters['finca']) {
                $lotes = DB::table('lotes')->where('IDFinca', $filters['finca'])
                    ->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
            } elseif ($ownerFincas) {
                $lotes = DB::table('lotes')->whereIn('IDFinca', $ownerFincas)
                    ->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
            } else {
                $lotes = DB::table('lotes')->select('IDLote', 'Nombre')->orderBy('Nombre')->get();
            }
        }

        $data = [
            'production' => $this->getProductionReport($filters, $ownerFincas),
            'feeding' => $this->getFeedingReport($filters, $ownerFincas),
            'health' => $this->getHealthReport($filters, $ownerFincas),
            'finance' => $this->getFinanceReport($filters, $ownerFincas),
            'filters' => $filters,
            'fincas' => Schema::hasTable('fincas')
                ? ($ownerFincas
                    ? DB::table('fincas')->whereIn('IDFinca', $ownerFincas)->select('IDFinca','Nombre','Ubicacion')->orderBy('Nombre')->get()
                    : DB::table('fincas')->select('IDFinca','Nombre','Ubicacion')->orderBy('Nombre')->get())
                : collect(),
            'lotes' => $lotes,
        ];

        return view('admin.reports.index', $data);
    }

    // EXPORTS TO EXCEL
    public function exportProduction(Request $request)
    {
        $ownerFincas = $this->isOwnerContext($request) ? $this->userFincaIds($request) : null;
        $data = $this->getProductionReport($this->makeFilters($request), $ownerFincas);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Producción Huevos');
        
        // Headers
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Cantidad Huevos');
        $this->styleHeader($sheet, 'A1:B1');
        
        // Data
        $row = 2;
        foreach ($data['daily'] as $item) {
            $sheet->setCellValue('A'.$row, $item->date);
            $sheet->setCellValue('B'.$row, $item->total);
            $row++;
        }
        
        // Top Lotes
        $sheet->setCellValue('D1', 'Top 10 Lotes');
        $sheet->setCellValue('E1', 'Total Producción');
        $this->styleHeader($sheet, 'D1:E1');
        
        $row = 2;
        foreach ($data['by_lot'] as $item) {
            $sheet->setCellValue('D'.$row, $item->lote);
            $sheet->setCellValue('E'.$row, $item->total);
            $row++;
        }
        
        $this->autoSizeColumns($sheet, ['A', 'B', 'D', 'E']);
        return $this->downloadExcel($spreadsheet, 'Reporte_Produccion_'.date('Y-m-d').'.xlsx');
    }

    public function exportFeeding(Request $request)
    {
        $ownerFincas = $this->isOwnerContext($request) ? $this->userFincaIds($request) : null;
        $data = $this->getFeedingReport($this->makeFilters($request), $ownerFincas);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Alimentación');
        
        // Headers por tipo
        $sheet->setCellValue('A1', 'Tipo de Alimento');
        $sheet->setCellValue('B1', 'Cantidad (Kg)');
        $this->styleHeader($sheet, 'A1:B1');
        
        // Data
        $row = 2;
        foreach ($data['by_type'] as $item) {
            $sheet->setCellValue('A'.$row, $item->feed_type);
            $sheet->setCellValue('B'.$row, $item->total);
            $row++;
        }
        
        // Consumo diario
        $sheet->setCellValue('D1', 'Fecha');
        $sheet->setCellValue('E1', 'Kg Consumidos');
        $this->styleHeader($sheet, 'D1:E1');
        
        $row = 2;
        foreach ($data['daily'] as $item) {
            $sheet->setCellValue('D'.$row, $item->date);
            $sheet->setCellValue('E'.$row, $item->total);
            $row++;
        }
        
        $this->autoSizeColumns($sheet, ['A', 'B', 'D', 'E']);
        return $this->downloadExcel($spreadsheet, 'Reporte_Alimentacion_'.date('Y-m-d').'.xlsx');
    }

    public function exportHealth(Request $request)
    {
        $ownerFincas = $this->isOwnerContext($request) ? $this->userFincaIds($request) : null;
        $data = $this->getHealthReport($this->makeFilters($request), $ownerFincas);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Salud');
        
        // Headers tratamientos
        $sheet->setCellValue('A1', 'Tipo de Tratamiento');
        $sheet->setCellValue('B1', 'Total Aplicaciones');
        $this->styleHeader($sheet, 'A1:B1');
        
        // Data
        $row = 2;
        foreach ($data['treatments'] as $item) {
            $sheet->setCellValue('A'.$row, $item->treatment);
            $sheet->setCellValue('B'.$row, $item->total);
            $row++;
        }
        
        // Tratamientos recientes
        $sheet->setCellValue('D1', 'Lote');
        $sheet->setCellValue('E1', 'Tratamiento');
        $sheet->setCellValue('F1', 'Fecha');
        $this->styleHeader($sheet, 'D1:F1');
        
        $row = 2;
        foreach ($data['recent'] as $item) {
            $sheet->setCellValue('D'.$row, $item->lote);
            $sheet->setCellValue('E'.$row, $item->TipoTratamiento);
            $sheet->setCellValue('F'.$row, $item->Fecha);
            $row++;
        }
        
        $this->autoSizeColumns($sheet, ['A', 'B', 'D', 'E', 'F']);
        return $this->downloadExcel($spreadsheet, 'Reporte_Salud_'.date('Y-m-d').'.xlsx');
    }

    public function exportFinance(Request $request)
    {
        $ownerFincas = $this->isOwnerContext($request) ? $this->userFincaIds($request) : null;
        $data = $this->getFinanceReport($this->makeFilters($request), $ownerFincas);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Finanzas');
        
        // Headers
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Tipo de Movimiento');
        $sheet->setCellValue('C1', 'Cantidad');
        $this->styleHeader($sheet, 'A1:C1');
        
        // Data
        $row = 2;
        foreach ($data['movements'] as $item) {
            $sheet->setCellValue('A'.$row, $item->Fecha);
            $sheet->setCellValue('B'.$row, $item->TipoMovimiento);
            $sheet->setCellValue('C'.$row, $item->total);
            $row++;
        }
        
        // Totales
        $sheet->setCellValue('E1', 'Resumen');
        $sheet->setCellValue('F1', 'Total');
        $this->styleHeader($sheet, 'E1:F1');
        
        $sheet->setCellValue('E2', 'Total Ventas');
        $sheet->setCellValue('F2', $data['totals']['ventas']);
        $sheet->setCellValue('E3', 'Total Compras');
        $sheet->setCellValue('F3', $data['totals']['compras']);
        
        $this->autoSizeColumns($sheet, ['A', 'B', 'C', 'E', 'F']);
        return $this->downloadExcel($spreadsheet, 'Reporte_Finanzas_'.date('Y-m-d').'.xlsx');
    }

    // HELPERS
    private function makeFilters(Request $request): array
    {
        return [
            'finca' => $request->integer('finca'),
            'lote' => $request->integer('lote'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ];
    }

    private function styleHeader($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'] // Blue 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    private function autoSizeColumns($sheet, array $columns)
    {
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // REPORT QUERIES
    private function getProductionReport(array $filters, $ownerFincas = null): array
    {
        if (!Schema::hasTable('produccion_huevos')) return [
            'daily' => collect(),
            'by_lot' => collect(),
        ];

        $q = DB::table('produccion_huevos')->select('Fecha as date', DB::raw('SUM(CantidadHuevos) as total'));
        if ($filters['desde']) $q->where('Fecha', '>=', $filters['desde']);
        if ($filters['hasta']) $q->where('Fecha', '<=', $filters['hasta']);
        if ($filters['lote']) {
            $q->where('produccion_huevos.IDLote', $filters['lote']);
        } elseif ($filters['finca'] || $ownerFincas) {
            $q->join('lotes','produccion_huevos.IDLote','=','lotes.IDLote');
            if ($filters['finca']) $q->where('lotes.IDFinca', $filters['finca']);
            if ($ownerFincas) $q->whereIn('lotes.IDFinca', $ownerFincas);
        }
        $daily = $q->groupBy('Fecha')->orderBy('Fecha')->get();

        $byLot = DB::table('produccion_huevos as ph')
            ->join('lotes as l','ph.IDLote','=','l.IDLote')
            ->select('l.Nombre as lote', DB::raw('SUM(ph.CantidadHuevos) as total'))
            ->when($filters['desde'], fn($qq)=>$qq->where('ph.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($qq)=>$qq->where('ph.Fecha','<=',$filters['hasta']))
            ->when($filters['lote'], fn($qq)=>$qq->where('ph.IDLote',$filters['lote']))
            ->when($filters['finca'], fn($qq)=>$qq->where('l.IDFinca',$filters['finca']))
            ->when($ownerFincas, fn($qq)=>$qq->whereIn('l.IDFinca', $ownerFincas))
            ->groupBy('l.Nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [ 'daily' => $daily, 'by_lot' => $byLot ];
    }

    private function getFeedingReport(array $filters, $ownerFincas = null): array
    {
        if (!Schema::hasTable('alimentacion')) return [
            'by_type' => collect(),
            'daily' => collect(),
        ];

        $byType = DB::table('alimentacion as a')
            ->join('tipo_alimentos as t','a.IDTipoAlimento','=','t.IDTipoAlimento')
            ->when($filters['desde'], fn($q)=>$q->where('a.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('a.Fecha','<=',$filters['hasta']))
            ->when($filters['lote'], fn($q)=>$q->where('a.IDLote',$filters['lote']))
            ->when($filters['finca'] || $ownerFincas, function($q) use ($filters, $ownerFincas){
                $q->join('lotes as l','a.IDLote','=','l.IDLote');
                if ($filters['finca']) $q->where('l.IDFinca',$filters['finca']);
                if ($ownerFincas) $q->whereIn('l.IDFinca',$ownerFincas);
            })
            ->select('t.Nombre as feed_type', DB::raw('SUM(a.CantidadKg) as total'))
            ->groupBy('t.Nombre')
            ->orderByDesc('total')
            ->get();

        $daily = DB::table('alimentacion as a')
            ->when($filters['desde'], fn($q)=>$q->where('a.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('a.Fecha','<=',$filters['hasta']))
            ->when($filters['lote'], fn($q)=>$q->where('a.IDLote',$filters['lote']))
            ->when($filters['finca'] || $ownerFincas, function($q) use ($filters, $ownerFincas){
                $q->join('lotes as l','a.IDLote','=','l.IDLote');
                if ($filters['finca']) $q->where('l.IDFinca',$filters['finca']);
                if ($ownerFincas) $q->whereIn('l.IDFinca',$ownerFincas);
            })
            ->select('a.Fecha as date', DB::raw('SUM(a.CantidadKg) as total'))
            ->groupBy('a.Fecha')
            ->orderBy('a.Fecha')
            ->get();

        return [ 'by_type' => $byType, 'daily' => $daily ];
    }

    private function getHealthReport(array $filters, $ownerFincas = null): array
    {
        if (!Schema::hasTable('sanidad')) return [
            'treatments' => collect(),
            'recent' => collect(),
        ];

        $treatments = DB::table('sanidad as s')
            ->when($filters['desde'], fn($q)=>$q->where('s.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('s.Fecha','<=',$filters['hasta']))
            ->when($filters['lote'], fn($q)=>$q->where('s.IDLote',$filters['lote']))
            ->when($filters['finca'] || $ownerFincas, function($q) use ($filters, $ownerFincas){
                $q->join('lotes as l','s.IDLote','=','l.IDLote');
                if ($filters['finca']) $q->where('l.IDFinca',$filters['finca']);
                if ($ownerFincas) $q->whereIn('l.IDFinca',$ownerFincas);
            })
            ->select('s.TipoTratamiento as treatment', DB::raw('COUNT(*) as total'))
            ->groupBy('s.TipoTratamiento')
            ->orderByDesc('total')
            ->get();

        $recent = DB::table('sanidad as s')
            ->join('lotes as l','s.IDLote','=','l.IDLote')
            ->select('l.Nombre as lote','s.TipoTratamiento','s.Fecha')
            ->when($filters['lote'], fn($q)=>$q->where('s.IDLote',$filters['lote']))
            ->when($filters['finca'], fn($q)=>$q->where('l.IDFinca',$filters['finca']))
            ->when($ownerFincas, fn($q)=>$q->whereIn('l.IDFinca',$ownerFincas))
            ->orderByDesc('s.Fecha')
            ->limit(10)
            ->get();

        return [ 'treatments' => $treatments, 'recent' => $recent ];
    }

    private function getFinanceReport(array $filters, $ownerFincas = null): array
    {
        if (!Schema::hasTable('movimiento_lote')) return [
            'movements' => collect(),
            'totals' => ['ventas'=>0,'compras'=>0],
        ];

        $movements = DB::table('movimiento_lote as m')
            ->join('lotes as l','m.IDLote','=','l.IDLote')
            ->when($filters['desde'], fn($q)=>$q->where('m.Fecha','>=',$filters['desde']))
            ->when($filters['hasta'], fn($q)=>$q->where('m.Fecha','<=',$filters['hasta']))
            ->when($filters['lote'], fn($q)=>$q->where('m.IDLote',$filters['lote']))
            ->when($filters['finca'], fn($q)=>$q->where('l.IDFinca',$filters['finca']))
            ->when($ownerFincas, fn($q)=>$q->whereIn('l.IDFinca',$ownerFincas))
            ->select('m.Fecha','m.TipoMovimiento', DB::raw('COUNT(*) as total'))
            ->groupBy('m.Fecha','m.TipoMovimiento')
            ->orderBy('m.Fecha')
            ->get();

        $ventas = $movements->where('TipoMovimiento','Venta')->sum('total');
        $compras = $movements->where('TipoMovimiento','Compra')->sum('total');

        return [ 'movements' => $movements, 'totals' => ['ventas'=>$ventas, 'compras'=>$compras] ];
    }
}
