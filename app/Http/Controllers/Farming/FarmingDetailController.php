<?php

namespace App\Http\Controllers\Farming;

use App\DataTables\PlotdetailsDataTable;
use App\Models\FarmingDetail;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Center;
use App\Models\Farming;
use App\Models\GramPanchyat;
use App\Models\Irrigation;
use App\Models\SeedCategory;
use App\Models\Village;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FarmingDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PlotdetailsDataTable $table)
    {
        if (\Auth::user()->can('manage-plot')) {
            // $farming_details = FarmingDetail::query()->select('farming_details.*')
            //     ->join('users', 'users.id', 'farming_details.created_by')
            //     ->where('farming_details.created_by', Auth::user()->id)
            //     ->orWhere('users.supervisor_id', Auth::user()->id)
            //     ->orderBy('farming_details.id', 'ASC')
            //     ->get();
            $zones = Zone::all();
            // return view('admin.farmer.farming_detail.index', compact('farming_details', 'zones'));
            return $table->render('admin.farmer.farming_detail.index', compact('zones'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-plot')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();

            $farming_details = FarmingDetail::select('plot_number')
                ->where('created_by', Auth::user()->id)
                ->OrderBy('id', 'DESC')
                ->first();

            $zones = Zone::all();

            if (!empty($farming_details)) {
                $plot_number = "00" . $farming_details->plot_number + 1;
            } else {
                $plot_number = "001";
            }
            $seed_categories = SeedCategory::all();
            return view('admin.farmer.farming_detail.create', compact('farmings', 'seed_categories', 'plot_number', 'zones'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-plot')) {
            try {
                $validator = Validator::make($request->all(), [
                    'farming_id' => 'required',
                    'plot_number' => 'unique:farming_details,plot_number',
                    'area_in_acar' => 'required',
                    'date_of_harvesting' => 'required',
                    'seed_category_id' => 'required',
                    'created_by' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }

                FarmingDetail::create($request->all());
                return redirect()->to(route('admin.farmer.farming_detail.index'))->with('success', 'Plot Details Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FarmingDetail $farmingDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-plot')) {
            $farming_detail = FarmingDetail::find($id);
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)->get();
            $seed_categories = SeedCategory::all();
            $zones = Zone::all();
            $centers = Center::where('zone_id', $farming_detail->can_field_zone_id)->get();
            $village = Village::where('center_id', $farming_detail->can_field_center_id)->where('zone_id', $farming_detail->can_field_zone_id)->orderBy('name', 'asc')->get();
            if ($farming_detail->irregation_mode != null) {
                $irrigations = Irrigation::where('category', $farming_detail->irregation_mode)->get();
            } else {
                $irrigations = Irrigation::all();
            }
            return view('admin.farmer.farming_detail.edit', compact('farming_detail', 'farmings', 'seed_categories', 'zones', 'centers', 'village', 'irrigations'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-plot')) {
            $farming_detail = FarmingDetail::find($id);
            try {
                $validator = Validator::make($request->all(), [
                    'farming_id' => 'required',
                    'plot_number' => 'required',
                    'area_in_acar' => 'required',
                    'date_of_harvesting' => 'required',
                    'seed_category_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }

                $farming_detail->update($request->all());
                return redirect()->to(route('admin.farmer.farming_detail.index'))->with('success', 'Plot Details Updated Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-plot')) {
            $farmingDetail = FarmingDetail::find($id);
            $farmingDetail->delete();
            return redirect()->back()->with('success', 'Farming Detail Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getFarmingDetail(Request $request)
    {
        $farming = Farming::find($request->farming_id);
        $blockHtml = $gpHtml = $villageHtml = $zoneHtml = $centerHtml = '';
        if ($farming->block) {
            $blockHtml = '<option value="' . $farming->block->id . '"selected>' . $farming->block->name . '</option>';
        }
        if ($farming->gram_panchyat) {
            $gpHtml = '<option value="' . $farming->gram_panchyat->id . '"selected>' . $farming->gram_panchyat->name . '</option>';
        }
        if ($farming->village) {
            $villageHtml = '<option value="' . $farming->village->id . '"selected>' . $farming->village->name . '</option>';
        }
        if ($farming->zone) {
            $zoneHtml = '<option value="' . $farming->zone->id . '"selected>' . $farming->zone->name . '</option>';
        }
        if ($farming->center) {
            $centerHtml = '<option value="' . $farming->center->id . '" selected>' . $farming->center->name . '</option>';
        }
        return response()->json([
            'blockHtml' => $blockHtml,
            'gpHtml' => $gpHtml,
            'villageHtml' => $villageHtml,
            'zoneHtml' => $zoneHtml,
            'centerHtml' => $centerHtml,
        ]);
    }

    public function get_FarmingDetail(Request $request)
    {
        $farming = Farming::where('old_g_code', $request->g_code)
            // ->where('is_validate', 1)
            ->first();

        if ($farming != null) {
            $farmerHtml = $blockHtml = $gpHtml = $villageHtml = $zoneHtml = $centerHtml = '';
            if ($farming->id) {
                $farmerHtml = '<option value="' . $farming->id . '"selected>' . $farming->name . '</option>';
            }
            if ($farming->block) {
                $blockHtml = '<option value="' . $farming->block->id . '"selected>' . $farming->block->name . '</option>';
            }
            if ($farming->gram_panchyat) {
                $gpHtml = '<option value="' . $farming->gram_panchyat->id . '"selected>' . $farming->gram_panchyat->name . '</option>';
            }
            if ($farming->village) {
                $villageHtml = '<option value="' . $farming->village->id . '"selected>' . $farming->village->name . '</option>';
            }
            if ($farming->zone) {
                $zoneHtml = '<option value="' . $farming->zone->id . '"selected>' . $farming->zone->name . '</option>';
            }
            if ($farming->center) {
                $centerHtml = '<option value="' . $farming->center->id . '" selected>' . $farming->center->name . '</option>';
            }

            //can field
            $farming = $farming;
            $zone = Zone::all();
            $zone_id = $farming->zone_id;
            $center = Center::where('zone_id', $farming->zone_id)->get();
            $center_id = $farming->center_id;
            $village = Village::where('center_id', $farming->center_id)->where('zone_id', $farming->zone_id)->orderBy('name', 'asc')->get();
            $village_id = $farming->village_id;
        } else {
            $farmerHtml = '<option  value="">Select Farmer</option>';

            $blockHtml = '<option  value="">Select Block</option>';

            $gpHtml = '<option  value="">Select Farmer</option>';

            $villageHtml = '<option value="">Select Gram Panchyat</option>';

            $zoneHtml = '<option value="">Select Zone</option>';

            $centerHtml = '<option value="">Select Center</option>';

            $farming = null;
            $zone = Zone::all();
            $zone_id = null;
            $center = null;
            $center_id = null;
            $village = null;
            $village_id = null;
        }

        return response()->json([
            'farming' => $farming,
            'farmerHtml' => $farmerHtml,
            'blockHtml' => $blockHtml,
            'gpHtml' => $gpHtml,
            'villageHtml' => $villageHtml,
            'zoneHtml' => $zoneHtml,
            'centerHtml' => $centerHtml,
            'zone' => $zone,
            'zone_id' => $zone_id,
            'center' => $center,
            'center_id' => $center_id,
            'village' => $village,
            'village_id' => $village_id,
        ]);
    }

    public function getFarmingDetailData(Request $request)
    {
        $plot_details = FarmingDetail::findorfail($request->id);
        $farmer = Farming::findorfail($plot_details->farming_id);
        // dd($farmer->name);
        return response()->json([
            'plot_details' => $plot_details,
            'farmer_name' => $farmer->name
        ]);
    }

    public function servey_data(Request $request)
    {
        if (\Auth::user()->can('edit-plot')) {
            $farming_detail = FarmingDetail::find($request->id);
            try {
                $validator = Validator::make($request->all(), [
                    'croploss' => 'required',
                    'total_planting_area' => 'required',
                    'tentative_harvest_quantity' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                
                $farming_detail->update($request->all());
                return redirect()->back()->with('success', 'Plot Details Updated Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function search_filter(Request $request)
    {
        $farming_details = FarmingDetail::where('created_by', Auth::user()->id)
        ->where('zone_id', $request->zone_id)
        ->when($request->center_id !== null, function ($query) use ($request) {
            $query->where('center_id', $request->center_id);
        })
        ->orderBy('id', 'DESC')
        ->get();

        $zones = Zone::all();
        return view('admin.farmer.farming_detail.index', compact('farming_details', 'zones'));
    }
}
