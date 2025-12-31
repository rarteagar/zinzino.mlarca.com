<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'identifier' => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:clients,email',
                'phone' => 'required|string|max:50',
                'birthdate' => 'required|date',
                'height_cm' => 'required|integer|min:30|max:300',
                'weight_kg' => 'required|numeric|min:1|max:500',
                'is_self' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $user = Auth::user();

        // If marking as self, ensure only one self-client per user by clearing others
        $isSelf = (bool) ($validated['is_self'] ?? false);

        $clientData = [
            'user_id' => $user->id,
            'name' => $validated['name'],
            'identifier' => $validated['identifier'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birthdate' => $validated['birthdate'],
            'height_cm' => $validated['height_cm'],
            'weight_kg' => $validated['weight_kg'],
            'is_self' => $isSelf,
        ];


        // wrap in transaction to ensure atomicity
        $client = null;
        \DB::transaction(function () use ($user, $isSelf, $clientData, &$client) {
            if ($isSelf) {
                // clear other self flags for this user
                Client::where('user_id', $user->id)->where('is_self', true)->update(['is_self' => false]);
            }

            $client = Client::create($clientData);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'client' => $client]);
        }

        return redirect()->back()->with('status', 'Cliente creado');
    }


    public function update(Client $client, Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'identifier' => 'nullable|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('clients', 'email')->ignore($client->id)
                ],
                'phone' => 'required|string|max:50',
                'birthdate' => 'required|date',
                'height_cm' => 'required|integer|min:30|max:300',
                'weight_kg' => 'required|numeric|min:1|max:500'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $user = Auth::user();
        if ($client->user_id !== $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            abort(403);
        }

        \DB::transaction(function () use ($client, $validated) {
            // preservar explícitamente is_self (opcional)
            $client->update(array_merge($validated, ['is_self' => $client->is_self]));
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'client' => $client->refresh()]);
        }

        return redirect()->back()->with('status', 'Cliente actualizado');
    }
}
