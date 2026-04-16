<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'idpropinsi', 'kabupaten', 'idkabsatusehat', 'code'];

    public function propinsi()
    {
        return $this->belongsTo(Propinsi::class, 'idpropinsi', 'id');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'idkabupaten', 'id');
    }
}
