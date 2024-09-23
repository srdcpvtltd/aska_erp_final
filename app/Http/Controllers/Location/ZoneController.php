<?php

namespace App\Http\Controllers\Location;

use App\Models\Zone;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-zone')) {
            $zones = Zone::all();
            return view('admin.location.zone.index', compact('zones'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-zone')) {
            return view('admin.location.zone.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-zone')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                ]);
                Zone::create($request->all());
                return redirect()->route('admin.location.zone.index')->with('success', 'Zone Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Zone $zone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        if (\Auth::user()->can('edit-zone')) {
            return view('admin.location.zone.edit', compact('zone'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-zone')) {
            $zone = Zone::find($id);
            $zone->update($request->all());
            return redirect()->route('admin.location.zone.index')->with('success', 'Zone Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-zone')) {
            $zone = Zone::find($id);
            $zone->delete();
            return redirect()->back()->with('success', 'Zone Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
