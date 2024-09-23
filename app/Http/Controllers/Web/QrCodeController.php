<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Qr\QrCodeService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    private $view = 'admin.qr.';

    public function __construct(public QrCodeService $qrCodeService) {}
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        if (\Auth::user()->can('manage-qr')) {
            try {
                $qrData = $this->qrCodeService->getAllQr();
                return view($this->view . 'index', compact('qrData'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|RedirectResponse|Response
     */
    public function create(): View|Factory|Response|RedirectResponse|Application
    {
        if (\Auth::user()->can('create-qr')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function store(Request $request): Response|RedirectResponse
    {
        if (\Auth::user()->can('create-qr')) {
            try {
                $validatedData = $request->all();

                $this->qrCodeService->saveQrDetail($validatedData);
                return redirect()->route('admin.qr.index')->with('success', 'QR created successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|RedirectResponse|Response
     */
    public function edit(int $id): View|Factory|Response|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-qr')) {
            try {

                $qrData = $this->qrCodeService->findQrDetailById($id);
                return view($this->view . 'edit', compact('qrData'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function update(Request $request, int $id): Response|RedirectResponse
    {
        if (\Auth::user()->can('edit-qr')) {
            try {
                $this->qrCodeService->updateQrDetail($request->all(), $id);
                return redirect()->route('admin.qr.index')->with('success', 'QR updated successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function destroy(int $id): Response|RedirectResponse
    {
        if (\Auth::user()->can('delete-qr')) {
            try {
                $this->qrCodeService->deleteQrDetail($id);
                return redirect()->route('admin.qr.index')->with('success', 'QR deleted successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * @param int $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function print(int $id): View|Factory|RedirectResponse|Application
    {
        try {
            $qrData = $this->qrCodeService->findQrDetailById($id);
            return view($this->view . 'print', compact('qrData'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
