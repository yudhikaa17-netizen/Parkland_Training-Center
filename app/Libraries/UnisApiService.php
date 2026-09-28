<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class UnisApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = env('unis.api.baseUrl', 'http://192.168.44.97/endpointapp/public/api');
        $this->timeout = (int) env('unis.api.timeout', 3);
    }

    /**
     * Check UNIS API health and database connection status
     */
    public function checkHealth(): array
    {
        try {
            $client = \Config\Services::curlrequest([
                'base_uri' => $this->baseUrl . '/',
                'timeout'  => $this->timeout,
                'http_errors' => false,
            ]);

            $response = $client->get('health');
            $body = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 200 && is_array($body)) {
                return [
                    'connected' => true,
                    'data'      => $body,
                ];
            }

            return [
                'connected' => false,
                'message'   => 'HTTP Status ' . $response->getStatusCode(),
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'message'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Lookup user by RFID Card Hexadecimal (e.g. DCEAE8AD)
     */
    public function getUserByCard(string $cardHex): array
    {
        $cardHex = trim($cardHex);
        if (empty($cardHex)) {
            return ['status' => false, 'message' => 'Nomor kartu RFID tidak boleh kosong.'];
        }

        try {
            $client = \Config\Services::curlrequest([
                'base_uri' => $this->baseUrl . '/',
                'timeout'  => $this->timeout,
                'http_errors' => false,
            ]);

            $response = $client->get('user/card/' . urlencode($cardHex));
            $body = json_decode($response->getBody(), true);

            if (is_array($body)) {
                return $body;
            }

            return ['status' => false, 'message' => 'Respon API UNIS tidak valid.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal terhubung ke API UNIS: ' . $e->getMessage()];
        }
    }

    /**
     * Lookup user by NIK (e.g. 199315)
     */
    public function getUserByNik(string $nik): array
    {
        $nik = trim($nik);
        if (empty($nik)) {
            return ['status' => false, 'message' => 'NIK tidak boleh kosong.'];
        }

        try {
            $client = \Config\Services::curlrequest([
                'base_uri' => $this->baseUrl . '/',
                'timeout'  => $this->timeout,
                'http_errors' => false,
            ]);

            $response = $client->get('user/nik/' . urlencode($nik));
            $body = json_decode($response->getBody(), true);

            if (is_array($body)) {
                return $body;
            }

            return ['status' => false, 'message' => 'Respon API UNIS tidak valid.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal terhubung ke API UNIS: ' . $e->getMessage()];
        }
    }

    /**
     * Get list of users from UNIS API
     */
    public function getAllUsers(): array
    {
        try {
            $client = \Config\Services::curlrequest([
                'base_uri' => $this->baseUrl . '/',
                'timeout'  => $this->timeout,
                'http_errors' => false,
            ]);

            $response = $client->get('users');
            $body = json_decode($response->getBody(), true);

            if (is_array($body)) {
                return $body;
            }

            return ['status' => false, 'message' => 'Respon API UNIS tidak valid.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal terhubung ke API UNIS: ' . $e->getMessage()];
        }
    }
}
