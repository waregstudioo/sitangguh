<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class ReportImageStorage
{
    /**
     * Determine whether Cloudinary URL is configured.
     */
    public function isCloudinaryConfigured(): bool
    {
        return filled(config('cloudinary.url'));
    }

    /**
     * Compress the uploaded report photo with Intervention Image and store it on Cloudinary (or local public disk as fallback).
     *
     * @return array{path: string, public_id: ?string}
     */
    public function store(UploadedFile $uploadedFile): array
    {
        // Raise memory_limit from 128M default to 256M for GD decoding while staying safe on 512MB containers
        @ini_set('memory_limit', '256M');

        $imageManager = ImageManager::usingDriver(Driver::class);
        $image = $imageManager->decodePath($uploadedFile->getRealPath());

        $image->scaleDown(width: 1280, height: 1280);

        $encodedImage = (string) $image->encode(new JpegEncoder(quality: 70, progressive: true, strip: true));
        unset($image);

        $filenameBase = now()->format('Ymd_His').'_'.Str::lower(Str::random(10));

        if ($this->isCloudinaryConfigured()) {
            $dataUri = 'data:image/jpeg;base64,'.base64_encode($encodedImage);

            $result = $this->makeCloudinaryClient()->uploadApi()->upload($dataUri, [
                'folder' => config('cloudinary.folder', 'kalikurmo/reports'),
                'public_id' => $filenameBase,
                'resource_type' => 'image',
                'overwrite' => true,
            ]);

            return [
                'path' => (string) ($result['secure_url'] ?? $result['url']),
                'public_id' => isset($result['public_id']) ? (string) $result['public_id'] : null,
            ];
        }

        $relativePath = 'reports/'.$filenameBase.'.jpg';
        Storage::disk('public')->put($relativePath, $encodedImage);

        return [
            'path' => $relativePath,
            'public_id' => null,
        ];
    }

    /**
     * Delete a stored report image from Cloudinary or local public disk.
     */
    public function delete(?string $imagePath, ?string $publicId = null): void
    {
        if (filled($publicId) && $this->isCloudinaryConfigured()) {
            $this->makeCloudinaryClient()->uploadApi()->destroy($publicId, [
                'resource_type' => 'image',
            ]);

            return;
        }

        if (filled($imagePath)
            && ! str_starts_with($imagePath, 'http://')
            && ! str_starts_with($imagePath, 'https://')
            && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Instantiate a configured Cloudinary client from CLOUDINARY_URL.
     */
    protected function makeCloudinaryClient(): Cloudinary
    {
        return new Cloudinary((string) config('cloudinary.url'));
    }
}

