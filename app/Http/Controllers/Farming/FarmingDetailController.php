<?php

namespace App\Http\Controllers\Farming;

use App\Models\FarmingDetail;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Farming;
use App\Models\GramPanchyat;
use App\Models\SeedCategory;
use App\Models\Village;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmingDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-plot')) {
            $farming_details = FarmingDetail::query()->select('farming_details.*')
                ->join('users', 'users.id', 'farming_details.created_by')
                ->where('farming_details.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)->get();
            return view('admin.farmer.farming_detail.index', compact('farming_details'));
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

            $blocks = Block::all();

            if (!empty($farming_details)) {
                $plot_number = "00" . $farming_details->plot_number + 1;
            } else {
                $plot_number = "001";
            }
            $seed_categories = SeedCategory::all();
            return view('admin.farmer.farming_detail.create', compact('farmings', 'seed_categories', 'plot_number', 'blocks'));
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
            // dd($request->all());
            try {
                $this->validate($request, [
                    'farming_id' => 'required',
                    'plot_number' => 'required',
                    // 'kata_number' => 'required',
                    'area_in_acar' => 'required',
                    'date_of_harvesting' => 'required',
                    // 'quantity' => 'required',
                    'seed_category_id' => 'required',
                    // 'tentative_harvest_quantity' => 'required',
                    'created_by' => 'required',
                ]);
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
            $blocks = Block::all();
            $gp = GramPanchyat::all();
            $village = Village::all();
            return view('admin.farmer.farming_detail.edit', compact('farming_detail', 'farmings', 'seed_categories', 'blocks', 'gp', 'village'));
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
                $this->validate($request, [
                    'farming_id' => 'required',
                    'plot_number' => 'required',
                    'area_in_acar' => 'required',
                    'date_of_harvesting' => 'required',
                    'seed_category_id' => 'required',
                ]);
                $farming_detail->update($request->all());
                return redirect()->back()->with('success', 'Plot Details Updated Successfully.');
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
                $this->validate($request, [
                    'croploss' => 'required',
                    'total_planting_area' => 'required',
                    'tentative_harvest_quantity' => 'required',
                ]);
                $farming_detail->update($request->all());
                return redirect()->back()->with('success', 'Plot Details Updated Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
