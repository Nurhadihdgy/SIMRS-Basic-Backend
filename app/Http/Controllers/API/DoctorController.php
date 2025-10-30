<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SimrsDoctor;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if (Auth::user() == null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|max:15|unique:simrs_doctors,doctor_id',
            'doctor_name' => 'required',
            'doctor_gender' => 'required|in:M,F',
            'doctor_phone_number' => 'required',
            'doctor_address' => 'required',
            'doctor_email' => 'required|email|unique:simrs_doctors,doctor_email',
            'doctor_bio' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        SimrsDoctor::create($request->all());
        return response()->json(['message' => 'Doctor created'], 200);
    }

    public function update(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if (Auth::user() == null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $doctor = SimrsDoctor::find($id);
        if (!$doctor) return response()->json(['message' => 'Doctor not found'], 404);

        $validator = Validator::make($request->all(), [
            'doctor_name' => 'required',
            'doctor_gender' => 'required|in:M,F',
            'doctor_phone_number' => 'required',
            'doctor_address' => 'required',
            'doctor_email' => 'required|email',
            'doctor_bio' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        //updated_at field will be automatically updated
        $updateData = $request->only([
            'doctor_name',
            'doctor_gender',
            'doctor_phone_number',
            'doctor_address',
            'doctor_email',
            'doctor_bio'
        ]);

        $doctor->update(array_merge($updateData, ['updated_at' => now()]));

        return response()->json(['message' => 'Doctor modified'], 200);
    }

    public function destroy($id)
    {
        if (Auth::user() == null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $doctor = SimrsDoctor::find($id);
        if (!$doctor) return response()->json(['message' => 'Doctor not found'], 404);

        if ($doctor->schedules()->exists()) {
            return response()->json(['message' => 'Doctor cannot deleted'], 422);
        }

        $doctor->delete();
        return response()->json(null, 204);
    }

    public function index(Request $request)
    {
        //Response Invalid Token when token is not provided
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if (Auth::user() == null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);

        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:0',
            'size' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        $data = SimrsDoctor::with('schedules')->skip($page * $size)->take($size)->get();
        return response()->json([
            'page' => (int)$page,
            'size' => (int)$size,
            'doctors' => $data
        ], 200);
    }
}
