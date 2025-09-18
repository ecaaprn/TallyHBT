<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'Tanggal',
        'NoShift',
        'Kapal',
        'NoJobOrder',
        'NoTruck',
        'WaktuTiba',
        'user_id',
    ];

    public function timeList()
    {
        return $this->hasOne(TimeList::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
