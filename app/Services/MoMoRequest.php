<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * @author Kiala Ntona (nekiala@gmail.com, kiala@ntoprog.org)
 * @version 1.0
 */
class MoMoRequest
{
    private string $authorization;
    private string $subscription_value;
    private string $endpoint;
    private string $phone = '224666221552';
    private string $amount = '1000';

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->authorization = env('MOMO_AUTHORIZATION');
        $this->subscription_value = env('MOMO_SUBSCRIPTION_KEY');
        $this->endpoint = env('MOMO_ENDPOINT');
    }

    public function setParams($phone, $amount): void
    {
        $this->phone = $phone;
        $this->amount = $amount;
    }

    public function start(): void
    {
        try {

            $token = Http::withHeaders([
                'Authorization' => $this->authorization,
                'Ocp-Apim-Subscription-Key' => $this->subscription_value
            ])->post($this->endpoint . 'token/');

            $access_token = $token->json('access_token');

            $body = [
                'amount' => $this->amount,
                'currency' => 'GNF',
                'externalId' => fake()->uuid,
                'payee' => [
                    'partyIdType' => 'MSISDN',
                    //'partyId' => '224663131018'
                    'partyId' => $this->phone
                ],
                'payerMessage' => 'ComJaim Cash',
                'payeeNote' => 'ComJaim Cash'
            ];

            $reference_id = fake()->uuid;

            $transfer = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'X-Reference-Id' => $reference_id,
                'X-Target-Environment' => "mtnguineaconakry",
                'Content-Type' => "application/json",
                'Ocp-Apim-Subscription-Key' => $this->subscription_value,
            ])->withBody(json_encode($body), 'application/json')->post($this->endpoint . 'v1_0/transfer');

            //var_dump($transfer->body());

            if ($transfer->status() == 202) {

                // sleep 5 seconds and get transaction status
                sleep(5);

                $status = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Target-Environment' => "mtnguineaconakry",
                    'Content-Type' => "application/json",
                    'Ocp-Apim-Subscription-Key' => $this->subscription_value,
                ])->get($this->endpoint . 'v1_0/transfer/' . $reference_id);

                // echoing the response
                echo $status->body();

            } else {

                // sending a custom message
                echo json_encode(['message' => 'Token generation error.']);
            }
        } catch (ConnectionException $exception) {

            echo json_encode(['message' => 'MoMo server temporary down: ' . $exception->getMessage()]);
        }
    }
}
