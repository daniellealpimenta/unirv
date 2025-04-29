<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoDeCompra;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['data']['id'])) {
            return response()->json(['error' => 'ID do pagamento não encontrado.'], 400);
        }

        $paymentId = $data['data']['id'];

        try {
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

            return response()->json(['message' => 'Webhook processado com sucesso.']);
        } catch (MPApiException $e) {
            return response()->json(['error' => 'Erro ao consultar pagamento: '.$e->getMessage()], 500);
        }
    }
}