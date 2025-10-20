<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoteApiController extends Controller
{
    /**
     * Obtener información de producción esperada para un lote
     */
    public function getProduccionInfo($loteId): JsonResponse
    {
        try {
            $lote = Lote::findOrFail($loteId);
            
            $avesActivas = $lote->aves_activas_count;
            
            if ($avesActivas == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El lote seleccionado no tiene aves activas.',
                    'data' => null
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'lote_id' => $lote->IDLote,
                    'lote_nombre' => $lote->Nombre,
                    'aves_activas' => $avesActivas,
                    'produccion_minima' => $lote->produccion_minima_esperada,
                    'produccion_promedio' => $lote->produccion_promedio_esperada,
                    'produccion_maxima' => $lote->produccion_maxima_esperada,
                    'huevos_por_ave_min' => 3,
                    'huevos_por_ave_promedio' => 4,
                    'huevos_por_ave_max' => 5
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del lote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar cantidad de huevos para un lote
     */
    public function validarCantidad(Request $request): JsonResponse
    {
        try {
            $loteId = $request->input('lote_id');
            $cantidad = $request->input('cantidad');

            if (!$loteId || !is_numeric($cantidad)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos'
                ], 422);
            }

            $lote = Lote::findOrFail($loteId);
            $validacion = $lote->validarCantidadHuevos((int)$cantidad);

            return response()->json([
                'success' => true,
                'valido' => $validacion['valido'],
                'mensaje' => $validacion['mensaje'],
                'aves_activas' => $lote->aves_activas_count,
                'maximo_permitido' => $lote->produccion_maxima_esperada
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar cantidad: ' . $e->getMessage()
            ], 500);
        }
    }
}
