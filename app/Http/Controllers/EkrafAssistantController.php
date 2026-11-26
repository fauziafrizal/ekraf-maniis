<?php

namespace App\Http\Controllers;

use App\Support\EkrafAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkrafAssistantController extends Controller
{
    public function __invoke(Request $request, EkrafAssistant $assistant): JsonResponse
    {
        $data = $request->validate(['question' => 'required|string|min:2|max:400', 'allow_ai' => 'sometimes|boolean']);

        return response()->json($assistant->answer($data['question'], $request->boolean('allow_ai')));
    }
}
