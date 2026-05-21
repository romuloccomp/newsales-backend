<?php

namespace App\Services\ExternalAPI;

use App\DTOs\CustomerDTO;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductApiService
{
    private PendingRequest $client;

    private array $apiParams = [
        'action' => 'getPrecoItem',
    ];

    public function __construct()
    {
        $this->client = Http::withBasicAuth(config('datasul.api.user'), config('datasul.api.password'))
            ->baseUrl(config('datasul.api.host'))
            ->timeout(30)
            ->retry(5, 200)
            ->throw();
    }

    public function getProducts(string $nr_tabpre)
    {
        try {
            $response  = $this->client->get('/preco', [
                ...$this->apiParams,
                'nr_tabpre' => $nr_tabpre
            ]);

            return $response;
        } catch (Exception $e) {
            throw new (
                "Erro ao retornar clientes: " . $e->getMessage()
            );
        }
    }
}
