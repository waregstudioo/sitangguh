<?php

namespace App\Http\Controllers;

use App\Models\AlertStatus;
use App\Models\Logistic;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class ReportController extends Controller
{
    /**
     * Display the public disaster management homepage with incident markers, alert status, and logistics.
     */
    public function index(): View
    {
        $alertStatus = AlertStatus::current();
        $reports = Report::query()->latest()->get();
        $logistics = Logistic::query()->orderBy('item_name')->get();

        return view('home', [
            'alertStatus' => $alertStatus,
            'reports' => $reports,
            'logistics' => $logistics,
        ]);
    }

    /**
     * Show the standalone citizen incident reporting form.
     */
    public function create(): View
    {
        return view('reports.create', [
            'alertStatus' => AlertStatus::current(),
        ]);
    }

    /**
     * Store a newly submitted citizen incident report and compress its photo via Intervention Image.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reporter_name' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $uploadedFile = $request->file('image');

        $imageManager = ImageManager::usingDriver(Driver::class);
        $image = $imageManager->decodePath($uploadedFile->getRealPath());

        $image->scaleDown(width: 1280, height: 1280);

        $encodedImage = $image->encode(new JpegEncoder(quality: 70, progressive: true, strip: true));

        $relativePath = 'reports/'.now()->format('Ymd_His').'_'.Str::lower(Str::random(10)).'.jpg';

        Storage::disk('public')->put($relativePath, (string) $encodedImage);

        Report::query()->create([
            'reporter_name' => filled($validated['reporter_name'] ?? null)
                ? $validated['reporter_name']
                : 'Warga Desa',
            'category' => $validated['category'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'image_path' => $relativePath,
            'status' => 'Pending',
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Laporan kejadian berhasil dikirim dan foto telah dikompresi secara otomatis.');
    }

    /**
     * Update the verification status of a citizen report from the admin dashboard.
     */
    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Verified,Resolved'],
        ]);

        $report->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Status laporan warga berhasil diperbarui.');
    }

    /**
     * Remove a citizen report and delete its compressed image from storage.
     */
    public function destroy(Report $report): RedirectResponse
    {
        if (filled($report->image_path) && Storage::disk('public')->exists($report->image_path)) {
            Storage::disk('public')->delete($report->image_path);
        }

        $report->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Laporan kejadian berhasil dihapus.');
    }
}
