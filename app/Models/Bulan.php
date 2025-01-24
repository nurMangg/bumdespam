<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulan extends Model
{
    protected $table = 'msbulan';

    public $timestamps = false;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'bulan',
    ];
}
