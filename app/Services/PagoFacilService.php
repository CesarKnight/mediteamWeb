<?php

namespace App\Services;

use App\Models\Pago;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PagoFacilService
{
    private const TOKEN_CACHE_KEY = 'pagofacil_access_token';

    private function baseUrl(): string
    {
        return config('services.pagofacil.base_url');
    }

    public function getAccessToken(): string
    {
        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if ($cached) {
            return $cached;
        }

        $response = Http::withHeaders([
            'tcTokenSecret'  => config('services.pagofacil.token_secret'),
            'tcTokenService' => config('services.pagofacil.token_service'),
        ])->timeout(15)->post("{$this->baseUrl()}/login");

        if ($response->failed() || $response->json('error') !== 0) {
            throw new \RuntimeException('PagoFácil login falló: ' . $response->body());
        }

        $token = $response->json('values.accessToken');
        $expiresInMinutes = (float) $response->json('values.expiresInMinutes', 20);

        // cache with a 5-minute safety buffer before actual expiry
        Cache::put(self::TOKEN_CACHE_KEY, $token, now()->addMinutes(max(1, $expiresInMinutes - 5)));

        return $token;
    }

    /**
     * @return array{transactionId:int,paymentMethodTransactionId:string,status:int,expirationDate:string,qrBase64:string}
     */
    public function generateQr(Pago $pago, string $transferenciaUuid): array
    {
        $token = $this->getAccessToken();

        $paciente = $pago->paciente;
        $usuario = $paciente->user;

        $payload = [
            'paymentMethod' => 34, // locked by PagoFácil
            'clientName'    => trim($usuario->name . ' ' . $usuario->lastName),
            'documentType'  => 1, // locked by PagoFácil
            'documentId'    => $usuario->ci,
            'phoneNumber'   => $usuario->telefono,
            'email'         => $usuario->email,
            'paymentNumber' => $transferenciaUuid,
            'amount'        => (float) $pago->total,
            'currency'      => 2, // locked by PagoFácil
            'clientCode'    => (string) $paciente->id,
            'callbackUrl'   => config('services.pagofacil.callback_url'),
            'orderDetail'   => [[
                'serial'   => $pago->servicio_id,
                'product'  => $pago->servicio->titulo,
                'quantity' => 1, // locked by PagoFácil
                'price'    => (float) $pago->total,
                'discount' => 0, // locked by PagoFácil
                'total'    => (float) $pago->total,
            ]],
        ];

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(15)
            ->post("{$this->baseUrl()}/generate-qr", $payload);

        if ($response->failed() || $response->json('error') !== 0) {
            throw new \RuntimeException('PagoFácil generate-qr falló: ' . $response->body());
        }

        return $response->json('values');
    }

    /**
     * @return array{
     *     pagofacilTransactionId:int,
     *     paymentStatus:int,
     *     paymentStatusDescription:string,
     *     amount:string,
     *     ...
     * }
     */
    public function queryTransaction(int $pagofacilTransactionId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(15)
            ->post("{$this->baseUrl()}/query-transaction", [
                'pagofacilTransactionId' => $pagofacilTransactionId,
            ]);

        if ($response->failed() || $response->json('error') !== 0) {
            throw new \RuntimeException('PagoFácil query-transaction falló: ' . $response->body());
        }

        return $response->json('values');
    }
}
