<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Denda
 * Service: DendaService (Port 8004)
 */
class Denda extends Model
{
    use HasFactory;

    protected $table = 'dendas';

    protected $fillable = [
        'pinjam_id',
        'nominal_denda',
        'status_bayar',
    ];
}
