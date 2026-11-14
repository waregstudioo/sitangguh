<?php

namespace App\Http\Controllers;

use App\Models\AlertStatus;
use App\Models\Logistic;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticController extends Controller
{
    /**
     * Display the unified admin view with logistics inventory.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'alertStatus' => AlertStatus::current(),
            'logistics' => Logistic::query()->latest()->get(),
            'reports' => Report::query()->latest()->paginate(8),
            'editingLogistic' => null,
        ]);
    }

    /**
     * Show the unified admin view with the selected logistic item loaded for editing.
     */
    public function edit(Logistic $logistic): View
    {
        return view('admin.dashboard', [
            'alertStatus' => AlertStatus::current(),
            'logistics' => Logistic::query()->latest()->get(),
            'reports' => Report::query()->latest()->paginate(8),
            'editingLogistic' => $logistic,
        ]);
    }

    /**
     * Store a newly created evacuation center logistic item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'unit' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:150'],
        ]);

        Logistic::query()->create([
            'item_name' => $validated['item_name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'location' => filled($validated['location'] ?? null)
                ? $validated['location']
                : 'Posko Utama Balai Desa',
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Item logistik '{$validated['item_name']}' berhasil ditambahkan ke inventaris posko.");
    }

    /**
     * Update the specified evacuation center logistic item in storage.
     */
    public function update(Request $request, Logistic $logistic): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'unit' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:150'],
        ]);

        $logistic->update([
            'item_name' => $validated['item_name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'location' => filled($validated['location'] ?? null)
                ? $validated['location']
                : 'Posko Utama Balai Desa',
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Data logistik '{$logistic->item_name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified evacuation center logistic item from storage.
     */
    public function destroy(Logistic $logistic): RedirectResponse
    {
        $itemName = $logistic->item_name;
        $logistic->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Item logistik '{$itemName}' telah dihapus dari inventaris.");
    }
}

