<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesMenu extends Model
{
    use HasFactory;

    protected $table = 'akses_menus';

    protected $fillable = [
        'user_id',
        'cabang_id',
        'akses_cabang',
        'akses_menu'
    ];

    protected $casts = [
        'akses_menu' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cabang()
    {
        return $this->belongsTo(MasterCabang::class, 'cabang_id');
    }
}
