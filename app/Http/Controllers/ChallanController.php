<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use App\Models\ProductService;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $rules = [
            'warehouse_id' => 'required',
            'challan_no' => 'required',
            'product_id' => 'required',
            'receive_date' => 'required',
            'vehicle_no' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('danger', $messages->first());
        }

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

        $product = new WarehouseProduct();
        $product->challan_no = $challan->id;
        $product->warehouse_id = $request->warehouse_id;
        $product->product_id = $request->product_id;
        $product->quantity = $request->quantity;
        $product->created_by = $request->created_by;
        $product->save();

        return redirect()->route('admin.challan.index')->with('success', __('Challan successfully created.'));
    }

    public function edit($id)
    {
        $challan = Challan::findorfail($id);
        $warehouses = warehouse::where('created_by', Auth::user()->id)->get();
        $products = ProductService::where('created_by', Auth::user()->id)->get();

        return view('admin.challan.edit', compact('challan', 'warehouses', 'products'));
    }

    public function update(Request $request, $id)
    {
        $challan = Challan::findorfail($id);
        $challan->challan_no = $request->challan_no;
        $challan->receive_date = $request->receive_date;
        $challan->vehicle_no = $request->vehicle_no;
        $challan->amount = $request->amount;
        $challan->created_by = $request->created_by;
        $challan->save();

        return redirect()->route('admin.challan.index')->with('success', __('Challan successfully updated.'));
    }

    public function destroy($id)
    {
        $challan = Challan::findorfail($id);
        $challan->delete();
        
        $product = WarehouseProduct::where('challan_no', $id);
        $product->delete();

        return redirect()->route('admin.challan.index')->with('success', __('Challan successfully deleted.'));
    }
}
