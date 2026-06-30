<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Socio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function ingresos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        $ingresos = Pago::select(
                DB::raw('YEAR(fecha_pago) as year'),
                DB::raw('MONTH(fecha_pago) as month'),
                DB::raw('SUM(monto) as total_ingresos'),
                DB::raw('COUNT(*) as pagos_registrados')
            )
            ->when($validated['desde'] ?? null, fn($query, $value) => $query->whereDate('fecha_pago', '>=', $value))
            ->when($validated['hasta'] ?? null, fn($query, $value) => $query->whereDate('fecha_pago', '<=', $value))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn($row) => [
                'year' => (int) $row->year,
                'month' => (int) $row->month,
                'total_ingresos' => (float) $row->total_ingresos,
                'pagos_registrados' => (int) $row->pagos_registrados,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte de ingresos generado correctamente.',
            'data' => [
                'periodo' => [
                    'desde' => $validated['desde'] ?? null,
                    'hasta' => $validated['hasta'] ?? null,
                ],
                'ingresos' => $ingresos,
                'total_general' => $ingresos->sum('total_ingresos'),
            ],
        ]);
    }

    public function deudores(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hasta' => 'nullable|date',
        ]);

        $deudores = Socio::select(
                'socios.id',
                'socios.nombres',
                'socios.apellidos',
                'socios.ci',
                'socios.telefono',
                DB::raw('SUM(facturas.monto_total) as total_facturado'),
                DB::raw('COALESCE(SUM(pagos.monto), 0) as total_pagado'),
                DB::raw('SUM(facturas.monto_total) - COALESCE(SUM(pagos.monto), 0) as total_deuda')
            )
            ->join('facturas', 'socios.id', '=', 'facturas.socio_id')
            ->leftJoin('pagos', 'facturas.id', '=', 'pagos.factura_id')
            ->when($validated['hasta'] ?? null, fn($query, $value) => $query->whereDate('facturas.fecha_vencimiento', '<=', $value))
            ->groupBy('socios.id', 'socios.nombres', 'socios.apellidos', 'socios.ci', 'socios.telefono')
            ->havingRaw('SUM(facturas.monto_total) - COALESCE(SUM(pagos.monto), 0) > 0')
            ->orderByDesc('total_deuda')
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'nombres' => $row->nombres,
                'apellidos' => $row->apellidos,
                'ci' => $row->ci,
                'telefono' => $row->telefono,
                'total_facturado' => (float) $row->total_facturado,
                'total_pagado' => (float) $row->total_pagado,
                'total_deuda' => (float) $row->total_deuda,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte de deudores generado correctamente.',
            'data' => [
                'filtro_hasta' => $validated['hasta'] ?? null,
                'deudores' => $deudores,
            ],
        ]);
    }

    public function consumo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        $consumo = Socio::select(
                'socios.id',
                'socios.nombres',
                'socios.apellidos',
                'socios.ci',
                DB::raw('SUM(lecturas.consumo) as total_consumo'),
                DB::raw('MIN(lecturas.fecha_lectura) as periodo_inicio'),
                DB::raw('MAX(lecturas.fecha_lectura) as periodo_fin')
            )
            ->join('medidores', 'socios.id', '=', 'medidores.socio_id')
            ->join('lecturas', 'medidores.id', '=', 'lecturas.medidor_id')
            ->when($validated['desde'] ?? null, fn($query, $value) => $query->whereDate('lecturas.fecha_lectura', '>=', $value))
            ->when($validated['hasta'] ?? null, fn($query, $value) => $query->whereDate('lecturas.fecha_lectura', '<=', $value))
            ->groupBy('socios.id', 'socios.nombres', 'socios.apellidos', 'socios.ci')
            ->orderByDesc('total_consumo')
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'nombres' => $row->nombres,
                'apellidos' => $row->apellidos,
                'ci' => $row->ci,
                'total_consumo' => (float) $row->total_consumo,
                'periodo_inicio' => $row->periodo_inicio,
                'periodo_fin' => $row->periodo_fin,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte de consumo generado correctamente.',
            'data' => [
                'periodo' => [
                    'desde' => $validated['desde'] ?? null,
                    'hasta' => $validated['hasta'] ?? null,
                ],
                'consumo' => $consumo,
            ],
        ]);
    }
}
