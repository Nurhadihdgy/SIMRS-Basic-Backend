<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SimrsPoliklinik;
use Illuminate\Support\Facades\Auth;

class PoliklinikController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'pol_id' => 'required|max:10|unique:simrs_poliklinik,pol_id',
            'pol_name' => 'required',
            'pol_description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        SimrsPoliklinik::create($request->only(['pol_id', 'pol_name', 'pol_description']));
        return response()->json(['message' => 'Poliklinik is created'], 200);
    }

    public function update(Request $request, $id)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $pol = SimrsPoliklinik::find($id);
        if (!$pol) return response()->json(['message' => 'Poliklinik not found'], 404);

        $validator = Validator::make($request->all(), [
            'pol_name' => 'required',
            'pol_description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        $pol->update($request->only(['pol_name', 'pol_description']) + ['updated_at' => now()]);
        return response()->json(['message' => 'Poliklinik modified'], 200);
    }

    public function destroy($id)
    {
        if(Auth::user() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $pol = SimrsPoliklinik::find($id);
        if (!$pol) return response()->json(['message' => 'Poliklinik not found'], 404);

        if ($pol->schedules()->exists()) {
            return response()->json(['message' => 'Poliklinik cannot deleted'], 422);
        }

        $pol->delete();
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
            'page' => 'integer|min:0',
            'size' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid field', 'errors' => $validator->errors()], 422);
        }

        $data = SimrsPoliklinik::with('schedules')->skip($page * $size)->take($size)->get();
        return response()->json([
            'page' => (int)$page,
            'size' => (int)$size,
            'polikliniks' => $data
        ], 200);
    }
}
