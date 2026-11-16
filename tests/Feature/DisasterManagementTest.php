<?php

use App\Models\AlertStatus;
use App\Models\Logistic;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('homepage renders global alert banner, leaflet map centered on hardcoded Kalikurmo coordinates, dynamic report markers, and logistics', function (): void {
    Logistic::query()->create([
        'item_name' => 'Tenda Regu Darurat',
        'quantity' => 12,
        'unit' => 'Unit',
        'location' => 'Posko Utama Balai Desa',
    ]);

    Report::query()->create([
        'reporter_name' => 'Slamet Riyadi',
        'category' => 'Tanah Longsor',
        'description' => 'Longsor tebing jalan desa',
        'latitude' => -7.1923420,
        'longitude' => 110.5634810,
        'image_path' => 'reports/sample.jpg',
        'status' => 'Pending',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('SI-TANGGUH')
        ->assertSee('setView([-7.192342, 110.563481], 15)', false)
        ->assertSee('Slamet Riyadi')
        ->assertSee('Tenda Regu Darurat')
        ->assertSee('Status Desa Kalikurmo: AMAN (Normal)')
        ->assertDontSee('navigator.geolocation');

    $this->get(route('reports.create'))
        ->assertOk()
        ->assertSee('-7.192342')
        ->assertSee('110.563481')
        ->assertDontSee('navigator.geolocation');
});

test('citizen report submission with latitude and longitude compresses uploaded photo via Intervention Image and stores report', function (): void {
    config(['cloudinary.url' => null]);
    Storage::fake('public');

    $fakeImage = UploadedFile::fake()->image('longsor-kalikurmo.png', 1800, 1400);

    $response = $this->post(route('reports.store'), [
        'reporter_name' => 'Ahmad Fauzi',
        'category' => 'Tanah Longsor',
        'description' => 'Tebing di dekat jalan Dusun Krajan RT 02 mengalami longsor sepanjang 5 meter.',
        'latitude' => -7.192342,
        'longitude' => 110.563481,
        'image' => $fakeImage,
    ]);

    $response->assertRedirect(route('home'))
        ->assertSessionHas('success');

    $report = Report::query()->first();

    expect($report)->not->toBeNull()
        ->and($report->reporter_name)->toBe('Ahmad Fauzi')
        ->and($report->category)->toBe('Tanah Longsor')
        ->and($report->latitude)->toEqualWithDelta(-7.192342, 0.0001)
        ->and($report->longitude)->toEqualWithDelta(110.563481, 0.0001)
        ->and($report->image_path)->toStartWith('reports/')
        ->and($report->image_path)->toEndWith('.jpg');

    Storage::disk('public')->assertExists($report->image_path);
});

test('guest cannot access admin dashboard and is redirected to login', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('admin can log in using username and password', function (): void {
    $response = $this->post(route('login.post'), [
        'username' => 'admin',
        'password' => 'admin',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('admin can update AlertStatus and it reflects globally on homepage banner', function (): void {
    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    $response = $this->actingAs($admin)->put(route('admin.alert-status.update'), [
        'level' => AlertStatus::LEVEL_EMERGENCY,
        'headline' => 'DARURAT BENCANA LONGSOR DUSUN KRAJAN',
        'message' => 'Seluruh warga RT 01-03 harap segera menuju titik kumpul Balai Desa.',
        'updated_by' => 'Kepala Desa Kalikurmo',
    ]);

    $response->assertRedirect(route('admin.dashboard'));

    expect(AlertStatus::current()->level)->toBe(AlertStatus::LEVEL_EMERGENCY);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('SIAGA I • DARURAT')
        ->assertSee('DARURAT BENCANA LONGSOR DUSUN KRAJAN');
});

test('admin can perform full CRUD operations on Logistics', function (): void {
    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    // Create
    $this->actingAs($admin)->post(route('logistics.store'), [
        'item_name' => 'Selimut Tebal',
        'quantity' => 90,
        'unit' => 'Lembar',
        'location' => 'Posko Balai Desa',
    ])->assertRedirect(route('admin.dashboard'));

    $logistic = Logistic::query()->where('item_name', 'Selimut Tebal')->first();
    expect($logistic)->not->toBeNull()
        ->and($logistic->quantity)->toBe(90);

    // Update
    $this->actingAs($admin)->put(route('logistics.update', $logistic), [
        'item_name' => 'Selimut Tebal Darurat',
        'quantity' => 140,
        'unit' => 'Lembar',
        'location' => 'Posko Utama',
    ])->assertRedirect(route('admin.dashboard'));

    expect($logistic->fresh()->item_name)->toBe('Selimut Tebal Darurat')
        ->and($logistic->fresh()->quantity)->toBe(140);

    // Delete
    $this->actingAs($admin)->delete(route('logistics.destroy', $logistic))
        ->assertRedirect(route('admin.dashboard'));

    expect(Logistic::query()->find($logistic->id))->toBeNull();
});

test('admin can perform CRUD on emergency contacts and reflects in footer', function (): void {
    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    // Create
    $this->actingAs($admin)->post(route('emergency-contacts.store'), [
        'name' => 'Posko SAR Kalikurmo',
        'number' => '0899-1122-3344',
        'color' => 'text-amber-400',
    ])->assertRedirect(route('admin.dashboard'));

    $contact = \App\Models\EmergencyContact::query()->where('name', 'Posko SAR Kalikurmo')->first();
    expect($contact)->not->toBeNull()
        ->and($contact->number)->toBe('0899-1122-3344');

    // Check footer has the contact
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Posko SAR Kalikurmo')
        ->assertSee('0899-1122-3344');

    // Update
    $this->actingAs($admin)->put(route('emergency-contacts.update', $contact), [
        'name' => 'Posko SAR Utama',
        'number' => '0899-9999-0000',
        'color' => 'text-emerald-400',
    ])->assertRedirect(route('admin.dashboard'));

    expect($contact->fresh()->name)->toBe('Posko SAR Utama')
        ->and($contact->fresh()->number)->toBe('0899-9999-0000');

    // Delete
    $this->actingAs($admin)->delete(route('emergency-contacts.destroy', $contact))
        ->assertRedirect(route('admin.dashboard'));

    expect(\App\Models\EmergencyContact::query()->find($contact->id))->toBeNull();
});

test('admin can update password with valid current password', function (): void {
    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    $response = $this->actingAs($admin)->put(route('admin.password.update'), [
        'current_password' => 'admin',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $response->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('success');

    expect(Hash::check('rahasia123', $admin->fresh()->password))->toBeTrue();
});

test('admin password update fails when current password is incorrect', function (): void {
    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    $response = $this->actingAs($admin)->put(route('admin.password.update'), [
        'current_password' => 'salah',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $response->assertSessionHasErrors('current_password');
    expect(Hash::check('admin', $admin->fresh()->password))->toBeTrue();
});

test('report image_url returns external cloudinary url directly and stores public_id', function (): void {
    $cloudUrl = 'https://res.cloudinary.com/demo/image/upload/v123/kalikurmo/reports/test.jpg';

    $mockStorage = Mockery::mock(\App\Services\ReportImageStorage::class);
    $mockStorage->shouldReceive('store')
        ->once()
        ->andReturn([
            'path' => $cloudUrl,
            'public_id' => 'kalikurmo/reports/test',
        ]);
    $mockStorage->shouldReceive('delete')
        ->once()
        ->with($cloudUrl, 'kalikurmo/reports/test');

    $this->app->instance(\App\Services\ReportImageStorage::class, $mockStorage);

    $fakeImage = UploadedFile::fake()->image('banjir.png', 1000, 800);

    $this->post(route('reports.store'), [
        'reporter_name' => 'Warga Cloudinary',
        'category' => 'Banjir / Luapan Sungai',
        'description' => 'Air meluap ke jalan dusun.',
        'latitude' => -7.192342,
        'longitude' => 110.563481,
        'image' => $fakeImage,
    ])->assertRedirect(route('home'));

    $report = Report::query()->where('reporter_name', 'Warga Cloudinary')->first();

    expect($report)->not->toBeNull()
        ->and($report->image_path)->toBe($cloudUrl)
        ->and($report->image_public_id)->toBe('kalikurmo/reports/test')
        ->and($report->image_url)->toBe($cloudUrl);

    $admin = User::query()->firstOrCreate(
        ['username' => 'admin'],
        [
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]
    );

    $this->actingAs($admin)
        ->delete(route('reports.destroy', $report))
        ->assertRedirect(route('admin.dashboard'));

    expect(Report::query()->find($report->id))->toBeNull();
});
