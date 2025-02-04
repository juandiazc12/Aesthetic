<?php

namespace App\Http\Controllers;

use App\Models\BinLookup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BinLookController extends Controller
{
    private $proxyList = [
        'https://lookup.binlist.net',
        'https://bins.payout.com',
        'https://binlist.io',
        // Agrega más endpoints alternativos si es necesario
    ];

    private function getCardLogos()
    {
        return [
            'visa' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/visa.png',
            'mastercard' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/mastercard.png',
            'amex' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/amex.png',
            'discover' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/discover.png',
            'troy' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/troy.png',
            'default' => 'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/chip.png'
        ];
    }

    private function getCardLogo($scheme)
    {
        $logos = $this->getCardLogos();
        $key = strtolower($scheme);
        return $logos[$key] ?? $logos['default'];
    }

    public function getBankInfo($bin)
    {
        try {
            $bin = preg_replace('/\D/', '', $bin);
            if (strlen($bin) < 6) {
                throw new \Exception('Se requieren los primeros 6 dígitos de la tarjeta');
            }
            $bin = substr($bin, 0, 6);

            // Buscar en caché primero
            $cachedLookup = BinLookup::where('bin', $bin)->first();
            if ($cachedLookup) {
                return response()->json([
                    'scheme' => ucfirst($cachedLookup->brand),
                    'type' => $cachedLookup->type,
                    'logo' => $this->getCardLogo($cachedLookup->brand),
                    'country' => [
                        'name' => $cachedLookup->country_name,
                        'emoji' => $cachedLookup->country_emoji
                    ]
                ]);
            }

            // Datos predefinidos para BINs conocidos
            $knownBins = $this->getKnownBins();
            if (isset($knownBins[$bin])) {
                $bankData = $knownBins[$bin];
                $this->saveBinToCache($bin, $bankData);
                return response()->json($bankData);
            }

            // Intentar con diferentes endpoints
            foreach ($this->proxyList as $baseUrl) {
                try {
                    $response = Http::timeout(5)->withHeaders([
                        'Accept-Version' => '3',
                        'Accept' => 'application/json',
                    ])->get("{$baseUrl}/{$bin}");

                    if ($response->successful()) {
                        $data = $response->json();
                        $this->saveBinToCache($bin, [
                            'scheme' => ucfirst($data['scheme'] ?? 'Unknown'),
                            'type' => $data['type'] ?? 'Unknown',
                            'country' => [
                                'name' => $data['country']['name'] ?? 'Unknown',
                                'emoji' => $data['country']['emoji'] ?? '🌎'
                            ]
                        ]);
                        return response()->json([
                            'scheme' => ucfirst($data['scheme'] ?? 'Unknown'),
                            'type' => $data['type'] ?? 'Unknown',
                            'logo' => $this->getCardLogo($data['scheme'] ?? ''),
                            'country' => [
                                'name' => $data['country']['name'] ?? 'Unknown',
                                'emoji' => $data['country']['emoji'] ?? '🌎'
                            ]
                        ]);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Si no se encuentra, usar datos predeterminados basados en el rango
            $defaultData = $this->getDefaultCardData($bin);
            $this->saveBinToCache($bin, $defaultData);
            return response()->json($defaultData);

        } catch (\Exception $e) {
            Log::error('Error en BinLookup', [
                'bin' => $bin ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    private function getKnownBins()
    {
        return [
            '453123' => [
                'scheme' => 'Visa',
                'type' => 'credit',
                'country' => ['name' => 'Colombia', 'emoji' => '🇨🇴']
            ],
            '557910' => [
                'scheme' => 'Mastercard',
                'type' => 'credit',
                'country' => ['name' => 'Colombia', 'emoji' => '🇨🇴']
            ],
            '374245' => [
                'scheme' => 'American Express',
                'type' => 'credit',
                'country' => ['name' => 'United States', 'emoji' => '🇺🇸']
            ]
        ];
    }

    private function getDefaultCardData($bin)
    {
        $firstDigit = substr($bin, 0, 1);
        $firstTwo = substr($bin, 0, 2);

        if ($firstDigit === '4') {
            return [
                'scheme' => 'Visa',
                'type' => 'credit',
                'country' => ['name' => 'Unknown', 'emoji' => '🌎']
            ];
        }

        if ($firstTwo >= '51' && $firstTwo <= '55') {
            return [
                'scheme' => 'Mastercard',
                'type' => 'credit',
                'country' => ['name' => 'Unknown', 'emoji' => '🌎']
            ];
        }

        if ($firstTwo === '34' || $firstTwo === '37') {
            return [
                'scheme' => 'American Express',
                'type' => 'credit',
                'country' => ['name' => 'Unknown', 'emoji' => '🌎']
            ];
        }

        return [
            'scheme' => 'Unknown',
            'type' => 'Unknown',
            'country' => ['name' => 'Unknown', 'emoji' => '🌎']
        ];
    }

    private function saveBinToCache($bin, $data)
    {
        BinLookup::create([
            'bin' => $bin,
            'brand' => $data['scheme'],
            'type' => $data['type'],
            'country_name' => $data['country']['name'],
            'country_emoji' => $data['country']['emoji'],
            'is_valid' => true
        ]);
    }
} 