<?php

namespace App\Http\Controllers\Farming;

use App\Models\Farming;
use App\Models\FarmingPayment;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmingPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-security_deposite')) {
            $payments = FarmingPayment::where('type', FarmingPayment::SECURITY_DEPOSIT)->where('created_by', Auth::user()->id)->get();
            return view('admin.farmer.payment.index', compact('payments'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-security_deposite')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();

            return view('admin.farmer.payment.create', compact('farmings'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-security_deposite')) {
            $farming_payment = FarmingPayment::where('farming_id', $request->farming_id)->where('type',"Security Deposit")->get();
            if(count($farming_payment) > 0){
                return redirect()->route('admin.farmer.payment.index')->with('danger', 'Payment Already Added.');
            }
            try {
                if ($request->amount != null) {
                    $this->validate($request, [
                        'farming_id' => 'required',
                        'created_by' => 'required',
                        'amount' => 'required|integer',
                    ]);
                } else {
                    $this->validate($request, [
                        'farming_id' => 'required',
                        'created_by' => 'required',
                    ]);
                }

                if($request->type != null){
                    $type = $request->type;
                } else {
                    $type = "Security Deposit";
                }

                $client = new FarmingPayment;
                $client->farming_id = $request->farming_id;
                $client->receipt_no = $request->receipt_no;
                $client->receipt_type = $request->receipt_type;
                $client->agreement_number = $request->agreement_number;
                $client->date = $request->date;
                $client->amount = $request->amount;
                $client->type = $type;
                $client->bank = $request->bank;
                $client->loan_account_number = $request->loan_account_number;
                $client->ifsc = $request->ifsc;
                $client->branch = $request->branch;
                $client->created_by = Auth::user()->id;
                $client->save();

                return redirect()->to(route('admin.farmer.farming_registration.show', $request->farming_id))->with('success', 'Payment Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('danger', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FarmingPayment $farmingPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-security_deposite')) {
            $payment = FarmingPayment::find($id);
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            return view('admin.farmer.payment.edit', compact(
                'payment',
                'farmings',
            ));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-security_deposite')) {
            $farmingPayment = FarmingPayment::find($id);
            $farmingPayment->farming_id = $request->farming_id;
            $farmingPayment->receipt_no = $request->receipt_no;
            $farmingPayment->agreement_number = $request->agreement_number;
            $farmingPayment->date = $request->date;
            $farmingPayment->amount = $request->amount;
            $farmingPayment->bank = $request->bank;
            $farmingPayment->loan_account_number = $request->loan_account_number;
            $farmingPayment->ifsc = $request->ifsc;
            $farmingPayment->branch = $request->branch;
            $farmingPayment->created_by = Auth::user()->id;
            $farmingPayment->save();

            return redirect()->back()->with('success', 'Farming Payment Updated Successfully.');
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-security_deposite')) {
            $farmingPayment = FarmingPayment::find($id);
            $farmingPayment->delete();
            return redirect()->back()->with('success', 'Farming Payment Deleted Successfully.');
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function bankGuarantee()
    {
        if (\Auth::user()->can('show-bank_guarantee')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            return view('admin.farmer.bank_guarantee.create', compact('farmings'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function reimbursement()
    {
        if (\Auth::user()->can('show-reimbursement')) {
            $payments = FarmingPayment::where('type', FarmingPayment::REIMBURSEMENT)
                ->where('created_by', Auth::user()->id)->get();

            return view('admin.farmer.reimbursement.index', compact('payments'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function reimbursementCreate()
    {
        if (\Auth::user()->can('create-reimbursement')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            return view('admin.farmer.reimbursement.create', compact('farmings'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function reimbursement_delete($id)
    {
        if (\Auth::user()->can('delete-reimbursement')) {
            $farmingPayment = FarmingPayment::find($id);
            $farmingPayment->delete();
            return redirect()->back()->with('success', 'Farming Payment Deleted Successfully.');
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function editBankGuarantee($id)
    {
        if (\Auth::user()->can('edit-bank_guarantee')) {
            $payment = FarmingPayment::find($id);
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            return view('admin.farmer.bank_guarantee.edit', compact(
                'payment',
                'farmings',
            ));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
    public function pdfBankGuarantee($id)
    {
        $payment = FarmingPayment::find($id);
        return view('admin.farmer.bank_guarantee.show', compact(
            'payment'
        ));
    }

    public function g_code(Request $request)
    {
        $farmer = Farming::find($request->farmer_id);

        return response()->json([
            'registration_id' => $farmer->registration_no,
            'g_code' => $farmer->g_code
        ]);
    }
}
