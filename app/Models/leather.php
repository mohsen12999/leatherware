<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leather extends Model
{
    /** @use HasFactory<\Database\Factories\LeatherFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'butcher_id', 'cow', 'sheep', 'goat', 'loading_date' // , 'loading'
    ];

    public function butcher()
    {
        return $this->belongsTo(Butcher::class);
    }

}
