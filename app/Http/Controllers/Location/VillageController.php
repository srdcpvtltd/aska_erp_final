<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\District;
use App\Models\GramPanchyat;
use App\Models\Village;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use App\Exports\VillagesExport;
use App\Models\Block;
use Maatwebsite\Excel\Facades\Excel;

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
            $blocks = Block::all()->pluck('name', 'id');
            $blocks->prepend('Select Blocks', '');
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zones', '');
            return view('admin.location.village.index', compact('villages','blocks','zones'));
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
            $districts = District::all()->pluck('name', 'id');
            $districts->prepend('Select District', '');
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            $centers = Center::all()->pluck('name', 'id');
            $centers->prepend('Select Center', '');

            return view('admin.location.village.create', compact('districts', 'zones', 'centers'));
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
                    'block_id' => 'required',
                ]);

                $village = new Village;
                $village->name = $request->name;
                $village->district_id = $request->district_id;
                $village->block_id = $request->block_id;
                $village->gram_panchyat_id = $request->gram_panchyat_id;
                $village->zone_id = $request->zone_id;
                $village->center_id = $request->center_id;
                $village->km = $request->km;
                $village->save();

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
            $districts = District::all()->pluck('name', 'id');
            $districts->prepend('Select District', '');
            $blocks = Block::all()->pluck('name', 'id');
            $blocks->prepend('Select Block', '');
            $grampanchyats = GramPanchyat::all()->pluck('name', 'id');
            $grampanchyats->prepend('Select GramPanchyat', '');
            $zones = Zone::all()->pluck('name', 'id');
            $zones->prepend('Select Zone', '');
            $centers = Center::all()->pluck('name', 'id');
            $centers->prepend('Select Center', '');
            $blk = GramPanchyat::find($village->gram_panchyat_id);
            $dstct = Block::find($blk->block_id);

            return view('admin.location.village.edit', compact('village', 'districts', 'blocks', 'grampanchyats', 'zones', 'centers','blk', 'dstct'));
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
            $village->name = $request->name;
            $village->district_id = $request->district_id;
            $village->block_id = $request->block_id;
            $village->gram_panchyat_id = $request->gram_panchyat_id;
            $village->zone_id = $request->zone_id;
            $village->center_id = $request->center_id;
            $village->km = $request->km;
            $village->update();
            
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

    public function search_filter(Request $request)
    {
        $villages = Village::where(function($query) use ($request) {
            if ($request->block_id != '') {
                $query->where('block_id', $request->block_id);
            }
            if ($request->grampanchyat_id != '') {
                $query->Where('gram_panchyat_id', $request->grampanchyat_id);
            }
            if ($request->zone_id != '') {
                $query->Where('zone_id', $request->zone_id);
            }
            if ($request->center_id != '') {
                $query->Where('center_id', $request->center_id);
            }
        })->get();

        $blocks = Block::all()->pluck('name', 'id');
        $blocks->prepend('Select Blocks', '');
        $zones = Zone::all()->pluck('name', 'id');
        $zones->prepend('Select Zones', '');

        return view('admin.location.village.index', compact('villages','blocks','zones'));
    }
}
