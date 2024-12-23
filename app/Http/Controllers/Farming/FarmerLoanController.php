<?php

namespace App\Http\Controllers\Farming;

use App\Models\FarmerLoan;
use App\Http\Controllers\Controller;
use App\Models\Farming;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\StockReport;
use App\Models\Utility;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FarmerLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-allotment')) {
            $loans = FarmerLoan::where('loan_category_id', '!=', "11")->where('created_by', Auth::user()->id)->get();
            return view('admin.farmer.loan.index', compact('loans'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create-allotment')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            $categories = ProductServiceCategory::where('id', '!=', "11")->get();
            return view('admin.farmer.loan.create', compact('categories', 'farmings'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if (\Auth::user()->can('create-allotment')) {
            try {
                $validator = Validator::make($request->all(), [
                    'farming_id' => 'required',
                    'created_by' => 'required',
                ]);

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('danger', $messages->first());
                }

                $encoded_loan_type_id = json_encode($request->loan_type_id);
                $encoded_price_kg = json_encode($request->price_kg);
                $encoded_quantity = json_encode($request->quantity);
                $encoded_total_amount = json_encode($request->total_amount);

                $farmerLoan = new FarmerLoan;
                $farmerLoan->farming_id = $request->farming_id;
                $farmerLoan->invoice_no = $request->invoice_no;
                $farmerLoan->warehouse_id = $request->warehouse_id;
                $farmerLoan->registration_number = $request->registration_number;
                $farmerLoan->date = $request->date;
                $farmerLoan->loan_category_id = $request->loan_category_id;
                $farmerLoan->loan_type_id = $encoded_loan_type_id;
                $farmerLoan->price_kg = $encoded_price_kg;
                $farmerLoan->quantity = $encoded_quantity;
                $farmerLoan->total_amount = $encoded_total_amount;
                $farmerLoan->bill_amount = $request->bill_amount;
                $farmerLoan->round_amount = $request->round_amount;
                $farmerLoan->created_by = $request->created_by;
                $farmerLoan->save();

                $count = count($request->quantity);

                for ($i = 0; $i < $count; $i++) {
                    //inventory management (Quantity)
                    Utility::total_quantity('minus', $request['quantity'][$i], $request['loan_type_id'][$i]);

                    //Warehouse Stock Report
                    $product_id = $request['loan_type_id'][$i];
                    $quantity = $request['quantity'][$i];
                    $warehouse_id = $request['warehouse_id'];

                    if (isset($product_id) && $warehouse_id !== null && $request->loan_category_id !== "11") {

                        $product = WarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();

                        if ($product) {
                            $pro_quantity = $product->quantity;
                            $product_quantity = $pro_quantity - $quantity;
                        } else {
                            $product_quantity = $quantity;
                        }

                        $data = WarehouseProduct::updateOrCreate(
                            ['warehouse_id' => $warehouse_id, 'product_id' => $product_id],
                            ['warehouse_id' => $warehouse_id, 'product_id' => $product_id, 'quantity' => $product_quantity, 'created_by' => \Auth::user()->id]
                        );

                        //Product Stock Report
                        $type = 'allotment';
                        $type_id = 0;
                        $description = $request['quantity'][$i] . ' ' . __('quantity deducted by allotment');

                        $stocks             = new StockReport();
                        $stocks->warehouse_id = $warehouse_id;
                        $stocks->product_id = $product_id;
                        $stocks->quantity     = $quantity;
                        $stocks->type = $type;
                        $stocks->type_id = $type_id;
                        $stocks->description = $description;
                        $stocks->created_by = \Auth::user()->creatorId();
                        $stocks->save();
                    }
                }

                return redirect()->to(route('admin.farmer.loan.index'))->with('success', 'Loan Added Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-allotment')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                // ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            $loan = FarmerLoan::find($id);
            $categories = ProductServiceCategory::all();
            $warehouses = Warehouse::all();
            $types = ProductService::all();

            return view('admin.farmer.loan.edit', compact(
                'farmings',
                'loan',
                'categories',
                'types',
                'warehouses'
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
        if (\Auth::user()->can('edit-allotment')) {
            try {
                $farmerLoan = FarmerLoan::find($id);

                if (isset($request->quantity) && !empty($request->quantity)) {
                    $count = count($request->quantity);

                    for ($i = 0; $i < $count; $i++) {
                        //inventory management (Quantity)
                        $product_id = $request->loan_type_id[$i];
                        $quantity = $request->quantity[$i];

                        $product      = ProductService::find($product_id);
                        if (($product->type == 'product')) {
                            $pro_quantity = $product->quantity;
                            $product->quantity = $pro_quantity - $quantity;
                            $product->save();
                        }

                        //Warehouse Stock Report
                        $warehouse_id = $request['warehouse_id'];

                        if (isset($request['loan_type_id'][$i])) {

                            $product = WarehouseProduct::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->first();

                            if ($product) {
                                $pro_quantity = $product->quantity;
                                $product_quantity = $pro_quantity - $quantity;
                            } else {
                                $product_quantity = $quantity;
                            }

                            $data = WarehouseProduct::updateOrCreate(
                                ['warehouse_id' => $warehouse_id, 'product_id' => $product_id],
                                ['warehouse_id' => $warehouse_id, 'product_id' => $product_id, 'quantity' => $product_quantity, 'created_by' => \Auth::user()->id]
                            );
                        }

                        //Product Stock Report
                        $type = 'allotment';
                        $type_id = 0;
                        $description = $request['quantity'][$i] . ' ' . __('quantity deducted by allotment');

                        $stocks             = new StockReport();
                        $stocks->warehouse_id = $warehouse_id;
                        $stocks->product_id = $product_id;
                        $stocks->quantity     = $quantity;
                        $stocks->type = $type;
                        $stocks->type_id = $type_id;
                        $stocks->description = $description;
                        $stocks->created_by = \Auth::user()->creatorId();
                        $stocks->save();
                    }

                    $loan_type_id_array = json_decode($farmerLoan->loan_type_id);
                    $merged_loan_type_id = array_merge($loan_type_id_array, $request->loan_type_id);

                    $price_kg_array = json_decode($farmerLoan->price_kg);
                    $merged_price_kg = array_merge($price_kg_array, $request->price_kg);

                    $quantity_array = json_decode($farmerLoan->quantity);
                    $merged_quantity = array_merge($quantity_array, $request->quantity);

                    $total_amount_array = json_decode($farmerLoan->total_amount);
                    $merged_total_amount = array_merge($total_amount_array, $request->total_amount);


                    $encoded_loan_type_id = json_encode($merged_loan_type_id);
                    $encoded_price_kg = json_encode($merged_price_kg);
                    $encoded_quantity = json_encode($merged_quantity);
                    $encoded_total_amount = json_encode($merged_total_amount);

                    $farmerLoan->loan_type_id = $encoded_loan_type_id;
                    $farmerLoan->price_kg = $encoded_price_kg;
                    $farmerLoan->quantity = $encoded_quantity;
                    $farmerLoan->total_amount = $encoded_total_amount;
                    $farmerLoan->bill_amount = $request->bill_amount;
                    $farmerLoan->round_amount = $request->round_amount;
                    $farmerLoan->update();
                }

                return redirect()->to(route('admin.farmer.loan.index'))->with('success', 'Farming Loan Updated Successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-allotment')) {
            $loan = FarmerLoan::find($id);
            if($loan->loan_category_id != "11"){
                $loan_type_id = json_decode($loan->loan_type_id);
                $quantity = json_decode($loan->quantity);
                $count = count($loan_type_id);

                for ($i = 0; $i < $count; $i++) {
                    $product = ProductService::where('id', $loan_type_id[$i])->first();
                    $product->quantity = $product->quantity - $quantity[$i];
                    $product->save();

                    if (isset($loan_type_id) && $loan->warehouse_id !== null && $loan->loan_category_id !== "11") {
                        $warehouse_product = WarehouseProduct::where('warehouse_id', $loan->warehouse_id)->where('product_id', $loan_type_id[$i])->first();
                        $warehouse_product->quantity = $warehouse_product->quantity - $quantity[$i];
                        $warehouse_product->save();

                        $description = $quantity[$i] . ' quantity deducted by allotment';

                        $stock_report = StockReport::where('warehouse_id', $loan->warehouse_id)
                            ->where('product_id', $loan_type_id[$i])
                            ->where('quantity', $quantity[$i])
                            ->where('type', "allotment")
                            ->where('description', $description)
                            ->first();

                        $stock_report->delete();
                    }
                }
            }
            $loan->delete();
            return redirect()->back()->with('success', 'Farming Loan Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function getProductServiceByCategory(Request $request)
    {
        $product_services = ProductService::where('category_id', $request->loan_category_id)->get();
        return response()->json([
            'product_services' => $product_services,
        ]);
    }

    public function getProductServiceDetail(Request $request)
    {
        $product_service = ProductService::find($request->product_id);
        $warehouse_product = WarehouseProduct::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        return response()->json([
            'warehouse_product' => $warehouse_product,
            'product_service' => $product_service,
        ]);
    }

    public function getWarehouseProduct(Request $request)
    {
        $warehouse_product = WarehouseProduct::where('product_id', $request->loan_type_id)->where('created_by', Auth::user()->id)->get();
        $warehouses = [];
        foreach ($warehouse_product as $warehouse) {
            $warehouses[] = Warehouse::where('id', $warehouse->warehouse_id)->first();
        }

        return response()->json([
            'warehouse_product' => $warehouse_product,
            'warehouse' => $warehouses
        ]);
    }

    public function getFarmingDetail(Request $request)
    {
        $farming = Farming::find($request->farming_id);
        return response()->json([
            'farming' => $farming
        ]);
    }

    public function invoice_generate($id)
    {
        $farmingloan = FarmerLoan::findorfail($id);
        if ($farmingloan->invoice_generate_status == 0) {
            $data = $farmingloan;
            $farming = Farming::findorfail($farmingloan->farming_id);

            $pdf = Pdf::loadView('admin.farmer.loan.invoice', compact('data', 'farming'));

            $path = public_path('/farmer/allotment/');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file_name = time() . '.pdf';
            $pdf->save($path  . $file_name);
            $pdf->download($file_name);

            $farmingloan->invoice = $file_name;
            $farmingloan->invoice_generate_status = 1;
            $farmingloan->save();
        }
        return redirect('/farmer/allotment/' . $farmingloan->invoice);
    }
}
