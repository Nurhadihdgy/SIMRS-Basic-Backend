<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SimrsSchedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:simrs_doctors,doctor_id',
            'pol_id' => 'required|exists:simrs_poliklinik,pol_id',
            'schedule_date' => 'required|date|after:today',
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i|after:schedule_start',
        ]);

        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        // cek konflik jadwal
        $conflict = SimrsSchedule::where('doctor_id', $request->doctor_id)
            ->where('schedule_date', $request->schedule_date)
            ->where(function ($q) use ($request) {
                $q->whereBetween('schedule_start', [$request->schedule_start, $request->schedule_end])
                  ->orWhereBetween('schedule_end', [$request->schedule_start, $request->schedule_end]);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Schedule conflict'], 422);
        }

        SimrsSchedule::create($request->all());
        return response()->json(['message' => 'Schedule created'], 200);
    }
    public function update(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $schedule = SimrsSchedule::find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:simrs_doctors,doctor_id',
            'pol_id' => 'required|exists:simrs_poliklinik,pol_id',
            'schedule_date' => 'required|date|after:today',
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i|after:schedule_start',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        // cek konflik jadwal
        $conflict = SimrsSchedule::where('doctor_id', $request->doctor_id)
            ->where('schedule_date', $request->schedule_date)
            ->where('schedule_id', '!=', $id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('schedule_start', [$request->schedule_start, $request->schedule_end])
                  ->orWhereBetween('schedule_end', [$request->schedule_start, $request->schedule_end]);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Schedule conflict'], 422);
        }

        $schedule->update(array_merge($request->all(), ['updated_at' => now()]));
        return response()->json(['message' => 'Schedule modified'], 200);
    }


    public function destroy($id)
    {
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        

        $schedule = SimrsSchedule::find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }   
        //Response Constraint Error when schedule is referenced in another table
        if ($schedule->poliklinik()->exists() || $schedule->doctor()->exists()) {
            return response()->json(['message' => 'Schedule cannot be deleted'], 422);
        }
        //Cannot delete if the schedule date is today or in the past
        if (strtotime($schedule->schedule_date) <= strtotime(date('Y-m-d'))) {
            return response()->json(['message' => 'Schedule cannot be deleted'], 422);
        }

        $schedule->delete();
        return response()->json(null, 204);
    }

    public function index(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'nullable|exists:simrs_doctors,doctor_id',
            'pol_id' => 'nullable|exists:simrs_poliklinik,pol_id',
            'page' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid fields', 'errors' => $validator->errors()], 422);
        }
        

        $query = SimrsSchedule::with(['doctor', 'poliklinik']);
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->has('pol_id')) {
            $query->where('pol_id', $request->pol_id);
        }
        $schedules = $query->skip($page * $size)->take($size)->get();
        return response()->json([
            'page' => (int)$page,
            'size' => (int)$size,
            'schedules' => $schedules
        ], 200);
    }
}
