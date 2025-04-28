<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoDeCompra;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];

            
            $payment = (new \MercadoPago\Client\Payment\PaymentClient())->get($paymentId);

            
            $historico = HistoricoDeCompra::where('payment_id', $paymentId)->first();

            if ($historico) {
                $historico->update([
                    'status_do_pagamento' => $payment->status, 
                    'comprovante_de_pagamento' => $payment->point_of_interaction->transaction_data->ticket_url ?? null, 
                ]);
            }
        }

        return response()->json(['message' => 'Webhook recebido']);
    }
}
