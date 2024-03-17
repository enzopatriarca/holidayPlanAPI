<?php

namespace App\Http\Controllers;

use App\Models\HolidayPlan;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HolidayPlanController extends Controller
{
    public function index($userId)
    {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $plans = $user->holidayPlans()->with('participants')->get();
    
        return response()->json(['plans' => $plans], 200);
    }

    public function create(){
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'participants' => 'sometimes|array',
            'participants.*' => 'exists:users,id' 
        ]);
    
        
        $holidayPlan = new HolidayPlan($validated);
        $holidayPlan->user()->associate(User::find($validated['user_id']));
        $holidayPlan->save();
        
        
        $participantIds = [];
        foreach ($request->participants as $userId) {
            
            $user = User::find($userId);
            if (!$user) {
                Log::error("User ID not found:", ['id' => $userId]);
                continue; 
            }
        
            $participant = Participant::firstOrCreate(
                ['user_id' => $userId], 
                ['name' => $user->name, 'user_id' => $userId] 
            );
        
            $participantIds[] = $participant->id;
        }
    
        
        if (!empty($participantIds)) {
            $holidayPlan->participants()->attach($participantIds);
        }
    
        $newPlan = HolidayPlan::with('participants', 'user')->find($holidayPlan->id);
    
        return response()->json($newPlan, 201);
    }
    

    public function show($holiday_id)
    {
        $holidayPlan = HolidayPlan::with('participants')->find($holiday_id);
    
        if (!$holidayPlan) {
            return response()->json(['message' => 'Holiday plan not found'], 404);
        }


        return response()->json($holidayPlan);
    }

    public function edit(HolidayPlan $holidayPlan)
    {
        //
    }

    public function update(Request $request, $holiday_id)
    {
        $holidayPlan = HolidayPlan::findOrFail($holiday_id);
    
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'participants' => 'sometimes|array',
            'participants.*' => 'exists:users,id'
        ]);
    
        
        $holidayPlan->update($validated);
        
        
        if (isset($validated['participants'])) {
            $participantIds = [];
    
            foreach ($validated['participants'] as $userId) {
                $user = User::find($userId);
                if (!$user) {
                    Log::error("User ID not found:", ['id' => $userId]);
                    continue; 
                }
    
                $participant = Participant::firstOrCreate(
                    ['user_id' => $userId], 
                    ['name' => $user->name, 'user_id' => $userId]
                );
    
                $participantIds[] = $participant->id;
            }
            
            $holidayPlan->participants()->sync($participantIds);
        }
    
        
        $updatedPlan = HolidayPlan::with('participants', 'user')->find($holidayPlan->id);
    
        return response()->json($updatedPlan, 200);
    }
    

    public function destroy(Request $request, $holiday_id)
    {
        $holidayPlan = HolidayPlan::find($holiday_id);

        if (!$holidayPlan) {
            return response()->json(['message' => 'Holiday plan not found'], 404);
        }

        if ($holidayPlan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized to delete this holiday plan'], 403);
        }
        $holidayPlan->delete();

        return response()->json(['message' => 'Holiday plan deleted successfully'], 200);
    }
}
