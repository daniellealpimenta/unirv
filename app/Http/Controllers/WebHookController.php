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
        // 1. Lê o corpo bruto da requisição e decodifica o JSON
        $data = json_decode($request->getContent(), true);

        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];

            // 2. Garante que o Access Token está configurado
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));

            // 3. Cria o client de pagamento
            $paymentClient = new PaymentClient();

            // 4. Busca o pagamento pelo ID
            $payment = $paymentClient->get($paymentId);

            // 5. Atualiza o histórico de compra
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
