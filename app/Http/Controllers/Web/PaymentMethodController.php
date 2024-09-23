<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Requests\Payroll\PaymentMethod\PaymentMethodStoreRequest;
use App\Requests\Payroll\PaymentMethod\PaymentMethodUpdateRequest;
use App\Services\Payroll\PaymentMethodService;
use Exception;

class PaymentMethodController extends Controller
{

    private $view = 'admin.payrollSetting.paymentMethod.';

    public function __construct(public PaymentMethodService $paymentMethodService) {}

    public function index()
    {
        if (\Auth::user()->can('manage-payment_method')) {
            try {
                $select = ['*'];
                $paymentMethodLists = $this->paymentMethodService->getAllPaymentMethodList($select);
                return view($this->view . 'index', compact('paymentMethodLists'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-payment_method')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(PaymentMethodStoreRequest $request)
    {
        if (\Auth::user()->can('create-payment_method')) {
            try {
                $validatedData = $request->validated();
                $this->paymentMethodService->store($validatedData);
                return redirect()
                    ->route('admin.payment-methods.index')
                    ->with('success', 'Payment Methods Added Successfully');
            } catch (Exception $e) {
                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(PaymentMethodUpdateRequest $request, $id)
    {
        if (\Auth::user()->can('edit-payment_method')) {
            try {
                $validatedData = $request->validated();
                $select = ['*'];
                $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id, $select);
                $update = $this->paymentMethodService->updateDetail($paymentMethodDetail, $validatedData);
                return AppHelper::sendSuccessResponse('Payment Method Updated Successfully', $update);
            } catch (Exception $e) {
                return AppHelper::sendErrorResponse($e->getMessage(), $e->getCode());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function deletePaymentMethod($id)
    {
        if (\Auth::user()->can('delete-payment_method')) {
            try {
                $select = ['*'];
                $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id, $select);
                $this->paymentMethodService->deletePaymentMethodDetail($paymentMethodDetail);
                return redirect()
                    ->back()
                    ->with('success', 'Payment Method Detail Deleted Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function togglePaymentMethodStatus($id)
    {
        if (\Auth::user()->can('edit-payment_method')) {
            try {
                $select = ['*'];
                $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id, $select);
                $this->paymentMethodService->changePaymentMethodStatus($paymentMethodDetail);
                return redirect()
                    ->back()
                    ->with('success', 'Status changed Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
