<?php

namespace App\Http\Controllers\Location;

use App\Models\Center;
use App\Models\Zone;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-center')) {
            $centers = Center::all();
            return view('admin.location.center.index', compact('centers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-center')) {
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            return view('admin.location.center.create', compact('zones'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-center')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'zone_id' => 'required',
                ]);
                Center::create($request->all());
                return redirect()->route('admin.location.center.index')->with('success', 'Center Added Successfully.');
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
    public function show(Center $center)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Center $center)
    {
        if (\Auth::user()->can('edit-center')) {
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            return view('admin.location.center.edit', compact('center', 'zones'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-center')) {
            $center = Center::find($id);
            $center->update($request->all());
            return redirect()->route('admin.location.center.index')->with('success', 'Center Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-center')) {
            $center = Center::find($id);
            $center->delete();
            return redirect()->back()->with('success', 'Center Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
