<?php

namespace App\Http\Controllers;

use App\Models\AlertStatus;
use App\Models\EmergencyContact;
use App\Models\Logistic;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlertStatusController extends Controller
{
    /**
     * Display the unified admin dashboard for managing Village AlertStatus, Logistics CRUD, and Citizen Reports.
     */
    public function index(?Logistic $editingLogistic = null): View
    {
        return view('admin.dashboard', [
            'alertStatus' => AlertStatus::current(),
            'logistics' => Logistic::query()->latest()->get(),
            'reports' => Report::query()->latest()->paginate(8),
            'emergencyContacts' => EmergencyContact::getAllSorted(),
            'editingLogistic' => $editingLogistic?->exists ? $editingLogistic : null,
        ]);
    }

    /**
     * Update the single-row village emergency level (Safe, Alert, Emergency).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level' => ['required', Rule::in(AlertStatus::LEVELS)],
            'headline' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string', 'max:1000'],
            'updated_by' => ['nullable', 'string', 'max:100'],
        ]);

        $defaultHeadlines = [
            AlertStatus::LEVEL_SAFE => 'Status Desa Kalikurmo: AMAN (Normal)',
            AlertStatus::LEVEL_ALERT => 'Status Desa Kalikurmo: WASPADA (Siaga Bencana)',
            AlertStatus::LEVEL_EMERGENCY => 'PERINGATAN DARURAT DESA KALIKURMO: EVAKUASI SEGERA!',
        ];

        $defaultMessages = [
            AlertStatus::LEVEL_SAFE => 'Kondisi wilayah Desa Kalikurmo terpantau aman. Warga diimbau tetap menjaga kesiapsiagaan.',
            AlertStatus::LEVEL_ALERT => 'Potensi bencana meningkat akibat cuaca ekstrem. Warga di zona rawan harap bersiap menuju titik kumpul.',
            AlertStatus::LEVEL_EMERGENCY => 'Terjadi kondisi darurat bencana! Segera ikuti jalur evakuasi menuju Posko Balai Desa Kalikurmo.',
        ];

        $alertStatus = AlertStatus::current();

        $alertStatus->update([
            'level' => $validated['level'],
            'headline' => filled($validated['headline'] ?? null)
                ? $validated['headline']
                : $defaultHeadlines[$validated['level']],
            'message' => filled($validated['message'] ?? null)
                ? $validated['message']
                : $defaultMessages[$validated['level']],
            'updated_by' => filled($validated['updated_by'] ?? null)
                ? $validated['updated_by']
                : 'Posko Komando Desa',
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Status darurat desa berhasil diperbarui menjadi: {$alertStatus->label()}.");
    }
}

