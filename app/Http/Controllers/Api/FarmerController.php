<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FarmerRegistrationRequest;
use App\Models\Farming;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FarmerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(FarmerRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->validated();
            // Log::info($validatdData);
            $request->merge([
                'registration_no' => "ACSI" . '-' . rand(0, 9999)
            ]);
            $farming = Farming::create($request->all());
            // dd($farming);
            if ($farming) {
                DB::commit();
                return response()->json([
                    "message" => "Record Created Successfully",
                    "code" => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong",
                    "code" => 500
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                'code' => 500
            ]);
        }
    }
}
