<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Requests\Client\ClientRequest;
use App\Services\Client\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    private $view = 'admin.client.';

    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        if (\Auth::user()->can('manage-client')) {
            try {
                $clientLists = $this->clientService->getAllClientsList();
                return view($this->view . 'index', compact('clientLists'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-client')) {
            try {
                return view($this->view . 'create');
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(ClientRequest $request)
    {
        if (\Auth::user()->can('create-client')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->clientService->saveClientDetail($validatedData);
                DB::commit();
                return redirect()->route('admin.clients.index')->with('success', 'Client Detail Created Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function ajaxClientStore(ClientRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            DB::beginTransaction();
            $client = $this->clientService->saveClientDetail($validatedData);
            DB::commit();
            return AppHelper::sendSuccessResponse('Client Detail Created Successfully', $client);
        } catch (\Exception $exception) {
            DB::rollBack();
            return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function show($clientId)
    {
        if (\Auth::user()->can('show-client')) {
            try {
                $select = ['*'];
                $with = [
                    'projects:id,client_id,name,start_date,deadline,cost,status',
                    'projects.tasks:id,project_id',
                    'projects.completedTask:id,project_id'
                ];
                $clientDetail = $this->clientService->findClientDetailById($clientId, $select, $with);
                return view($this->view . 'show', compact('clientDetail'));
            } catch (\Exception $e) {

                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-client')) {
            try {
                $clientDetail = $this->clientService->findClientDetailById($id);
                return view($this->view . 'edit', compact('clientDetail'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(ClientRequest $request, $id)
    {
        if (\Auth::user()->can('edit-client')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->clientService->updateClientDetail($validatedData, $id);
                DB::commit();
                return redirect()->route('admin.clients.index')
                    ->with('success', 'Client Detail Updated Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleIsActiveStatus($id)
    {
        if (\Auth::user()->can('edit-client')) {
            try {
                $this->clientService->toggleIsActiveStatus($id);
                return redirect()->back()->with('success', 'Status changed  Successfully');
            } catch (\Exception $exception) {

                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function delete($id)
    {
        if (\Auth::user()->can('delete-client')) {
            try {
                DB::beginTransaction();
                $this->clientService->deleteClientDetail($id);
                DB::commit();
                return redirect()->back()->with('success', 'Client Detail Deleted  Successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
