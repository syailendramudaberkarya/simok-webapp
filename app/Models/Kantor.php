<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'nama_kantor',
    'jenjang',
    'telepon',
    'email',
    'alamat',
    'kode_pos',
    'idpropinsi',
    'idkabupaten',
    'idkecamatan',
    'idkelurahan',
    'provinsi',
    'kabupaten',
    'kecamatan',
    'kelurahan',
    'latitude',
    'longitude',
    'status',
    'parent_id',
])]
class Kantor extends Model
{
    use HasFactory, \App\Traits\HasHierarchicalScope;

    protected $table = 'kantor';

    /**
     * Get the parent office
     */
    public function parent()
    {
        return $this->belongsTo(Kantor::class, 'parent_id');
    }

    /**
     * Get the child offices
     */
    public function children()
    {
        return $this->hasMany(Kantor::class, 'parent_id');
    }

    /**
     * Scope for active offices.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }
}
