<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualRelative extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function relative()
    {
        return $this->belongsTo(Individual::class, 'relative_id');
    }
}
