<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ToyyibPayService
{
    private string $baseUrl;
    private string $secretKey;
    private string $categoryCode;

    public function __construct()
    {
        // Guna https://dev.toyyibpay.com untuk sandbox/testing,
        // https://toyyibpay.com untuk produksi sebenar.
        $this->baseUrl = rtrim(config('services.toyyibpay.base_url', 'https://dev.toyyibpay.com'), '/');
        $this->secretKey = config('services.toyyibpay.secret_key', '');
        $this->categoryCode = config('services.toyyibpay.category_code', '');
    }

    /**
     * Cipta Bill ToyyibPay. Return array ['billCode' => ..., 'payment_url' => ...]
     * atau null kalau gagal (cth: credential salah/tiada internet).
     */
    public function createBill(array $data): ?array
    {
        $payload = array_merge([
            'userSecretKey' => $this->secretKey,
            'categoryCode' => $this->categoryCode,
            'billPriceSetting' => 1, // 1 = harga tetap (fixed), tak boleh pelanggan ubah
            'billPayorInfo' => 1,
            'billPaymentChannel' => 0, // 0 = FPX + Kad Kredit
            'billDisplayMerchant' => 1,
            'billChargeToCustomer' => 0, // 0 = caj RM1 ditanggung merchant (bukan pelanggan)
        ], $data);

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/index.php/api/createBill", $payload);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $result = $response->json();

        // ToyyibPay pulangkan array dengan satu objek [{ "BillCode": "xxxx" }]
        $billCode = $result[0]['BillCode'] ?? null;

        if (! $billCode) {
            return null;
        }

        return [
            'billCode' => $billCode,
            'payment_url' => "{$this->baseUrl}/{$billCode}",
        ];
    }

    public function isConfigured(): bool
    {
        return ! empty($this->secretKey) && ! empty($this->categoryCode);
    }

    /**
     * Semak status SEBENAR bagi satu bill terus dari ToyyibPay — berguna kalau
     * callback tak sampai (server down sekejap, isu rangkaian, dll) tapi
     * pembayaran sebenarnya DAH berjaya di pihak ToyyibPay.
     * Return: 1=berjaya, 2=pending, 3=gagal, null=ralat/tak jumpa.
     */
    public function checkBillStatus(string $billCode): ?int
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/index.php/api/getBillTransactions", [
                'billCode' => $billCode,
                'userSecretKey' => $this->secretKey,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $transactions = $response->json();

        if (empty($transactions) || ! is_array($transactions)) {
            return null;
        }

        // Ambil transaksi PALING BARU, semak billpaymentStatus dia.
        $latest = $transactions[0];

        return isset($latest['billpaymentStatus']) ? (int) $latest['billpaymentStatus'] : null;
    }
}
