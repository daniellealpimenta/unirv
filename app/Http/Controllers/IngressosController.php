<?php

namespace App\Http\Controllers;

use App\Models\Ingresso;
use Illuminate\Http\Request;

class IngressosController extends Controller
{
    
    public function store(Request $request, $loteid)
    {
        $request->validate([
            'valor' => ['required', 'numeric'],
            'nome_evento' => ['required', 'string', 'max:255'],
            'data_validade' => ['required', 'date'],
        ]);

        if($request->data_validade < date('Y-m-d')) {
            return response()->json([
                "error" => "Data inválida"
            ], 400);
        }

        $ingresso = Ingresso::create([
            'valor' => $request->valor,
            'nome_evento' => $request->nome_evento,
            'data_validade' => $request->data_validade,
            'lote_id' => $loteid,
        ]);

        return response()->json($ingresso, 201);
    }

    public function show(){
        $ingressos = Ingresso::all();

        return response()->json($ingressos);
    }

    public function update(Request $request, $ingressoid)
    {
        $request->validate([
            'valor' => ['required', 'numeric'],
            'nome_evento' => ['required', 'string', 'max:255'],
            'data_validade' => ['required', 'date'],
        ]);

        $ingresso = Ingresso::find($ingressoid);
        $ingresso->valor = $request->valor;
        $ingresso->nome_evento = $request->nome_evento;
        $ingresso->data_validade = $request->data_validade;
        $ingresso->save();

        return response()->json($ingresso);
    }

    public function enable($ingressoid)
    {
        $ingresso = Ingresso::find($ingressoid);
        $ingresso->disponivel = true;
        $ingresso->save();

        return response()->json($ingresso);
    }

    public function disable($ingressoid)
    {
        $ingresso = Ingresso::find($ingressoid);
        $ingresso->disponivel = false;
        $ingresso->save();    

        return response()->json($ingresso);
    }

    public function destroy($ingressoid)
    {
        $ingresso = Ingresso::find($ingressoid);
        $ingresso->delete();

        return response()->json(null, 204);
    }
}
