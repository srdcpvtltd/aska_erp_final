<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SecurityDepositeRequest;
use App\Models\Farming;
use App\Models\FarmingPayment;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityDepositeController extends Controller
{
    //to store the securty deposit
    public function store_deposites(SecurityDepositeRequest $request)
    {
        try {
            //check if the the farmer already deposit any amount or not
            $farming_payment = FarmingPayment::where('farming_id', $request->farming_id)->where('type', "Security Deposit")->get();
            if (count($farming_payment) > 0) {
                return response()->json([
                    'message' => "Farmer Already have a Security Deposite",
                    'code' => 500
                ]);
            }
            if ($request->type != null) {
                $type = $request->type;
            } else {
                $type = "Security Deposit";
            }

            $security_deposite = new FarmingPayment;
            $security_deposite->farming_id = $request->farming_id;
            $security_deposite->receipt_no = $request->receipt_no;
            $security_deposite->receipt_type = $request->receipt_type;
            $security_deposite->agreement_number = $request->agreement_number;
            $security_deposite->date = $request->date;
            $security_deposite->amount = $request->amount;
            $security_deposite->type = $type;
            $security_deposite->bank = $request->bank;
            $security_deposite->loan_account_number = $request->loan_account_number;
            $security_deposite->ifsc = $request->ifsc;
            $security_deposite->branch = $request->branch;
            $security_deposite->created_by = Auth::user()->id;
            $result = $security_deposite->save();
            if ($result) {
                return response()->json([
                    'message' => "Deposit Successfull ",
                    'code' => 200
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'code' => 500
            ]);
        }
    }

    //delete security deposites
    public function delete_deposites(Request $request)
    {
        try {
            $deposit_id = $request->id;
            $deposits = FarmingPayment::find($deposit_id);
            if (!$deposits) {
                return response()->json([
                    "message" => "No record found in this id",
                    "code" => 500
                ], 200);
            }
            $result = $deposits->delete();
            if ($result) {
                return response()->json([
                    "message" => "Record Deleted Successfully",
                    "code" => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Failed to delete",
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

    public function update_deposites(Request $request)
    {
        try {
            $id = $request->id;
            $deposits = FarmingPayment::find($id);
            if (!$deposits) {
                return response()->json([
                    "message" => "Please verify the ID and try again.",
                    "code" => 500
                ]);
            }
            if ($deposits->registration_no == NULL) {
                $request->merge([
                    'registration_no' => "ACSI" . '-' . rand(0, 9999)
                ]);
            }
            $result = $deposits->update($request->all());
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
}
