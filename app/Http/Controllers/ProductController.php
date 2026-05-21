<?php

namespace App\Http\Controllers;

use App\Services\ExternalAPI\ProductApiService;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct(
        protected ProductApiService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // if($request->nr_tabpre == ""){
        //     return response()->json(['message' => 'Tabela de preços não informada!'], 500);
        // }
        try {
            $products = $this->service->getProducts($request->nr_tabpre);

            return response()->json($products['precoitem'], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
    //  */
    // public function show(Request $request)
    // {
    //     try {
    //         $product = $this->service->getProduct($request->cnpj);
    //         return response()->json($product);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
