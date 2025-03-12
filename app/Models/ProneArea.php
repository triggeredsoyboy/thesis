<?php

namespace App\Models;

use App\Enums\ProneZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProneArea extends Model
{
    /** @use HasFactory<\Database\Factories\ProneAreaFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'zone' => ProneZone::class,
        ];
    }

    /**
     * Get the subdistricts for the prone area.
     */
    public function subdistricts(): BelongsToMany
    {
        return $this->belongsToMany(Subdistrict::class, 'prone_area_subdistrict');
    }
}
