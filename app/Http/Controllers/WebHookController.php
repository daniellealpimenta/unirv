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
        try {
            $data = $request->all();

            if (isset($data['data']['id'])) {
                $paymentId = $data['data']['id'];
                
                MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_TOKEN'));

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

            return response()->json(['message' => 'Webhook recebido']);
        } catch (\Exception $e) {
            \Log::error('Erro no webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Falha no Webhook: ' . $e->getMessage()], 500);
        }
    }
}
