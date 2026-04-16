<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'idkabupaten', 'kecamatan', 'idkecsatusehat', 'code'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'idkabupaten', 'id');
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'idkecamatan', 'id');
    }
}
