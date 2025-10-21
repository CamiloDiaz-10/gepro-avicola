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
            $lote = Lote::with('gallinas.tipoGallina')->findOrFail($loteId);
            
            // Verificar si es lote de engorde (no producen huevos)
            if ($lote->es_lote_de_engorde) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lote es de aves de engorde y no producen huevos.',
                    'data' => null,
                    'es_engorde' => true
                ], 422);
            }
            
            $avesActivas = $lote->aves_activas_count;
            
            if ($avesActivas == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El lote seleccionado no tiene aves activas.',
                    'data' => null
                ], 422);
            }

            $tipoPredominante = $lote->tipo_predominante;
            $nombreTipo = $tipoPredominante ? $tipoPredominante->Nombre : 'Mixto';
            
            // Obtener promedios según tipo
            $promedios = $this->getPromediosPorTipo($nombreTipo);

            return response()->json([
                'success' => true,
                'data' => [
                    'lote_id' => $lote->IDLote,
                    'lote_nombre' => $lote->Nombre,
                    'tipo_gallina' => $nombreTipo,
                    'aves_activas' => $avesActivas,
                    'produccion_minima' => $lote->produccion_minima_esperada,
                    'produccion_promedio' => $lote->produccion_promedio_esperada,
                    'produccion_maxima' => $lote->produccion_maxima_esperada,
                    'huevos_por_ave_min' => $promedios['min'],
                    'huevos_por_ave_promedio' => $promedios['promedio'],
                    'huevos_por_ave_max' => $promedios['max']
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
     * Obtener promedios de producción según tipo de gallina
     */
    private function getPromediosPorTipo($tipoNombre)
    {
        switch ($tipoNombre) {
            case 'Ponedora':
                // Ponedoras: 5-7 huevos por día por ave (según requerimiento)
                return ['min' => 5.0, 'promedio' => 6.0, 'max' => 7.0];
            
            case 'Criolla':
                // Criollas: 0.4 - 0.7 huevos por día
                return ['min' => 0.4, 'promedio' => 0.55, 'max' => 0.7];
            
            case 'Doble Propósito':
                return ['min' => 0.7, 'promedio' => 0.8, 'max' => 0.9];
            
            case 'Reproductora':
                return ['min' => 0.6, 'promedio' => 0.8, 'max' => 1.0];
            
            case 'Engorde':
                return ['min' => 0, 'promedio' => 0, 'max' => 0];
            
            default:
                return ['min' => 0.5, 'promedio' => 0.8, 'max' => 1.0];
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
