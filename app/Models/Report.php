<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reporter_name',
        'category',
        'description',
        'latitude',
        'longitude',
        'image_path',
        'image_public_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get the public URL for the compressed report photo.
     */
    public function getImageUrlAttribute(): string
    {
        if (str_starts_with((string) $this->image_path, 'http://')
            || str_starts_with((string) $this->image_path, 'https://')) {
            return (string) $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
