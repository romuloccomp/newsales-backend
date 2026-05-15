<?php

namespace App\Services\ExternalAPI;

use App\DTOs\CustomerDTO;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CustomerApiService
{
    private PendingRequest $client;

    private array $apiParams = [
        'action' => 'getClientes',
        'codrep' => '1012',
        'clientIni' => '0',
        'clientEnd' => '999999999',
    ];

    public function __construct()
    {
        $this->client = Http::withBasicAuth(config('datasul.api.user'), config('datasul.api.password'))
            ->baseUrl(config('datasul.api.host'))
            ->timeout(30)
            ->retry(5, 200)
            ->throw();
    }

    public function getCustomers(): array
    {
        try {
            $response  = $this->client->get('/clientes', $this->apiParams);

            return collect($response->json()['clientes'])
                ->map(fn($customer) => CustomerDTO::fromArray($customer))
                ->toArray();
        } catch (Exception $e) {
            throw new (
                "Erro ao retornar clientes: " . $e->getMessage()
            );
        }
    }

    public function getCustomer(string $cnpj): CustomerDTO
    {
        try {
            $response = $this->client->get('/zooms', [
                ...$this->apiParams,
                'cgc' => $cnpj
            ]);

            return CustomerDTO::fromArray($response['clientes'][0]);
        } catch (Exception $e) {
            throw new (
                "Erro ao retornar cliente do cnpj {$cnpj} " . $e->getMessage()
            );
        }
    }
}
