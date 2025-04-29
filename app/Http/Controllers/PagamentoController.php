<?php

namespace App\Http\Controllers;

use App\Models\HistoricoDeCompra;
use App\Models\Ingresso;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\MercadoPagoService;

class PagamentoController extends Controller
{
    public function pagarPix(Request $request, MercadoPagoService $mp)
    {
        $validated = $request->validate([
            'ingresso_id' => 'required|exists:ingressos,id',
            'tipo_comprador' => 'required|in:aluno,externo',
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);

        $ingresso = Ingresso::find($validated['ingresso_id']);

        $valor = $validated['tipo_comprador'] == 'aluno' ? $ingresso->valor_aluno : $ingresso->valor_externo;

        $pagamento = $mp->criarPagamentoPix($valor, $validated['nome'], $validated['email']);

        $historico = HistoricoDeCompra::create([
            'user_id' => $user->id,
            'ingresso_id' => $ingresso->id,
            'status_do_pagamento' => $pagamento->status,
            'comprovante_de_pagamento' => null,
            'payment_id' => $pagamento->id,
        ]);

        return response()->json([
            'id' => $pagamento->id,
            'qr_code_base64' => $pagamento->point_of_interaction->transaction_data->qr_code_base64,
            'qr_code' => $pagamento->point_of_interaction->transaction_data->qr_code,
            'status' => $pagamento->status,
        ]);
    }
}
