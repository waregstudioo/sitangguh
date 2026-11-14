<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'number',
        'color',
        'order',
    ];

    /**
     * Get default emergency contacts if none exist in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function getAllSorted()
    {
        $contacts = static::query()->orderBy('order')->orderBy('id')->get();

        if ($contacts->isEmpty()) {
            static::seedDefaults();
            $contacts = static::query()->orderBy('order')->orderBy('id')->get();
        }

        return $contacts;
    }

    /**
     * Seed initial default contacts.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            [
                'name' => 'Posko Balai Desa',
                'number' => '0812-3456-7890',
                'color' => 'text-white',
                'order' => 1,
            ],
            [
                'name' => 'Call Center BPBD',
                'number' => '112 / (0298) 555-119',
                'color' => 'text-amber-400',
                'order' => 2,
            ],
            [
                'name' => 'Puskesmas Kalikurmo',
                'number' => '0811-2233-4455',
                'color' => 'text-emerald-400',
                'order' => 3,
            ],
        ];

        foreach ($defaults as $item) {
            static::query()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}

