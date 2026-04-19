<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengurus extends Model
{
    use HasFactory;

    protected $table = 'pengurus';

    protected $fillable = [
        'noanggota',
        'anggota_id',
        'nama',
        'kategorijabatan',
        'subkategorijabatan',
        'jabatan',
        'kantor',
        'kantor_id',
        'keterangan',
    ];

    /**
     * Relasi ke model Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    /**
     * Relasi ke model Kantor
     */
    public function dataKantor(): BelongsTo
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }
}
