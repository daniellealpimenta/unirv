<?php

namespace App\Http\Controllers;

use App\Models\Ingresso;
use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'ativo' => ['required', 'boolean'],
        ]);

        $lote = Lote::create([
            'nome' => $request->nome,
            'quantidade_ingressos' => 0,
            'ativo' => $request->ativo
        ]);

        return response()->json($lote, 201);
    }

    public function show()
    {
        $lote = Lote::all();

        return response()->json($lote);
    }

    public function updateQuantidade(Request $request, $loteid)
    {
        $quantidadeTotal = Ingresso::where('lote_id', $loteid)->where('disponivel', true)->count();
        $lote = Lote::find($loteid);
        $lote->quantidade_ingressos = $quantidadeTotal;
        $lote->save();

        return response()->json($lote);
    }

    public function update(Request $request, $loteid)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $lote = Lote::find($loteid);
        $lote->nome = $request->nome;
        $lote->save();

        $this->updateQuantidade($request, $loteid);

        return response()->json($lote);
    }

    public function disable($loteid)
    {
        $lote = Lote::find($loteid);
        $lote->ativo = false;
        $lote->save();

        return response()->json($lote);
    }

    public function enable($loteid)
    {
        $lote = Lote::find($loteid);
        $lote->ativo = true;
        $lote->save();

        return response()->json($lote);
    }

    public function destroy($loteid)
    {
        $lote = Lote::find($loteid);
        $lote->delete();

        return response()->json(null, 204);
    }
}
