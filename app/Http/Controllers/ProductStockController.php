<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use App\Models\ProductStock;
use App\Models\StockReport;
use App\Models\Utility;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-product_stock')) {
            $productServices = ProductService::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'product')->get();

            return view('admin.productstock.index', compact('productServices'));
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}


    /**
     * Display the specified resource.
     *
     * @param \App\Models\ProductStock $productStock
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductStock $productStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ProductStock $productStock
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productService = ProductService::find($id);
        if (\Auth::user()->can('edit-product_stock')) {
            if ($productService->created_by == \Auth::user()->creatorId()) {
                $warehouse = Warehouse::get();

                return view('admin.productstock.edit', compact('productService','warehouse'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProductStock $productStock
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-product_stock')) {
            $productService = ProductService::find($id);
            $total          = $productService->quantity + $request->quantity;

            if ($productService->created_by == \Auth::user()->creatorId()) {
                $productService->quantity   = $total;
                $productService->created_by = \Auth::user()->creatorId();
                $productService->save();

                //Product Stock Report
                $type        = 'manually';
                $type_id     = 0;
                $description = $request->quantity . '  ' . __('quantity added by manually');

                $stocks             = new StockReport();
                $stocks->warehouse_id = $request->warehouse_id;
                $stocks->product_id = $productService->id;
                $stocks->quantity     = $request->quantity;
                $stocks->type = $type;
                $stocks->type_id = $type_id;
                $stocks->description = $description;
                $stocks->created_by = \Auth::user()->creatorId();
                $stocks->save();

                if (isset($productService->id)) {
                    Utility::addWarehouseStock($productService->id, $request->quantity, $request->warehouse_id);
                }

                return redirect()->route('admin.productstock.index')->with('success', __('Product quantity updated manually.'));
            } else {
                return redirect()->back()->with('danger', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('danger', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ProductStock $productStock
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductStock $productStock)
    {
        //
    }

    public function sample_download()
    {
        $path = storage_path('uploads/sample/sample-product.csv');
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}
