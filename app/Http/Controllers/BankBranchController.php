<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Bank_branch;
use Illuminate\Http\Request;

class BankBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-bank_branch')) {
            $bank_branch = Bank_branch::all();

            return view('admin.bank_branch.index', compact('bank_branch'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-bank_branch')) {
            $banks = Bank::all();
            return view('admin.bank_branch.create', compact('banks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (\Auth::user()->can('create-bank_branch')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'bank_id' => 'required',
                    'name' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $bank = new Bank_branch();
            $bank->name = $request->name;
            $bank->bank_id = $request->bank_id;
            $bank->ifsc_code = $request->ifsc_code;
            $bank->save();

            return redirect()->route('admin.bank_branches.index')->with('success', __('Branch successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (\Auth::user()->can('edit-bank_branch')) {
            $bank_branches = Bank_branch::findorfail($id);
            $banks = Bank::all();
            return view('admin.bank_branch.edit', compact('banks','bank_branches'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (\Auth::user()->can('edit-bank_branch')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'bank_id' => 'required',
                    'name' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $bank_branch = Bank_branch::findorfail($id);
            $bank_branch->name = $request->name;
            $bank_branch->bank_id = $request->bank_id;
            $bank_branch->ifsc_code = $request->ifsc_code;
            $bank_branch->save();

            return redirect()->route('admin.bank_branches.index')->with('success', __('Branch successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (\Auth::user()->can('delete-bank_branch')) {
            $bank_branch = Bank_branch::findorfail($id);
            $bank_branch->delete();
            return redirect()->route('admin.bank_branches.index')->with('success', __('Branch successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
