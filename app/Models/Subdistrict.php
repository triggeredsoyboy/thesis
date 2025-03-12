<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subdistrict extends Model
{
    /** @use HasFactory<\Database\Factories\SubdistrictFactory> */
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
            'vulnerabilities' => 'array',
        ];
    }

    /**
     * Get the prone area that owns the subdistrict.
     */
    public function proneAreas(): BelongsToMany
    {
        return $this->BelongsToMany(ProneArea::class, 'prone_area_subdistrict');
    }
}
