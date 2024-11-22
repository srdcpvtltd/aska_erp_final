<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use App\Models\ProductService;
use App\Models\warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallanController extends Controller
{
    public function index()
    {
        $challans = Challan::all();
        return view('admin.challan.index', compact('challans'));
    }

    public function create()
    {
        $warehouses = warehouse::where('created_by', Auth::user()->id)->get();
        $products = ProductService::where('created_by', Auth::user()->id)->get();
        return view('admin.challan.create', compact('warehouses', 'products'));
    }

    public function store(Request $request) {
        // dd($request->all());
        $challan = new Challan();
        $challan->warehouse_id = $request->warehouse_id;
        $challan->challan_no = $request->challan_no;
        $challan->product_id = $request->product_id;
        $challan->receive_date = $request->receive_date;
        $challan->vehicle_no = $request->vehicle_no;
        $challan->quantity = $request->quantity;
        $challan->amount = $request->amount;
        $challan->created_by = $request->created_by;
        $challan->save();

        $product = ProductService::find($request->product_id);
        $product->quantity = $request->quantity;
        $product->save();

        return redirect()->route('admin.challan.index');
    }
}
