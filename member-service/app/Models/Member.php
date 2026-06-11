<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Member
 * Service: MemberService (Port 8001)
 */
class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'nama_member',
        'nim',
        'email',
    ];
}
