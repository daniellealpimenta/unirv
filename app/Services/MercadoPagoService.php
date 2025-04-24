<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoService
{
    protected $client;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
        $this->client = new PaymentClient();
    }

    public function criarPagamentoPix($valor, $nome, $email)
    {
        try {
            $payment = $this->client->create([
                "transaction_amount" => floatval($valor),
                "description" => "Compra de ingresso",
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => $email,
                    "first_name" => $nome,
                ]
            ]);

            return $payment;
        } catch (MPApiException $e) {
            throw new \Exception($e->getApiResponse()->getContent());
        }
    }
}
