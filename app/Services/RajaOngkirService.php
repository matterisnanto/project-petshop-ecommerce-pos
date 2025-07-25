<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    public static function getDomesticDestinations(string $type, $parentId = null): array
    {
        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url');

        $endpoint = match ($type) {
            'province' => '/destination/province',
            'city'     => "/destination/city/$parentId",
            'district' => "/destination/district/$parentId",
            'subdistrict' => "/destination/sub-district/$parentId",
            default    => '',
        };

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->timeout(10)->get($baseUrl . $endpoint);

            if (!$response->successful()) {
                Log::error('RajaOngkir API Error', [
                    'type' => $type,
                    'parentId' => $parentId,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return ['data' => []];
            }

            $responseData = $response->json();

            // Handle different response structures
            if (isset($responseData['rajaongkir']['results'])) {
                return ['data' => $responseData['rajaongkir']['results']];
            }

            if (isset($responseData['data'])) {
                return ['data' => $responseData['data']];
            }

            return ['data' => []];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Service Exception: ' . $e->getMessage(), [
                'type' => $type,
                'parentId' => $parentId
            ]);
            return ['data' => []];
        }
    }

    public static function getShippingCost($origin, $destination, $weight, $courier): array
    {
        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url');

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
            ])->post($baseUrl . '/calculate/district/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => 'lowest', // Ensure we get sorted by lowest price
            ]);

            Log::debug('RajaOngkir Shipping Cost Request:', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new \Exception($error['message'] ?? $error['rajaongkir']['status']['description'] ?? 'API Error');
            }

            $data = $response->json();

            // Validate response structure
            if (!isset($data['data']) || !is_array($data['data'])) {
                throw new \Exception('Invalid API response structure');
            }

            return [
                'data' => $data['data'],
                'meta' => $data['meta'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Shipping Cost Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
