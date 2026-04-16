<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = 'kelurahan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'idkecamatan', 'idkecsatusehat', 'kodebps', 'kelurahan', 'code'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'idkecamatan', 'id');
    }
}
