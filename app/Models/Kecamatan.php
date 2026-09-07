<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kecamatan',
        'cabang_pelayanan',
    ];

    public function pengumumans()
    {
        return $this->hasMany(Pengumuman::class, 'kecamatan_id');
    }
}
