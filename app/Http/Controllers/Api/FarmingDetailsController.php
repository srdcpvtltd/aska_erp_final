<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FarmingDetailsRequest;
use App\Models\FarmingDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FarmingDetailsController extends Controller
{
    public function store_farmingDetails(FarmingDetailsRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            $farming_details = FarmingDetail::create($request->all());
            if ($farming_details) {
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
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                'code' => 500
            ]);
        }
    }

    public function delete_farmingDetails(Request $request)
    {
        try {
            $id = $request->id;
            $farmer_details = FarmingDetail::find($id);
            if (!$farmer_details) {
                return response()->json([
                    "message" => "No record found in this id",
                    "code" => 500
                ], 200);
            }
            $result = $farmer_details->delete();
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

    public function update_farmingDetails(Request $request)
    {
        try {
            $id = $request->id;
            $guarantor = FarmingDetail::find($id);
            if (!$guarantor) {
                return response()->json([
                    "message" => "Please verify the ID and try again.",
                    "code" => 500
                ]);
            }
            $result = $guarantor->update($request->all());
            if ($result) {
                return response()->json([
                    "message" => "Record updated successfully.",
                    "code" => 200
                ], 200);
            } else {
                return response()->json([
                    "message" => "We encountered an issue while updating the record. Please try again",
                    "code" => 500
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => 500
            ]);
        }
    }

    public function retrive_farmingDetails()
    {
        try {
            $farmer_details = FarmingDetail::where('created_by', Auth::user()->id)
                // ->orWhereHas('users',function ($query) {
                //     $query->where('supervisor_id', Auth::user()->id);
                // })
                ->orderBy('id', 'desc')
                ->get();

            if ($farmer_details->isEmpty()) {
                return response()->json([
                    "message" => "No data found",
                    "code" => 500
                ]); 
            } else {
                return response()->json([
                    "data" => $farmer_details,
                    "code" => 200
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => 500
            ]);
        }
    }
}
