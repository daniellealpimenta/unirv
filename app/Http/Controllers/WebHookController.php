<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\HistoricoDeCompra;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use Exception;

class WebhookController extends Controller
{
    public function handle(Request $request)
{
    // Log e resposta imediata
    Log::info('📬 Webhook recebido', [
        'payload_raw' => $request->getContent(),
        'payload_json' => $request->all(),
    ]);

    // Retorna 200 rapidamente para o Mercado Pago
    response()->json(['message' => 'Recebido. Processando.'])->send();

    // Agora processa o resto
    ignore_user_abort(true);
    ob_end_flush();

    try {
        $data = $request->input('data');
        if (!isset($data['id'])) {
            Log::warning('❗ ID do pagamento não encontrado no webhook.');
            return;
        }

        $paymentId = $data['id'];

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
        $paymentClient = new PaymentClient();
        $payment = $paymentClient->get($paymentId);

        Log::info('✅ Pagamento recuperado:', [
            'id' => $payment->id,
            'status' => $payment->status,
        ]);

        $historico = HistoricoDeCompra::where('payment_id', $paymentId)->first();

        if ($historico) {
            $historico->update([
                'status_do_pagamento' => $payment->status,
                'comprovante_de_pagamento' => $payment->point_of_interaction->transaction_data->ticket_url ?? null,
            ]);
            Log::info('📝 Histórico atualizado com sucesso.');
        } else {
            Log::warning('⚠️ Nenhum histórico encontrado com payment_id: ' . $paymentId);
        }

    } catch (Exception $e) {
        Log::error('❌ Erro ao processar webhook: ' . $e->getMessage());
    }
}

}
