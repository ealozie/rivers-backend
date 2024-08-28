<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DemandNotice extends Model
{
    use HasFactory;


    protected $guarded = ['id'];


    public function year()
    {
        return $this->belongsTo(AssessmentYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function demand_notice_category()
    {
        return $this->belongsTo(DemandNoticeCategory::class);
    }

    public function user_generated()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function user_served()
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function demand_notice_items()
    {
        return $this->hasMany(DemandNoticeItem::class);
    }

    //morph
    public function demand_noticeable(): MorphTo {
        return $this->morphTo();
    }
}
