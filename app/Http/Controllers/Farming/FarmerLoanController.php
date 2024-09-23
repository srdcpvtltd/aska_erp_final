<?php

namespace App\Http\Controllers\Farming;

use App\Models\FarmerLoan;
use App\Http\Controllers\Controller;
use App\Models\Farming;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FarmerLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->can('manage-allotment')) {
            $loans = FarmerLoan::where('created_by', Auth::user()->id)->get();
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
            $categories = ProductServiceCategory::all();
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
        if (\Auth::user()->can('create-allotment')) {
            try {
                $this->validate($request, [
                    'farming_id' => 'required',
                    'created_by' => 'required',
                ]);
                $encoded_loan_category_id = json_encode($request->loan_category_id);
                $encoded_loan_type_id = json_encode($request->loan_type_id);
                $encoded_price_kg = json_encode($request->price_kg);
                $encoded_quantity = json_encode($request->quantity);
                $encoded_total_amount = json_encode($request->total_amount);

                $farmerLoan = new FarmerLoan;
                $farmerLoan->farming_id = $request->farming_id;
                $farmerLoan->registration_number = $request->registration_number;
                $farmerLoan->agreement_number = $request->agreement_number;
                $farmerLoan->date = $request->date;
                $farmerLoan->loan_category_id = $encoded_loan_category_id;
                $farmerLoan->loan_type_id = $encoded_loan_type_id;
                $farmerLoan->price_kg = $encoded_price_kg;
                $farmerLoan->quantity = $encoded_quantity;
                $farmerLoan->total_amount = $encoded_total_amount;
                $farmerLoan->created_by = $request->created_by;
                $farmerLoan->save();

                return redirect()->to(route('admin.farmer.loan.index'))->with('success', 'Loan Added Successfully.');
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
    public function show(FarmerLoan $farmerLoan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-allotment')) {
            $farmings = Farming::query()->select('farmings.*')->join('users', 'users.id', 'farmings.created_by')
                ->where('farmings.is_validate', 1)
                ->where('farmings.created_by', Auth::user()->id)
                ->orWhere('users.supervisor_id', Auth::user()->id)
                ->get();
            $loan = FarmerLoan::find($id);
            $categories = ProductServiceCategory::all();
            $types = ProductService::all();

            return view('admin.farmer.loan.edit', compact(
                'farmings',
                'loan',
                'categories',
                'types',
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
                $encoded_loan_category_id = json_encode($request->loan_category_id);
                $encoded_loan_type_id = json_encode($request->loan_type_id);
                $encoded_price_kg = json_encode($request->price_kg);
                $encoded_quantity = json_encode($request->quantity);
                $encoded_total_amount = json_encode($request->total_amount);

                $farmerLoan = FarmerLoan::find($id);
                $farmerLoan->farming_id = $request->farming_id;
                $farmerLoan->registration_number = $request->registration_number;
                $farmerLoan->agreement_number = $request->agreement_number;
                $farmerLoan->date = $request->date;
                $farmerLoan->loan_category_id = $encoded_loan_category_id;
                $farmerLoan->loan_type_id = $encoded_loan_type_id;
                $farmerLoan->price_kg = $encoded_price_kg;
                $farmerLoan->quantity = $encoded_quantity;
                $farmerLoan->total_amount = $encoded_total_amount;
                $farmerLoan->update();

                return redirect()->back()->with('success', 'Farming Loan Updated Successfully.');
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
        $product_service = ProductService::find($request->loan_type_id);
        $quantity = $product_service->getTotalProductQuantity()
            && $product_service->getTotalProductQuantity() > 0 ? $product_service->getTotalProductQuantity() : 0;

        return response()->json([
            'quantity' => $quantity,
            'product_service' => $product_service,
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
