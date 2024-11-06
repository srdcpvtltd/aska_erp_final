<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FarmerRegistrationRequest;
use App\Models\Farming;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'registration_no' => "ACSI" . '-' . rand(0, 9999),
                'created_by' => Auth::user()->id
            ]);
            $farming = Farming::create($request->all());
            if ($farming) {
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

    public function delete_farmer(Request $request)
    {
        try {
            $farmar_id = $request->id;
            $farming = Farming::find($farmar_id);
            if (!$farming) {
                return response()->json([
                    "message" => "No record found in this id",
                    "code" => 500
                ], 200);
            }
            $result = $farming->delete();
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

    public function update_farmer(Request $request)
    {
        try {
            $id = $request->id;
            $farming = Farming::find($id);
            if (!$farming) {
                return response()->json([
                    "message" => "Please verify the ID and try again.",
                    "code" => 500
                ]);
            }
            if ($farming->registration_no == NULL) {
                $request->merge([
                    'registration_no' => "ACSI" . '-' . rand(0, 9999)
                ]);
            }
            $result = $farming->update($request->all());
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

    public function retrive_farmers(Request $request)
    {
        try {
            if ($request->old_g_code) {
                $query = Farming::where('old_g_code', $request->old_g_code)->with(['state', 'district', 'block']);
            }

            if ($request->farmer_id) {
                $query = Farming::where('id', $request->farmer_id)->with(['state', 'district', 'block']);
            } elseif ($request->old_g_code) {
                $query = Farming::where('old_g_code', $request->old_g_code)->with(['state', 'district', 'block']);
            } else {
                $query = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                    ->where('farmings.created_by', Auth::user()->id)
                    ->orWhere('users.supervisor_id', Auth::user()->id)
                    ->orderBy('id', 'desc');
            }
            $farmer_data = $query->get()->map(function ($items) {
                $data = $items->toArray();
                $data['state_name'] = $items->state->name ?? null;
                $data['district_name'] = $items->district->name ?? null;
                $data['block_name'] = $items->block->name ?? null;
                unset($data['district']);
                unset($data['state']);
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
