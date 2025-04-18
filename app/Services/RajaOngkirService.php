<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    public static function getDomesticDestinations(string $type, $parentId = null): array
    {
        $apiKey = config('services.rajaongkir.key');

        $baseUrl = 'https://api.rajaongkir.com/starter'; // Bisa diubah ke basic/pro/enterprise
        $endpoint = match ($type) {
            'province' => '/province',
            'city'     => "/city?province=$parentId",
            default    => '',
        };

        $response = Http::withHeaders([
            'key' => $apiKey,
        ])->get($baseUrl . $endpoint);

        if ($response->successful()) {
            return [
                'data' => $response->json()['rajaongkir']['results'],
            ];
        }

        return ['data' => []];
    }

    public static function getShippingCost($origin, $destination, $weight, $courier): array
    {
        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url');

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->post($baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);

            Log::debug('RajaOngkir RAW Response:', $response->json());

            if (!$response->successful()) {
                throw new \Exception($response->json()['rajaongkir']['status']['description'] ?? 'API Error');
            }

            $data = $response->json()['rajaongkir'];

            // Validasi response structure
            if (!isset($data['results'][0]['costs'])) {
                throw new \Exception('Invalid API response structure');
            }

            return [
                'data' => $data['results'],
                'origin_details' => $data['origin_details'] ?? null,
                'destination_details' => $data['destination_details'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
