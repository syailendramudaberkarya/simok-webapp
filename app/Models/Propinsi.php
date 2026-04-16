<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propinsi extends Model
{
    protected $table = 'propinsi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'code', 'propinsi', 'latitude', 'longitude', 'island', 'timezone'];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'idpropinsi', 'id');
    }
}
