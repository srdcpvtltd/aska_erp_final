<?php

namespace App\Http\Controllers\Farming;

use App\Models\SeedCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class SeedCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-seed_category')) {
            $seed_categories = SeedCategory::all();
            return view('admin.farmer.seed_category.index', compact('seed_categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-seed_category')) {
            return view('admin.farmer.seed_category.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-seed_category')) {
            try {
                $this->validate($request, [
                    'name' => 'required',
                    'category' => 'required',
                    'type' => 'required',
                ]);
                SeedCategory::create($request->all());
                return redirect()->route('admin.farmer.seed_category.index')->with('success', 'Seed Category Added Successfully.');
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
    public function show(SeedCategory $seedCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeedCategory $seedCategory)
    {
        if (\Auth::user()->can('edit-seed_category')) {
            return view('admin.farmer.seed_category.edit', compact('seedCategory'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-seed_category')) {
            $seedCategory = SeedCategory::find($id);
            $seedCategory->update($request->all());
            return redirect()->route('admin.farmer.seed_category.index')->with('success', 'Seed Category Updated Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-seed_category')) {
            $seedCategory = SeedCategory::find($id);
            $seedCategory->delete();
            return redirect()->back()->with('success', 'Seed Category Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
