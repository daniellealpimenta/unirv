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
        Log::info('Recebido webhook', ['body' => $request->getContent()]); // log do conteúdo bruto recebido

        try {
            // Aqui a diferença: decodificando o raw JSON
            $data = json_decode($request->getContent(), true);

            if (isset($data['data']['id'])) {
                $paymentId = $data['data']['id'];
                Log::info('ID de pagamento recebido: ' . $paymentId);

                // Setar o access token da API do Mercado Pago
                MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_TOKEN'));

                $paymentClient = new PaymentClient();
                $payment = $paymentClient->get($paymentId);

                Log::info('Pagamento retornado do Mercado Pago', ['status' => $payment->status]);

                // Buscar o histórico de compra pelo payment_id
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
        } catch (\Exception $e) {
            Log::error('Erro no processamento do webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Erro no processamento do webhook: ' . $e->getMessage()], 500);
        }
    }
}
