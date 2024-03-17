<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HolidayPlanParticipants extends Pivot
{
    protected $fillable = ['holiday_plan_id', 'participant_id'];
    protected $table = 'holiday_plan_participant';
    public function holidayPlan()
    {
        return $this->belongsTo(HolidayPlan::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}

