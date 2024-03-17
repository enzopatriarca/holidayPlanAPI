<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayPlan extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'location','user_id'];

    public function participants()
    {
        return $this->belongsToMany(Participant::class)
                    ->using(HolidayPlanParticipants::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

