<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Tests entered by the user (their own tests)
        $myTests = Test::where('entered_by_id', $user->id)->whereNull('client_id')->latest()->get();

        // Tests of clients managed by the user
        $clientIds = $user->clients()->pluck('id');
        $clientTests = Test::whereIn('client_id', $clientIds)->latest()->get();

        return view('tests.index', compact('myTests', 'clientTests'));
    }

    public function create()
    {
        $user = Auth::user();
        $clients = $user->clients()->get();
        $hasSelf = $clients->where('is_self', true)->isNotEmpty();
        return view('tests.create', compact('clients', 'hasSelf'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'subject_user_id' => 'nullable|exists:users,id',
            'sample_date' => 'nullable|date',
            'type' => 'nullable|string|max:255',
            'data' => 'nullable', // accept JSON string from textarea or array
            'score' => 'nullable|numeric',
            'subject_age' => 'nullable|integer|min:0|max:200',
            'subject_height_cm' => 'nullable|integer|min:0|max:300',
            'subject_weight_kg' => 'nullable|numeric|min:0|max:500',
            'health_challenges' => 'nullable|string',
        ]);

        // Normalize data: if it's a string, try to json_decode
        $payload = null;
        if (is_string($validated['data'] ?? null)) {
            $decoded = json_decode($validated['data'], true);
            $payload = $decoded === null ? null : $decoded;
        } elseif (is_array($validated['data'] ?? null)) {
            $payload = $validated['data'];
        }

        $test = Test::create([
            'entered_by_id' => Auth::id(),
            'subject_user_id' => $validated['subject_user_id'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
            'is_my_test' => false,
            'sample_date' => $validated['sample_date'] ?? null,
            'type' => $validated['type'] ?? null,
            'data' => $payload,
            'score' => $validated['score'] ?? null,
            'subject_age' => $validated['subject_age'] ?? null,
            'subject_height_cm' => $validated['subject_height_cm'] ?? null,
            'subject_weight_kg' => $validated['subject_weight_kg'] ?? null,
            'health_challenges' => $validated['health_challenges'] ?? null,
        ]);

        return redirect()->route('tests.index')->with('status', 'Prueba registrada');
    }

    public function show(Test $test)
    {
        $this->authorize('view', $test);
        return view('tests.show', compact('test'));
    }
}
