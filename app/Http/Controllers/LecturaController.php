<?php

namespace App\Http\Controllers;

use App\Models\Lectura;
use App\Models\Medidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturaController extends Controller
{
    public function index()
    {
        $lecturas = Lectura::with('medidor', 'usuario')->paginate(10);
        return view('lecturas.index', compact('lecturas'));
    }

    public function create()
    {
        $medidores = Medidor::where('estado', 'activo')->get();
        return view('lecturas.create', compact('medidores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medidor_id' => 'required|exists:medidores,id',
            'lectura_anterior' => 'required|numeric|min:0',
            'lectura_actual' => 'required|numeric|gte:lectura_anterior',
            'observacion' => 'nullable|string',
            'fecha_lectura' => 'required|date',
        ]);

        // El consumo se calcula automáticamente
        $consumo = $request->lectura_actual - $request->lectura_anterior;

        Lectura::create([
            'medidor_id' => $request->medidor_id,
            'lectura_anterior' => $request->lectura_anterior,
            'lectura_actual' => $request->lectura_actual,
            'consumo' => $consumo,
            'observacion' => $request->observacion,
            'usuario_id' => Auth::id(),
            'fecha_lectura' => $request->fecha_lectura,
        ]);

        return redirect()->route('lecturas.index')->with('success', 'Lectura registrada correctamente.');
    }

    public function show(Lectura $lectura)
    {
        $lectura->load('medidor.socio', 'usuario');
        return view('lecturas.show', compact('lectura'));
    }

    public function edit(Lectura $lectura)
    {
        $medidores = Medidor::where('estado', 'activo')->get();
        return view('lecturas.edit', compact('lectura', 'medidores'));
    }

    public function update(Request $request, Lectura $lectura)
    {
        $request->validate([
            'medidor_id' => 'required|exists:medidores,id',
            'lectura_anterior' => 'required|numeric|min:0',
            'lectura_actual' => 'required|numeric|gte:lectura_anterior',
            'observacion' => 'nullable|string',
            'fecha_lectura' => 'required|date',
        ]);

        $consumo = $request->lectura_actual - $request->lectura_anterior;

        $lectura->update([
            'medidor_id' => $request->medidor_id,
            'lectura_anterior' => $request->lectura_anterior,
            'lectura_actual' => $request->lectura_actual,
            'consumo' => $consumo,
            'observacion' => $request->observacion,
            'fecha_lectura' => $request->fecha_lectura,
        ]);

        return redirect()->route('lecturas.index')->with('success', 'Lectura actualizada correctamente.');
    }

    public function destroy(Lectura $lectura)
    {
        $lectura->delete();
        return redirect()->route('lecturas.index')->with('success', 'Lectura eliminada correctamente.');
    }
}