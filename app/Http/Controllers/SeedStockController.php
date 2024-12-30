<?php

namespace App\Http\Controllers;

use App\Models\FarmerLoan;
use App\Models\Farming;
use App\Models\FarmingPayment;
use App\Models\ProductService;
use App\Models\Village;
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
            $loans = FarmerLoan::where('loan_category_id',"11")->where('created_by', Auth::user()->id)->get();
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
                'farmer_id_from' => 'required',
                'farmer_id_to' => 'required',
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
            $amount = $request->amount;

            $client = new FarmingPayment();
            $client->farming_id = $request->farmer_id_from;
            $client->g_code = $request->g_code_from;
            $client->date = $request->receive_date;
            $client->amount = $amount[0];
            $client->type = "Seed Stock Entry";
            $client->created_by = Auth::user()->id;
            $client->save();

            $seedstock = new FarmerLoan();
            $seedstock->farming_id = $request->farmer_id_to;
            $seedstock->invoice_no = $request->invoice_no;
            $seedstock->loan_category_id = '11';
            $seedstock->loan_type_id = json_encode($request->product_id);
            $seedstock->price_kg = json_encode($request->unit_price);
            $seedstock->date = $request->receive_date;
            $seedstock->quantity = json_encode($request->quantity);
            $seedstock->total_amount = json_encode($amount);
            $seedstock->bill_amount = $amount[0];
            $seedstock->round_amount = round($amount[0]);
            $seedstock->farming_payment_id = $client->id;
            $seedstock->created_by = Auth::user()->id;
            $seedstock->save();

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
    public function edit($id)
    {
        if (\Auth::user()->can('edit-seedstock')) {
            $farmer_loan = FarmerLoan::findorfail($id);
            if($farmer_loan->farming_payment_id != 0){
                $farmers_from = Farming::where('id', $farmer_loan->farming_payment->farming_id)->get();
                $villages_from = Village::where('id', $farmer_loan->farming_payment->farming->village_id)->get();
            }
            
            $farmers = Farming::where('id', $farmer_loan->farming_id)->get();
            $villages = Village::where('id', $farmer_loan->farming->village_id)->get();
            $products = ProductService::where('category_id', "11")->get();

            return view('admin.seedstock.edit', compact('farmer_loan', 'products', 'farmers', 'villages', 'farmers_from', 'villages_from'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-seedstock')) {
            $amount = $request->amount;

            $seedstock = FarmerLoan::findorfail($id);
            $seedstock->farming_id = $request->farmer_id_to;
            $seedstock->invoice_no = $request->invoice_no;
            $seedstock->loan_category_id = '11';
            $seedstock->loan_type_id = json_encode($request->product_id);
            $seedstock->price_kg = json_encode($request->unit_price);
            $seedstock->date = $request->receive_date;
            $seedstock->quantity = json_encode($request->quantity);
            $seedstock->total_amount = json_encode($amount);
            $seedstock->bill_amount = $amount[0];
            $seedstock->round_amount = round($amount[0]);
            $seedstock->created_by = Auth::user()->id;
            $seedstock->save();

            $client = FarmingPayment::where('id', $seedstock->farming_payment_id)->first();
            $client->farming_id = $request->farmer_id_from;
            $client->g_code = $request->g_code_from;
            $client->date = $request->receive_date;
            $client->amount = $amount[0];
            $client->type = "Seed Stock Entry";
            $client->created_by = Auth::user()->id;
            $client->save();

            return redirect()->route('admin.seedstock.index')->with('success', __('Seed Stock Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-seedstock')) {
            $farmer_loan = FarmerLoan::findorfail($id);
            $payment = FarmingPayment::where('id', $farmer_loan->farming_payment_id)->delete();
            $farmer_loan->delete();

            return redirect()->route('admin.seedstock.index')->with('success', __('Seed Stock Successfully Deleted.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getSeedAmount(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        $total_price = $product_service->sale_price * $request->quantity;

        return response()->json([
            'total_price' => $total_price,
        ]);
    }

    public function getSeedStock(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        return response()->json($product_service);
    }
}
