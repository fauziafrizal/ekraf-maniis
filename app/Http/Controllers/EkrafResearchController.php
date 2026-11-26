<?php

namespace App\Http\Controllers;

use App\Support\EkrafRandomForest;
use Illuminate\Http\Request;

class EkrafResearchController extends Controller
{
    public function index(EkrafRandomForest $forest)
    {
        return view('pages.community.research', ['pageTitle' => 'Klasifikasi Kebutuhan Pembinaan', 'available' => $forest->load() !== null]);
    }

    public function predict(Request $request, EkrafRandomForest $forest)
    {
        $model = $forest->load();
        abort_unless($model, 503, 'Model tervalidasi belum tersedia.');
        $data = $request->validate(array_fill_keys(EkrafRandomForest::FEATURES, 'required|integer|between:1,5'));
        $result = $forest->predict($model, array_map(fn ($key) => (int) $data[$key], EkrafRandomForest::FEATURES));
        abort_unless($result, 503, 'Model tidak dapat dibaca.');

        return back()->with('rf_result', $result)->withInput();
    }
}
