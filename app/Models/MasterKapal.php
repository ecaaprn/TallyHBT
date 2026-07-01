<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKapal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal',
        'status'
    ];

    public function trucks()
    {
        return $this->hasMany(MasterTruck::class, 'kapal_id');
    }
}
