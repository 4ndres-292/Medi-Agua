<?php

namespace App\Http\Controllers;

use App\Models\Medidor;
use App\Models\Socio;
use Illuminate\Http\Request;

class MedidorController extends Controller
{
    public function index()
    {
        $medidores = Medidor::with('socio')->paginate(10);
        return view('medidores.index', compact('medidores'));
    }

    public function create()
    {
        $socios = Socio::all();
        return view('medidores.create', compact('socios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:medidores',
            'ubicacion' => 'required|string|max:255',
            'socio_id' => 'required|exists:socios,id',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        Medidor::create($request->all());
        return redirect()->route('medidores.index')->with('success', 'Medidor creado correctamente.');
    }

    public function show(Medidor $medidor)
    {
        $medidor->load('socio', 'lecturas');
        return view('medidores.show', compact('medidor'));
    }

    public function edit(Medidor $medidor)
    {
        $socios = Socio::all();
        return view('medidores.edit', compact('medidor', 'socios'));
    }

    public function update(Request $request, Medidor $medidor)
    {
        $request->validate([
            'codigo' => 'required|string|unique:medidores,codigo,' . $medidor->id,
            'ubicacion' => 'required|string|max:255',
            'socio_id' => 'required|exists:socios,id',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        $medidor->update($request->all());
        return redirect()->route('medidores.index')->with('success', 'Medidor actualizado correctamente.');
    }

    public function destroy(Medidor $medidor)
    {
        $medidor->delete();
        return redirect()->route('medidores.index')->with('success', 'Medidor eliminado correctamente.');
    }
}