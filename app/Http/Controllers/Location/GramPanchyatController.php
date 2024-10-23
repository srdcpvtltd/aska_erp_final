<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\District;
use App\Models\GramPanchyat;
use Exception;
use Illuminate\Http\Request;

class GramPanchyatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-gram_panchyat')) {
            $gram_panchyats = GramPanchyat::all();
            $blocks = Block::all();
            return view('admin.location.gram_panchyat.index', compact('gram_panchyats','blocks'));
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
        if (\Auth::user()->can('create-gram_panchyat')) {
            $districts = District::all()->pluck('name', 'id');
            $districts->prepend('Select District', '');
            return view('admin.location.gram_panchyat.create', compact('districts'));
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
        if (\Auth::user()->can('create-gram_panchyat')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'block_id' => 'required',
                ]);
                // GramPanchyat::create($request->all());
                $grampanchyat = new GramPanchyat();
                $grampanchyat->name = $request->name;
                $grampanchyat->block_id = $request->block_id;
                $grampanchyat->save();

                return redirect()->route('admin.location.gram_panchyat.index')->with('success', 'Gram Panchyat Added Successfully.');
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
     * @param  \App\Models\GramPanchyat  $gramPanchyat
     * @return \Illuminate\Http\Response
     */
    public function show(GramPanchyat $gramPanchyat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GramPanchyat  $gramPanchyat
     * @return \Illuminate\Http\Response
     */
    public function edit(GramPanchyat $gramPanchyat)
    {
        if (\Auth::user()->can('edit-gram_panchyat')) {
            $districts = District::all()->pluck('name', 'id');
            $districts->prepend('Select District', '');
            $blocks = Block::all()->pluck('name', 'id');
            $blocks->prepend('Select Blocks', '');
            return view('admin.location.gram_panchyat.edit', compact('gramPanchyat', 'districts', 'blocks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GramPanchyat  $gramPanchyat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-gram_panchyat')) {
            $gramPanchyat = GramPanchyat::find($id);
            $gramPanchyat->name = $request->name;
            $gramPanchyat->block_id = $request->block_id;
            $gramPanchyat->update();
            return redirect()->route('admin.location.gram_panchyat.index')->with('success', 'Gram Panchyat Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GramPanchyat  $gramPanchyat
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-gram_panchyat')) {
            $gramPanchyat = GramPanchyat::find($id);
            $gramPanchyat->delete();
            return redirect()->back()->with('success', 'Gram Panchyat Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function search_filter(Request $request)
    {
        $gram_panchyats = GramPanchyat::where('block_id',$request->block_id)->get();
        $blocks = Block::all();

        return view('admin.location.gram_panchyat.index', compact('gram_panchyats','blocks'));
    }
}
