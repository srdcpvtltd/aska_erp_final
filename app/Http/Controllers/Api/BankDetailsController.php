<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Farming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BankDetailsController extends Controller
{
    public function update_bankDetails(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->farming_id;
            $farming = Farming::findorfail($id);

            if ($request->finance_category === "Loan") {

                $farming->finance_category = $request->finance_category;
                $farming->non_loan_type = $request->loan_type;

                if ($request->loan_type === "Bank") {
                    $farming->bank = $request->bank;
                    $farming->account_number = $request->account_number;
                    $farming->ifsc_code = $request->ifsc_code;
                    $farming->branch = $request->branch;
                } elseif ($request->loan_type === "Co-Operative") {

                    $farming->name_of_cooperative = $request->name_of_cooperative;
                    $farming->cooperative_address = $request->cooperative_address;
                }
            } elseif ($request->finance_category === "Non-loan") {

                $farming->finance_category = $request->finance_category;
                $farming->non_loan_type = "Bank";
                $farming->bank = $request->non_loan_bank;
                $farming->account_number = $request->non_loan_account_number;
                $farming->ifsc_code = $request->non_loan_ifsc_code;
                $farming->branch = $request->non_loan_branch;
            }
            $result =  $farming->save();

            if ($result) {
                DB::commit();
                return response()->json([
                    "message" => "Bank Details Updated Successfully.",
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

    public function retriveFarmerBankDetails()
    {
        try {
            $farmer_details = Farming::where('created_by', Auth::user()->id)
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
