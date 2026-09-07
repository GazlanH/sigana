<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'nomor_tiket',
        'judul',
        'kecamatan_id',
        'wilayah_terdampak',
        'penyebab',
        'tindakan',
        'dampak_aliran',
        'status',
        'waktu_mulai',
        'estimasi_selesai',
        'waktu_selesai_aktual',
        'kontak_posko',
        'created_by',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'estimasi_selesai' => 'datetime',
        'waktu_selesai_aktual' => 'datetime',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate Nomor Tiket Otomatis (GNG-YYYYMM-XXX)
     */
    public static function generateNomorTiket()
    {
        $prefix = 'GNG-' . date('Ym') . '-';
        $last = self::where('nomor_tiket', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_tiket, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }
}
