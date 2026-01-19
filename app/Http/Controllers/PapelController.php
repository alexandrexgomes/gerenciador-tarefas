<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Application\UseCases\Papel\PapelService;

class PapelController extends Controller
{
    public function index(PapelService $papelService): JsonResponse
    {
        $usuarios = $papelService->listar();
        return response()->json($usuarios, 200);
    }
}
