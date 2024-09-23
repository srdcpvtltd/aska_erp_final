<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country;
use Exception;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-state')) {
            $states = State::all();
            return view('admin.location.state.index', compact('states'));
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
        if (\Auth::user()->can('create-state')) {
            $countries = Country::all()->pluck('name', 'id');
            $countries->prepend('Select Country', '');
            return view('admin.location.state.create', compact('countries'));
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
        if (\Auth::user()->can('create-state')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'country_id' => 'required',
                ]);
                State::create($request->all());
                return redirect()->route('admin.location.state.index')->with('success', 'State Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {
        if (\Auth::user()->can('edit-state')) {
            $countries = Country::all()->pluck('name', 'id');
            $countries->prepend('Select Country', '');
            return view('admin.location.state.edit', compact('state', 'countries'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-state')) {
            $state = State::find($id);
            $state->update($request->all());
            return redirect()->route('admin.location.state.index')->with('success', 'State Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-state')) {
            $state = State::find($id);
            $state->delete();
            return redirect()->back()->with('success', 'State Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
