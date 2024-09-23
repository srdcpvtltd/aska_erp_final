<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\GramPanchyat;
use App\Models\Village;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-village')) {
            $villages = Village::all();
            return view('admin.location.village.index', compact('villages'));
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
        if (\Auth::user()->can('create-village')) {
            $gram_panchyats = GramPanchyat::all()->pluck('name', 'id');
            $gram_panchyats->prepend('Select GP', '');
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            $centers = Center::all()->pluck('name', 'id');
            $centers->prepend('Select Center', '');

            return view('admin.location.village.create', compact('gram_panchyats', 'zones', 'centers'));
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
        if (\Auth::user()->can('create-village')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'gram_panchyat_id' => 'required',
                    'zone_id' => 'required',
                    'center_id' => 'required',
                ]);
                Village::create($request->all());
                return redirect()->route('admin.location.village.index')->with('success', 'Village Added Successfully.');
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
     * @param  \App\Models\Village  $village
     * @return \Illuminate\Http\Response
     */
    public function show(Village $village)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Village  $village
     * @return \Illuminate\Http\Response
     */
    public function edit(Village $village)
    {
        if (\Auth::user()->can('edit-village')) {
            $gram_panchyats = GramPanchyat::all()->pluck('name', 'id');
            $gram_panchyats->prepend('Select GP', '');
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            $centers = Center::all()->pluck('name', 'id');
            $centers->prepend('Select Center', '');

            return view('admin.location.village.edit', compact('village', 'gram_panchyats', 'zones', 'centers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Village  $village
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-village')) {
            $village = Village::find($id);
            $village->update($request->all());
            return redirect()->route('admin.location.village.index')->with('success', 'Village Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Village  $village
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-village')) {
            $village = Village::find($id);
            $village->delete();
            return redirect()->back()->with('success', 'Village Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
