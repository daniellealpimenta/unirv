<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoricoDeCompra;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
            $data = json_decode($request->getContent(), true);

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
                    Log::info('Histórico atualizado com sucesso.');
                } else {
                    Log::warning('Histórico não encontrado para payment_id: ' . $paymentId);
                }
            } else {
                Log::warning('ID de pagamento não encontrado no payload.');
            }

            return response()->json(['message' => 'Webhook recebido']);
    }
}
