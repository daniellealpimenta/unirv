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
        \Log::info('Webhook recebido do MercadoPago:', [
            'payload' => $request->all()
        ]);
    
        try {
            $data = $request->input('data');
    
            if (!isset($data['id'])) {
                \Log::warning('ID do pagamento não encontrado no webhook.');
                return response()->json(['error' => 'ID do pagamento não encontrado.'], 400);
            }
    
            $paymentId = $data['id'];
    
            \MercadoPago\MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $paymentClient->get($paymentId);
    
            \Log::info('Pagamento recuperado com sucesso:', ['status' => $payment->status]);
    
            $historico = \App\Models\HistoricoDeCompra::where('payment_id', $paymentId)->first();
    
            if ($historico) {
                $historico->update([
                    'status_do_pagamento' => $payment->status,
                    'comprovante_de_pagamento' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
                ]);
            }
    
            return response()->json(['message' => 'Webhook processado com sucesso.']);
        } catch (\Exception $e) {
            \Log::error('Erro ao processar webhook do MercadoPago: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}
