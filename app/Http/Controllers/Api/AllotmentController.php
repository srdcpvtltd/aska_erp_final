<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AllotmentRequest;
use App\Models\FarmerLoan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllotmentController extends Controller
{
    public function store_loanAllotment(AllotmentRequest $request)
    {
        try {
            $encoded_loan_category_id = json_encode($request->loan_category_id);
            $encoded_loan_type_id = json_encode($request->loan_type_id);
            $encoded_price_kg = json_encode($request->price_kg);
            $encoded_quantity = json_encode($request->quantity);
            $encoded_total_amount = json_encode($request->total_amount);

            $farmerLoan = new FarmerLoan();
            $farmerLoan->farming_id = $request->farming_id;
            $farmerLoan->registration_number = $request->registration_number;
            $farmerLoan->agreement_number = $request->agreement_number;
            $farmerLoan->date = $request->date;
            $farmerLoan->loan_category_id = $encoded_loan_category_id;
            $farmerLoan->loan_type_id = $encoded_loan_type_id;
            $farmerLoan->price_kg = $encoded_price_kg;
            $farmerLoan->quantity = $encoded_quantity;
            $farmerLoan->total_amount = $encoded_total_amount;
            $farmerLoan->created_by = $request->created_by;
            $result = $farmerLoan->save();
            if ($result) {
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
            return response()->json([
                "message" => $e->getMessage(),
                "code" => 500
            ]);
        }
    }

    public function delete_loanAllotment(Request $request)
    {
        try {
            $id = $request->id;
            $loan_details = FarmerLoan::find($id);
            if (!$loan_details) {
                return response()->json([
                    "message" => "No record found in this id",
                    "code" => 500
                ], 200);
            }
            $result = $loan_details->delete();
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

    public function update_loanAllotment(Request $request)
    {
        try {
            $id = $request->id;
            $farmer_loan = FarmerLoan::find($id);
            if (!$farmer_loan) {
                return response()->json([
                    "message" => "Please verify the Farmer and try again.",
                    "code" => 500
                ]);
            }
            $result = $farmer_loan->update($request->all());
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

    public function retrive_loanAllotments()
    {
        try {
            $farmer_loan = FarmerLoan::where('created_by', Auth::user()->id)
                // ->orWhereHas('users',function ($query) {
                //     $query->where('supervisor_id', Auth::user()->id);
                // })
                ->orderBy('id', 'desc')
                ->get();

            if ($farmer_loan->isEmpty()) {
                return response()->json([
                    "message" => "No data found",
                    "code" => 500
                ]);
            } else {
                return response()->json([
                    "data" => $farmer_loan,
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
