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

    public function update_guarentor(Request $request)
    {
        try {
            $id = $request->id;
            $guarantor = Guarantor::find($id);
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

    public function retrive_guarantor()
    {
        try {
            $farmer_data = Guarantor::where('created_by', Auth::user()->id)
                ->with(['block', 'village', 'district'])
                ->orderBy('id', 'desc')
                ->get()->map(function ($items) {
                    $data = $items->toArray();
                    $data['village_name'] = $items->village->name ?? null;
                    $data['district_name'] = $items->district->name ?? null;
                    $data['block_name'] = $items->block->name ?? null;
                    unset($data['district']);
                    unset($data['village']);
                    unset($data['block']);
                    return $data;
                });

            if ($farmer_data->isEmpty()) {
                return response()->json([
                    "message" => "No data found",
                    "code" => 500
                ]);
            } else {
                return response()->json([
                    "data" => $farmer_data,
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
