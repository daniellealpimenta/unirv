<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoDeCompra;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));

            $paymentClient = new PaymentClient();

            $payment = $paymentClient->get($paymentId);

            $historico = HistoricoDeCompra::where('payment_id', $paymentId)->first();

            if ($historico) {
                $historico->update([
                    'status_do_pagamento' => $payment->status,
                    'comprovante_de_pagamento' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
                ]);
            }
        }

        return response()->json(['message' => 'Webhook recebido com sucesso!']);
    }
}
