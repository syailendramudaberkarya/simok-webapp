<?php

namespace App\Models;

use Database\Factories\KartuAnggotaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'anggota_id', 'nomor_anggota', 'qr_code_url',
    'pdf_path', 'template_id', 'berlaku_hingga', 'generated_at',
])]
class KartuAnggota extends Model
{
    /** @use HasFactory<KartuAnggotaFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'kartu_anggota';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'berlaku_hingga' => 'date',
            'generated_at' => 'datetime',
        ];
    }

    /**
     * Get the anggota that owns the card.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /**
     * Get the template used for this card.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(KartuTemplate::class, 'template_id');
    }

    /**
     * Check if the card is still valid.
     */
    public function isValid(): bool
    {
        return $this->berlaku_hingga && $this->berlaku_hingga->isFuture();
    }
}
