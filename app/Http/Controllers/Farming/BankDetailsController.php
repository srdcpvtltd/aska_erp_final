<?php

namespace App\Http\Controllers\Farming;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Block;
use App\Models\Farming;
use App\Models\FarmingDetail;
use App\Models\SeedCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-bank_detail')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();

            return view('admin.farmer.bank_details.index', compact('farmings'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-bank_detail')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            $banks = Bank::all();

            return view('admin.farmer.bank_details.create', compact('farmings', 'banks'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-bank_detail')) {
            try {
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
                    $farming->non_loan_type = $request->loan_type;
                    $farming->bank = $request->non_loan_bank;
                    $farming->account_number = $request->non_loan_account_number;
                    $farming->ifsc_code = $request->non_loan_ifsc_code;
                    $farming->branch = $request->non_loan_branch;
                }
                $farming->save();

                return redirect()->to(route('admin.farmer.bank_details.index'))->with('success', 'Bank Details Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-bank_detail')) {
            $farmings = Farming::findorfail($id);
            $farming = Farming::all();

            return view('admin.farmer.bank_details.edit', compact('farmings', 'farming'));
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-bank_detail')) {
            try {
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
                    $farming->non_loan_type = $request->loan_type;
                    $farming->bank = $request->non_loan_bank;
                    $farming->account_number = $request->non_loan_account_number;
                    $farming->ifsc_code = $request->non_loan_ifsc_code;
                    $farming->branch = $request->non_loan_branch;
                }
                $farming->save();

                return redirect()->to(route('admin.farmer.bank_details.index'))->with('success', 'Bank Details updated Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('edit-bank_detail')) {
            $farmings = Farming::findorfail($id);
            $farmings->delete();

            return redirect()->back()->with('success', 'Bank Details deleted Successfully.');
        } else {
            return redirect()->back()->with('danger', 'Permission denied.');
        }
    }
}
