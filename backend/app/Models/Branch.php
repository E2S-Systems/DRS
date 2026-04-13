<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'corporate_name',
        'slug_name',
        'document',
        'state_registration',
        'email',
        'phone',
        'zip_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
