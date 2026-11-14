<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertStatus extends Model
{
    use HasFactory;

    public const LEVEL_SAFE = 'Safe';

    public const LEVEL_ALERT = 'Alert';

    public const LEVEL_EMERGENCY = 'Emergency';

    /**
     * Allowed emergency levels.
     *
     * @var list<string>
     */
    public const LEVELS = [
        self::LEVEL_SAFE,
        self::LEVEL_ALERT,
        self::LEVEL_EMERGENCY,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'level',
        'headline',
        'message',
        'updated_by',
    ];

    /**
     * Retrieve the single active village alert status row, creating a default Safe row if none exists.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'level' => self::LEVEL_SAFE,
                'headline' => 'Status Desa Kalikurmo: AMAN (Normal)',
                'message' => 'Tidak ada ancaman bencana aktif saat ini. Warga diimbau tetap menjaga kelestarian lingkungan dan saluran drainase desa.',
                'updated_by' => 'Sistem BPBD Desa',
            ]
        );
    }

    /**
     * Get the localized Indonesian label for the current emergency level.
     */
    public function label(): string
    {
        return match ($this->level) {
            self::LEVEL_EMERGENCY => 'DARURAT (SIAGA I / AWAS)',
            self::LEVEL_ALERT => 'WASPADA (SIAGA II)',
            default => 'AMAN (NORMAL)',
        };
    }
}

