<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-district')) {
            $districts = District::all();
            return view('admin.location.district.index', compact('districts'));
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
        if (\Auth::user()->can('create-district')) {
            $states = State::all()->pluck('name', 'id');
            $states->prepend('Select State', '');
            return view('admin.location.district.create', compact('states'));
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
        if (\Auth::user()->can('create-district')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'state_id' => 'required',
                ]);
                District::create($request->all());
                return redirect()->route('admin.location.district.index')->with('success', 'District Added Successfully.');
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
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\Response
     */
    public function show(District $district)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\Response
     */
    public function edit(District $district)
    {
        if (\Auth::user()->can('edit-district')) {
            $states = State::all()->pluck('name', 'id');
            $states->prepend('Select State', '');
            return view('admin.location.district.edit', compact('district', 'states'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-district')) {
            $district = District::find($id);
            $district->update($request->all());
            return redirect()->route('admin.location.district.index')->with('success', 'District Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-district')) {
            $district = District::find($id);
            $district->delete();
            return redirect()->back()->with('success', 'District Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
