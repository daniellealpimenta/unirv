<?php

namespace App\Http\Controllers;

use App\Models\HistoricoDeCompra;
use Illuminate\Http\Request;
use App\Services\MercadoPagoService;

class PagamentoController extends Controller
{
    public function pagarPix(Request $request, MercadoPagoService $mp)
    {
        $validated = $request->validate([
            'valor' => 'required|numeric',
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $pagamento = $mp->criarPagamentoPix($validated['valor'], $validated['nome'], $validated['email']);

        // $historico = HistoricoDeCompra::create([
        //     'user_id' => auth()->user()->id,
        //     'status_do_pagamento' => $pagamento->status,
        //     'ingresso_id' => $validated['ingresso_id'],
        // ]);

        return response()->json([
            'id' => $pagamento->id,
            'qr_code_base64' => $pagamento->point_of_interaction->transaction_data->qr_code_base64,
            'qr_code' => $pagamento->point_of_interaction->transaction_data->qr_code,
            'status' => $pagamento->status,
        ]);
    }
}
