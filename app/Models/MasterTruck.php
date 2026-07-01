<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTruck extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kapal_id',
    ];

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }
}

