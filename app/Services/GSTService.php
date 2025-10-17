<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class GSTService
{
    protected $host;
    protected $username;
    protected $password;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->host = config('gst.host');
        $this->username = config('gst.username');
        $this->password = config('gst.password');
        $this->clientId = config('gst.client_id');
        $this->clientSecret = config('gst.client_secret');
    }

    /**
     * Get auth token from NIC (cached)
     */
    public function getAuthToken()
    {
        $cacheKey = 'gst_auth_token';

        return Cache::remember($cacheKey, 360 * 60, function () {
            $url = "{$this->host}/auth"; // confirm exact endpoint from NIC docs

            $response = Http::post($url, [
                'username' => $this->username,
                'password' => $this->password,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                // some NIC endpoints may require GSTIN or encrypted password
            ]);

            if (!$response->successful()) {
                throw new Exception('GST Auth Token request failed: ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['auth_token'])) {
                throw new Exception('No auth token returned from GST API');
            }

            return $data['auth_token'];
        });
    }

    /**
     * Generate IRN (e-Invoice)
     */
    public function generateIrn(array $invoicePayload)
    {
        $token = $this->getAuthToken();

        $url = "{$this->host}/einv/v2/eInvoice/generate"; // confirm exact path/version in NIC docs

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'accept' => 'application/json',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            // optionally add 'gstin' => 'your GSTIN'
        ])->put($url, $invoicePayload);

        if (!$response->successful()) {
            throw new Exception('Generate IRN failed: ' . $response->body());
        }

        return $response->json();
    }
}

// How to use?

// use App\Services\GSTService;

// $gstService = new GSTService();

// $invoicePayload = [
//     "Version" => "1.1",
//     "TranDtls" => [
//         "TaxSch" => "GST",
//         "SupTyp" => "B2B",
//     ],
//     "DocDtls" => [
//         "Typ" => "INV",
//         "No" => "INV001",
//         "Dt" => "17/10/2025"
//     ],
//     "SellerDtls" => [
//         "Gstin" => "27ABCDE1234F2Z5",
//         "LglNm" => "Your Company Name",
//     ],
//     "BuyerDtls" => [
//         "Gstin" => "27ABCDE1234F2Z5",
//         "LglNm" => "Buyer Name",
//     ],
//     "ItemList" => [
//         [
//             "SlNo" => "1",
//             "PrdDesc" => "Product 1",
//             "Qty" => 1,
//             "Unit" => "PCS",
//             "UnitPrice" => 100,
//             "TotAmt" => 100,
//             "GstRt" => 18,
//             "IgstAmt" => 0,
//             "CgstAmt" => 9,
//             "SgstAmt" => 9
//         ]
//     ],
// ];

// $result = $gstService->generateIrn($invoicePayload);

// dd($result);
