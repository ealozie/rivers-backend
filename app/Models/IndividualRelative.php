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
        return $this->belongsTo(Individual::class, 'entity_id', 'individual_id');
    }

    public function relative()
    {
        return $this->belongsTo(Individual::class, 'relative_id', 'individual_id');
    }

    public static function check_for_duplicates($entityId, $relativeId)
    {
        return self::where(function ($query) use ($entityId, $relativeId) {
            $query->where('entity_id', $entityId)->where('relative_id', $relativeId);
        })
        ->orWhere(function ($query) use ($entityId, $relativeId) {
            $query->where('entity_id', $relativeId)->where('relative_id', $entityId);
        })
        ->exists();
    }
}
