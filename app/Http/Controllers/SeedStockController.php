<?php

namespace App\Http\Controllers;

use App\Models\FarmerLoan;
use App\Models\Farming;
use App\Models\FarmingPayment;
use App\Models\ProductService;
use App\Models\SeedStock;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeedStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('manage-seedstock')) {
            $loans = FarmerLoan::where('loan_category_id','==',"11")->where('created_by', Auth::user()->id)->get();
            return view('admin.seedstock.index', compact('loans'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-seedstock')) {
            $farmers = Farming::get();
            $products = ProductService::where('category_id', "11")->get();
            return view('admin.seedstock.create', compact('products', 'farmers'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
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
        if (\Auth::user()->can('create-seedstock')) {
            $rules = [
                'farmer_id' => 'required',
                'product_id' => 'required',
                'receive_date' => 'required',
                'quantity' => 'required',
                'amount' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('danger', $messages->first());
            }

            $seedstock = new SeedStock();
            $seedstock->farmer_id = $request->farmer_id;
            $seedstock->product_id = $request->product_id;
            $seedstock->receive_date = $request->receive_date;
            $seedstock->quantity = $request->quantity;
            $seedstock->amount = $request->amount;
            $seedstock->save();

            $client = new FarmingPayment();
            $client->farming_id = $request->farmer_id;
            $client->date = $request->receive_date;
            $client->amount = $request->amount;
            $client->type = "Seed Stock Entry";
            $client->created_by = Auth::user()->id;
            $client->save();

            Utility::total_quantity('plus', $seedstock->quantity, $seedstock->product_id);

            return redirect()->route('admin.seedstock.index')->with('success', __('Seed Stock Successfully Created.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     if (\Auth::user()->can('edit-seedstock')) {
    //         $seedstock = SeedStock::findorfail($id);
    //         $farmers = Farming::get();
    //         $products = ProductService::where('category_id', "11")->get();

    //         return view('admin.seedstock.edit', compact('seedstock', 'products', 'farmers'));
    //     } else {
    //         return redirect()->back()->with('error', 'Permission denied.');
    //     }
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     if (\Auth::user()->can('edit-seedstock')) {
    //         $seedstock = SeedStock::findorfail($id);
    //         $seedstock->farmer_id = $request->farmer_id;
    //         $seedstock->receive_date = $request->receive_date;
    //         $seedstock->amount = $request->amount;

    //         //inventory management (Quantity)
    //         $product_id = $seedstock->product_id;
    //         $quantity = $request->quantity;

    //         $product      = ProductService::find($product_id);
    //         if (($product->type == 'product')) {
    //             $pro_quantity = $product->quantity;
    //             $product->quantity = ($pro_quantity - $seedstock->quantity) + $quantity;
    //             $product->save();
    //         }

    //         $seedstock->quantity = $request->quantity;
    //         $seedstock->save();

    //         return redirect()->route('admin.seedstock.index')->with('success', __('Seed Stock Successfully Updated.'));
    //     } else {
    //         return redirect()->back()->with('error', 'Permission denied.');
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     if (\Auth::user()->can('delete-seedstock')) {
    //         $seedstock = SeedStock::findorfail($id);

    //         $product = ProductService::where('id', $seedstock->product_id)->first();
    //         $product->quantity = $product->quantity - $seedstock->quantity;
    //         $product->save();

    //         $seedstock->delete();

    //         return redirect()->route('admin.seedstock.index')->with('success', __('Seed Stock Successfully Deleted.'));
    //     } else {
    //         return redirect()->back()->with('error', 'Permission denied.');
    //     }
    // }

    public function getSeedAmount(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        $total_price = $product_service->purchase_price * $request->quantity;

        return response()->json([
            'total_price' => $total_price,
        ]);
    }

    public function getSeedStock(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        // dd($product_service);
        return response()->json($product_service);
    }
}
