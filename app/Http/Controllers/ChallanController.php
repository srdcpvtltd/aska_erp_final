<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use App\Models\ProductService;
use App\Models\StockReport;
use App\Models\Utility;
use App\Models\Vender;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ChallanController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage-challan')) {
            $challans = Challan::get();
            return view('admin.challan.index', compact('challans'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-challan')) {
            $warehouses = Warehouse::get();
            $vendors = Vender::get();
            $products = ProductService::get();
            return view('admin.challan.create', compact('warehouses', 'products', 'vendors'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-challan')) {
            $rules = [
                'warehouse_id' => 'required',
                'challan_no' => 'required',
                'product_id' => 'required',
                'receive_date' => 'required',
                'vehicle_no' => 'required',
                'quantity' => 'required',
                // 'amount' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('danger', $messages->first());
            }

            $challan = new Challan();
            $challan->warehouse_id = $request->warehouse_id;
            $challan->vendor_id = $request->vendor_id;
            $challan->challan_no = $request->challan_no;
            $challan->product_id = $request->product_id;
            $challan->receive_date = $request->receive_date;
            $challan->vehicle_no = $request->vehicle_no;
            $challan->quantity = $request->quantity;
            // $challan->amount = $request->amount;
            $challan->created_by = $request->created_by;
            $challan->save();

            //inventory management (Quantity)
            Utility::total_quantity('plus', $challan->quantity, $challan->product_id);

            //Product Stock Report
            $type = 'challan';
            $type_id = 0;
            $description = $request->quantity . ' ' . __('quantity added by challan') . ' ' . $challan->challan_no;

            $stocks             = new StockReport();
            $stocks->warehouse_id = $request->warehouse_id;
            $stocks->product_id = $request->product_id;
            $stocks->quantity     = $request->quantity;
            $stocks->type = $type;
            $stocks->type_id = $type_id;
            $stocks->description = $description;
            $stocks->created_by = \Auth::user()->creatorId();
            $stocks->save();

            //Warehouse Stock Report
            if (isset($request->product_id)) {
                Utility::addWarehouseStock($request->product_id, $request->quantity, $request->warehouse_id);
            }

            return redirect()->route('admin.challan.index')->with('success', __('Challan successfully created.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-challan')) {
            $challan = Challan::findorfail($id);
            $warehouses = Warehouse::get();
            $products = ProductService::get();
            $vendors = Vender::get();

            return view('admin.challan.edit', compact('challan', 'warehouses', 'products', 'vendors'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-challan')) {
            $challan = Challan::findorfail($id);
            $challan->challan_no = $request->challan_no;
            $challan->vendor_id = $request->vendor_id;
            $challan->receive_date = $request->receive_date;
            $challan->vehicle_no = $request->vehicle_no;
            // $challan->amount = $request->amount;
            $challan->created_by = $request->created_by;


            //inventory management (Quantity)
            $product_id = $request->product_id;
            $quantity = $request->quantity;

            $product      = ProductService::find($product_id);
            if (($product->type == 'product')) {
                $pro_quantity = $product->quantity;
                $product->quantity = ($pro_quantity - $challan->quantity) + $quantity;
                $product->save();
            }

            //Product Stock Report
            $type = 'challan';
            $type_id = 0;
            $descriptions = $challan->quantity . ' ' . __('quantity added by challan') . ' ' . $challan->challan_no;

            $stocks = StockReport::where('description', $descriptions)->first();

            $description = $request->quantity . ' ' . __('quantity added by challan') . ' ' . $challan->challan_no;

            $stocks->warehouse_id = $request->warehouse_id;
            $stocks->product_id = $request->product_id;
            $stocks->quantity   = $request->quantity;
            $stocks->type = $type;
            $stocks->type_id = $type_id;
            $stocks->description = $description;
            $stocks->created_by = \Auth::user()->creatorId();
            $stocks->save();

            //Warehouse Stock Report
            if (isset($request->product_id)) {
                $product_id = $request->product_id;
                $warehouse_id = $request->warehouse_id;
                $quantity = $request->quantity;

                $product     = WarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();
                if ($product) {
                    $pro_quantity = $product->quantity;
                    $product_quantity = ($pro_quantity - $challan->quantity) + $quantity;
                } else {
                    $product_quantity = $quantity;
                }

                $data = WarehouseProduct::updateOrCreate(
                    ['warehouse_id' => $warehouse_id, 'product_id' => $product_id],
                    ['warehouse_id' => $warehouse_id, 'product_id' => $product_id, 'quantity' => $product_quantity, 'created_by' => \Auth::user()->id]
                );
            }
            $challan->quantity = $request->quantity;
            $challan->save();

            return redirect()->route('admin.challan.index')->with('success', __('Challan successfully updated.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-challan')) {
            $challan = Challan::findorfail($id);
            $challan->delete();

            $product = WarehouseProduct::where('challan_no', $id);
            $product->delete();

            return redirect()->route('admin.challan.index')->with('success', __('Challan successfully deleted.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getChallanAmount(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        $total_price = $product_service->purchase_price * $request->quantity;

        return response()->json([
            'total_price' => $total_price,
        ]);
    }
}
