<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmergencyContactController extends Controller
{
    /**
     * Store a newly created emergency contact in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'number' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        EmergencyContact::query()->create([
            'name' => $validated['name'],
            'number' => $validated['number'],
            'color' => filled($validated['color'] ?? null) ? $validated['color'] : 'text-white',
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Kontak darurat '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update the specified emergency contact in storage.
     */
    public function update(Request $request, EmergencyContact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'number' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $contact->update([
            'name' => $validated['name'],
            'number' => $validated['number'],
            'color' => filled($validated['color'] ?? null) ? $validated['color'] : 'text-white',
            'order' => $validated['order'] ?? $contact->order,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Kontak darurat '{$contact->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified emergency contact from storage.
     */
    public function destroy(EmergencyContact $contact): RedirectResponse
    {
        $contactName = $contact->name;
        $contact->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Kontak darurat '{$contactName}' berhasil dihapus.");
    }
}

