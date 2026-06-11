<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pinjam
 * Service: PinjamService (Port 8003)
 */
class Pinjam extends Model
{
    use HasFactory;

    protected $table = 'pinjams';

    protected $fillable = [
        'member_id',
        'buku_id',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
    ];
}
