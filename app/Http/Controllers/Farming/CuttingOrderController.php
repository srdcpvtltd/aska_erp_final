<?php

namespace App\Http\Controllers\Farming;

use App\Models\CuttingOrder;
use App\Models\FarmingDetail;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class CuttingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-issue_cutting_order')) {
            $query = FarmingDetail::query()->select('farming_details.*')
                ->join('users', 'users.id', 'farming_details.created_by');
            if ($request->block_id) {
                $query->where('farming_details.block_id', $request->block_id);
            }
            if ($request->gram_panchyat_id) {
                $query->where('farming_details.gram_panchyat_id', $request->gram_panchyat_id);
            }
            if ($request->village_id) {
                $query->where('farming_details.village_id', $request->village_id);
            }
            if ($request->zone_id) {
                $query->where('farming_details.zone_id', $request->zone_id);
            }
            if ($request->center_id) {
                $query->where('farming_details.center_id', $request->center_id);
            }
            if ($request->seed_category_id) {
                $query->where('farming_details.seed_category_id', $request->seed_category_id);
            }
            if ($request->type) {
                $query->where('farming_details.type', $request->type);
            }
            if ($request->date_of_harvesting_from) {
                $from = Carbon::parse($request->date_of_harvesting_from)->format('Y-m-d');
                $to = $request->date_of_harvesting_to ?  Carbon::parse($request->date_of_harvesting_to)->format('Y-m-d') : Carbon::parse($request->date_of_harvesting_from)->addDays(30)->format('Y-m-d');
                $query->whereBetween('farming_details.date_of_harvesting', [$from, $to]);
            }
            $farming_details = $query->where('farming_details.is_cutting_order', 0)
                ->where('farming_details.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)->get();
            return view('admin.farmer.cutting_order.index', compact('farming_details'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CuttingOrder $cuttingOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuttingOrder $cuttingOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuttingOrder $cuttingOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuttingOrder $cuttingOrder)
    {
        //
    }
    public function updateCuttingOrder(Request $request)
    {
        if (\Auth::user()->can('edit-issue_cutting_order')) {
            try {
                if (!$request->farming_detail || count($request->farming_detail) == 0) {

                    $request->session()->flash('error', "Please Select Farming Detail!");
                    return false;
                }
                foreach ($request->farming_detail as $id => $farming_detail) {
                    $farmingDetail = FarmingDetail::find($id);
                    $farmingDetail->update([
                        'is_cutting_order' => 1
                    ]);
                    CuttingOrder::create([
                        'farming_detail_id' => $farmingDetail->id,
                        'created_by' => Auth::user()->id,
                    ]);
                }
                $request->session()->flash('success', 'Cutting order issued successfully!');
                return true;
            } catch (Exception $e) {
                $request->session()->flash('error', $e->getMessage());
                return false;
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
