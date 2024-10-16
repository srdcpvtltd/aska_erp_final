<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GuarantorRequest;
use App\Models\Farming;
use App\Models\Guarantor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuarantorController extends Controller
{
    public function create_guarantor(GuarantorRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            $request->merge([
                'created_by' => Auth::user()->id
            ]);
            $guarantor = Guarantor::create($request->all());
            if ($guarantor) {
                DB::commit();
                return response()->json([
                    "message" => "Record Created Successfully.",
                    "code" => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Something went wrong",
                    "code" => 500
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                'code' => 500
            ]);
        }
    }

    public function delete_guarantor(Request $request)
    {
        try {
            $guarantor_id = $request->id;
            $guarantor = Guarantor::find($guarantor_id);
            if (!$guarantor) {
                return response()->json([
                    "message" => "No record found in this id",
                    "code" => 500
                ], 200);
            }
            $result = $guarantor->delete();
            if ($result) {
                return response()->json([
                    "message" => "Record Deleted Successfully",
                    "code" => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Record Not found",
                    "code" => 500
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => 500
            ]);
        }
    }
}
