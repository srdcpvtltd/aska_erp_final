<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Nfc\NfcService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NFCController extends Controller
{

    private $view = 'admin.nfc.';

    public function __construct(public NfcService $nfcService) {}

    /**
     * @return Application|Factory|View|RedirectResponse
     * @throws AuthorizationException
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        if (\Auth::user()->can('manage-nfc')) {

            try {
                $nfcData = $this->nfcService->getAllNfc();
                return view($this->view . 'index', compact('nfcData'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * @param $id
     * @return RedirectResponse
     *
     */
    public function delete($id): RedirectResponse
    {
        if (\Auth::user()->can('delete-nfc')) {
            try {
                $this->nfcService->deleteNfcDetail($id);
                return redirect()->route('admin.nfc.index')->with('success', 'NFC deleted successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
