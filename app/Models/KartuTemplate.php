<?php

namespace App\Models;

use Database\Factories\KartuTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama_template', 'warna_utama', 'warna_sekunder',
    'logo_path', 'is_active',
])]
class KartuTemplate extends Model
{
    /** @use HasFactory<KartuTemplateFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'kartu_templates';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all cards using this template.
     */
    public function kartuAnggota(): HasMany
    {
        return $this->hasMany(KartuAnggota::class, 'template_id');
    }
}
