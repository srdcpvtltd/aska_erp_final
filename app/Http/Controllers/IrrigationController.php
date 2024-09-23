<?php

namespace App\Http\Controllers;

use App\Models\Irrigation;
use Illuminate\Http\Request;

class IrrigationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-irrigation')) {
            $irrigations = Irrigation::all();
            return view('admin.irrigation.index', compact('irrigations'));
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
        if (\Auth::user()->can('create-irrigation')) {
            return view('admin.irrigation.create');
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
        if (\Auth::user()->can('create-irrigation')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'category' => 'required',
                    'code' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $irr = new Irrigation;
            $irr->name = $request->name;
            $irr->category = $request->category;
            $irr->code = $request->code;
            $irr->save();

            return redirect()->route('admin.irrigation.index')->with('success', 'Irrigation Added Successfully.');
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
        if (\Auth::user()->can('edit-irrigation')) {

            $irri = Irrigation::findorfail($id);

            return view('admin.irrigation.edit', compact('irri'));
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
        if (\Auth::user()->can('edit-irrigation')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'category' => 'required',
                    'code' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $irri = Irrigation::findorfail($id);
            $irri->name = $request->name;
            $irri->category = $request->category;
            $irri->code = $request->code;
            $irri->save();

            return redirect()->route('admin.irrigation.index')->with('success', 'Irrigation Updated Successfully.');
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
        if (\Auth::user()->can('delete-irrigation')) {
            
            $irri = Irrigation::findorfail($id);
            $irri->delete();

            return redirect()->route('admin.irrigation.index')->with('success', 'Irrigation Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
