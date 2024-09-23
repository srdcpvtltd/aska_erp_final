<?php

namespace App\Http\Controllers\Farming;

use App\Models\Guarantor;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Country;
use App\Models\District;
use App\Models\Farming;
use App\Models\GramPanchyat;
use App\Models\State;
use App\Models\Village;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuarantorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-farmer_guarantor')) {
            $guarantors = Guarantor::where('created_by', Auth::user()->id)->get();

            return view('admin.farmer.guarantor.index', compact('guarantors'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-farmer_guarantor')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();

            $countries = Country::all();
            return view('admin.farmer.guarantor.create', compact('countries', 'farmings'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-farmer_guarantor')) {
            try {
                $this->validate($request, [
                    'farming_id' => 'required',
                    'name' => 'required',
                    'father_name' => 'required',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'district_id' => 'required',
                    'block_id' => 'required',
                    'gram_panchyat_id' => 'required',
                    'village_id' => 'required',
                    'post_office' => 'required',
                    'police_station' => 'required',
                    'age' => 'required',
                    'created_by' => 'required',
                ]);
                Guarantor::create($request->all());
                return redirect()->to(route('admin.farmer.guarantor.index'))->with('success', 'Guarantor Added Successfully.');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-farmer_guarantor')) {
            $guarantor = Guarantor::find($id);
            $countries = Country::all();
            $states = State::where('country_id', $guarantor->country_id)->get();
            $districts = District::where('state_id', $guarantor->state_id)->get();
            $blocks = Block::where('district_id', $guarantor->district_id)->get();
            $gram_panchyats = GramPanchyat::where('block_id', $guarantor->block_id)->get();
            $villages = Village::where('gram_panchyat_id', $guarantor->gram_panchyat_id)->get();
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            return view('admin.farmer.guarantor.edit', compact(
                'guarantor',
                'countries',
                'states',
                'districts',
                'blocks',
                'gram_panchyats',
                'villages',
                'farmings',
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
        if (\Auth::user()->can('edit-farmer_guarantor')) {
            $guarantor = Guarantor::find($id);
            $guarantor->update($request->all());
            return redirect()->back()->with('success', 'Guarantor Updated Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-farmer_guarantor')) {
            $guarantor = Guarantor::find($id);
            $guarantor->delete();
            return redirect()->back()->with('success', 'Guarantor Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
