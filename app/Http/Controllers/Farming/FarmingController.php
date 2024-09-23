<?php

namespace App\Http\Controllers\Farming;

use App\Models\Farming;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Center;
use App\Models\Country;
use App\Models\District;
use App\Models\FarmerLoan;
use App\Models\FarmingDetail;
use App\Models\FarmingPayment;
use App\Models\GramPanchyat;
use App\Models\Guarantor;
use App\Models\Irrigation;
use App\Models\SeedCategory;
use App\Models\State;
use App\Models\Village;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FarmingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-farmer_registration')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)->get();
            return view('admin.farmer.registration.index', compact('farmings'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-farmer_registration')) {
            $countries = Country::all();
            $zones = Zone::all();
            $irrigations = Irrigation::all();

            return view('admin.farmer.registration.create', compact('countries', 'zones', 'irrigations'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-farmer_registration')) {
            try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'father_name' => 'required',
                    'mobile' => 'digits:10|required|numeric',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'district_id' => 'required',
                    'block_id' => 'required',
                    'gram_panchyat_id' => 'required',
                    'village_id' => 'required',
                    'age' => 'required',
                    'gender' => 'required',
                    'qualification' => 'required',
                    'offered_area' => 'required|numeric',
                    'adhaarno' => 'digits:12|required|numeric',
                    'language' => 'required',
                    'sms_mode' => 'required',
                    'created_by' => 'required',
                    'zone_id' => 'required',
                    'center_id' => 'required',
                    // 'farmer_id_2' => 'required',
                    'farmer_category' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                $request->merge([
                    'registration_no' => "ACSI" . '-' . rand(0, 9999)
                ]);

                Farming::create($request->all());
                return redirect()->to(route('admin.farmer.farming_registration.index'))->with('success', 'Farming Added Successfully.');
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
    public function show($id)
    {
        if (\Auth::user()->can('show-farmer_registration')) {
            $farming = Farming::find($id);
            $plot = FarmingDetail::where('farming_id', $farming->id)->get();
            $guarantors = Guarantor::where('farming_id', $farming->id)->get();
            $security_deposits = FarmingPayment::where('farming_id', $farming->id)
                ->whereIn('type', [FarmingPayment::SECURITY_DEPOSIT, FarmingPayment::REIMBURSEMENT])->get();
            $bank_guarantees = FarmingPayment::where('farming_id', $farming->id)
                ->where('type', FarmingPayment::BANK_GUARANTEE)->get();
            $loans = FarmerLoan::where('farming_id', $farming->id)->get();
            return view('admin.farmer.registration.show', compact('farming', 'guarantors', 'security_deposits', 'bank_guarantees', 'loans', 'plot'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-farmer_registration')) {
            $farming = Farming::find($id);
            $countries = Country::all();
            $states = State::where('country_id', $farming->country_id)->get();
            $districts = District::where('state_id', $farming->state_id)->get();
            $blocks = Block::where('district_id', $farming->district_id)->get();
            $gram_panchyats = GramPanchyat::where('block_id', $farming->block_id)->get();
            $villages = Village::where('gram_panchyat_id', $farming->gram_panchyat_id)->get();
            $zones = Zone::all();
            $centers = Center::where('zone_id', $farming->zone_id)->get();
            return view('admin.farmer.registration.edit', compact(
                'farming',
                'countries',
                'states',
                'districts',
                'blocks',
                'gram_panchyats',
                'villages',
                'zones',
                'centers',
            ));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-farmer_registration')) {
            $farming = Farming::find($id);

            $farming->update($request->all());
            return redirect()->back()->with('success', 'Farming Updated Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-farmer_registration')) {
            $farming = Farming::find($id);
            $farming->delete();
            return redirect()->back()->with('success', 'Farming Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->get();
        return response()->json([
            'states' => $states,
        ]);
    }
    public function getDistricts(Request $request)
    {
        $districts = District::where('state_id', $request->state_id)->get();
        return response()->json([
            'districts' => $districts,
        ]);
    }
    public function getBlocks(Request $request)
    {
        $blocks = Block::where('district_id', $request->district_id)->get();
        return response()->json([
            'blocks' => $blocks,
        ]);
    }
    public function getGramPanchyats(Request $request)
    {
        $gram_panchyats = GramPanchyat::where('block_id', $request->block_id)->get();
        return response()->json([
            'gram_panchyats' => $gram_panchyats,
        ]);
    }
    public function getVillages(Request $request)
    {
        $villages = Village::where('gram_panchyat_id', $request->gram_panchyat_id)->get();
        return response()->json([
            'villages' => $villages,
        ]);
    }
    public function getCenters(Request $request)
    {
        $centers = Center::where('zone_id', $request->zone_id)->get();
        return response()->json([
            'centers' => $centers,
        ]);
    }

    public function validateProfile($id)
    {
        $farming = Farming::find($id);
        $zone = Zone::find($farming->zone_id);
        $center = Center::find($farming->center_id);

        $existingFarmingProfiles = str_pad($id, 5, '0', STR_PAD_LEFT);
        $g_code = $zone->zone_number . $center->center_number . '/' . $existingFarmingProfiles;

        $farming->update(['is_validate' => 1, 'farmer_id' => 'ERP-' . random_int(100000, 999999), 'g_code' => $g_code]);
        return redirect()->to(route('admin.farmer.farming_registration.index'))->with('success', 'Farming Registration Validated Successfully.');
    }

    public function registration_id(Request $request)
    {
        $farmer = Farming::find($request->farmer_id);
        $guarentor = Farming::where('id', '!=', $request->farmer_id)->get();

        return response()->json([
            'registration_id' => $farmer->registration_no,
            'guarentor' => $guarentor
        ]);
    }

    public function get_country_state(Request $request)
    {
        $country = Country::find($request->country_id);
        $state = State::find($request->state_id);
        $district = District::find($request->district_id);
        $block = Block::find($request->block_id);
        $gram_panchyat = GramPanchyat::find($request->gram_panchyat_id);
        $village = Village::find($request->village_id);

        return response()->json([
            'country' => $country->name,
            'state' => $state->name,
            'district' => $district->name,
            'block' => $block->name,
            'gram_panchyat' => $gram_panchyat->name,
            'village' => $village->name,
        ]);
    }

    public function get_zone_center(Request $request)
    {
        $village = Village::findorfail($request->village_id);
        $zone = Zone::findorfail($village->zone_id);
        $center = Center::findorfail($village->center_id);

        return response()->json([
            'zone' => $zone,
            'center' => $center,
        ]);
    }

    public function get_bank_branches(Request $request)
    {
        dd($request->all());
        $village = Village::findorfail($request->village_id);
        $zone = Zone::findorfail($village->zone_id);
        $center = Center::findorfail($village->center_id);

        return response()->json([
            'zone' => $zone,
            'center' => $center,
        ]);
    }

    public function search(Request $request)
    {
        $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
            ->where('farmings.created_by', Auth::user()->id)
            ->where('farmings.is_validate', $request->filter)
            ->orWhere('users.supervisor_id', Auth::user()->id)->get();

        return view('admin.farmer.registration.index', compact('farmings'));
    }
}
