<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\Request;

class SocioController extends Controller
{
    public function index() {
        $socios = Socio::paginate(10);
        return view('socios.index', compact('socios'));
    }

    public function create() {
        return view('socios.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula'   => 'required|string|unique:socios',
        ]);
        Socio::create($request->all());
        return redirect()->route('socios.index')->with('success', 'Socio creado.');
    }

    public function edit(Socio $socio) {
        return view('socios.edit', compact('socio'));
    }

    public function update(Request $request, Socio $socio) {
        $request->validate([
            'cedula' => 'required|string|unique:socios,cedula,' . $socio->id,
        ]);
        $socio->update($request->all());
        return redirect()->route('socios.index')->with('success', 'Socio actualizado.');
    }

    public function destroy(Socio $socio) {
        $socio->delete();
        return redirect()->route('socios.index')->with('success', 'Socio eliminado.');
    }
}