<?php

namespace App\Http\Controllers;

use App\Services\ExternalAPI\CustomerApiService;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function __construct(
        protected CustomerApiService $service
    ) {}

    public function index()
    {
        try {
            $customers = $this->service->getCustomers();
            return response()->json($customers);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $customers = $this->service->getCustomer($request->cnpj);
            return response()->json($customers);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
