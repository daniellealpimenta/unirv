<?php

namespace App\Http\Controllers;

use App\Models\Ingresso;
use Illuminate\Http\Request;

class IngressosController extends Controller
{
    
    public function store(Request $request, $loteid)
    {
        $request->validate([
            'valor_aluno' => ['required', 'numeric'],
            'valor_externo' => ['required', 'numeric'],
            'nome_evento' => ['required', 'string', 'max:255'],
            'data_validade' => ['required', 'date'],
            'quantidade' => ['required', 'integer']
        ]);

        if($request->data_validade < date('Y-m-d')) {
            return response()->json([
                "error" => "Data inválida"
            ], 400);
        }

        while($request->quantidade > 0) {
            $ingresso = Ingresso::create([
                'valor_aluno' => $request->valor_aluno,
                'valor_externo' => $request->valor_externo,
                'nome_evento' => $request->nome_evento,
                'data_validade' => $request->data_validade,
                'lote_id' => $loteid,
            ]);
            $request->quantidade--;
        }

        return response()->json($ingresso, 201);
    }

    public function show(){
        $ingressos = Ingresso::all();

        return response()->json($ingressos);
    }

    public function update(Request $request, $loteid)
    {
        $request->validate([
            'valor_aluno' => ['required', 'numeric'],
            'valor_externo' => ['required', 'numeric'],
            'nome_evento' => ['required', 'string', 'max:255'],
            'data_validade' => ['required', 'date'],
        ]);

        $ingressos = Ingresso::where('lote_id', $loteid)->get();

        foreach($ingressos as $ingresso) {
            $ingresso->valor_externo = $request->valor_externo;
            $ingresso->valor_aluno = $request->valor_aluno;
            $ingresso->nome_evento = $request->nome_evento;
            $ingresso->data_validade = $request->data_validade;
            $ingresso->save();
        }

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
