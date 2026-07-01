<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_order_id',
        'truck_no',
        'NoPalka',
        'NoHose',
        'Catatan',
        'plugging',
        'open_valve',
        'close_valve',
        'unplugging',
        'kategori',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
