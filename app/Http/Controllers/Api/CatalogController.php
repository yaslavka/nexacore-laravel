<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __invoke(Request $request, CatalogService $catalog): JsonResponse
    {
        return response()->json($catalog->get($request->query('lang', 'en')));
    }
}
