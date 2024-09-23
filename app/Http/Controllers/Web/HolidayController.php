<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Imports\HolidaysImport;
use App\Requests\Holiday\HolidayRequest;
use App\Services\Holiday\HolidayService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HolidayController extends Controller
{
    private $view = 'admin.holiday.';

    private HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage-holidays')) {
            try {
                $filterParameters['event_year'] = $request->event_year ?? Carbon::now()->format('Y');
                $filterParameters['event'] = $request->event ?? null;
                $filterParameters['month'] = $request->month ?? null;
                if (AppHelper::ifDateInBsEnabled()) {
                    $nepaliDate = AppHelper::getCurrentNepaliYearMonth();
                    $filterParameters['event_year'] = $request->event_year ?? $nepaliDate['year'];
                }
                $months = AppHelper::MONTHS;
                $select = ['id', 'event', 'event_date', 'is_active'];
                $holidays = $this->holidayService->getAllHolidayLists($filterParameters, $select);
                return view($this->view . 'index', compact(
                    'holidays',
                    'filterParameters',
                    'months'
                ));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create(): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('create-holidays')) {
            try {
                return view($this->view . 'create');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(HolidayRequest $request): RedirectResponse
    {
        if (\Auth::user()->can('create-holidays')) {
            try {
                $validatedData = $request->validated();
                DB::beginTransaction();
                $this->holidayService->store($validatedData);
                DB::commit();
                return redirect()->route('admin.holidays.index')->with('success', 'New Holiday Detail Added Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('danger', $e->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show($id): JsonResponse
    {
        if (\Auth::user()->can('show-holidays')) {
            try {
                $holiday = $this->holidayService->findHolidayDetailById($id);
                $holiday->event_date = AppHelper::formatDateForView($holiday->event_date);
                return response()->json([
                    'data' => $holiday,
                ]);
            } catch (Exception $exception) {
                return AppHelper::sendErrorResponse($exception->getMessage(), $exception->getCode());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function edit($id): Factory|View|RedirectResponse|Application
    {
        if (\Auth::user()->can('edit-holidays')) {
            try {
                $holidayDetail = $this->holidayService->findHolidayDetailById($id);
                if (AppHelper::ifDateInBsEnabled()) {
                    $holidayDetail['event_date'] = AppHelper::dateInYmdFormatEngToNep($holidayDetail['event_date']);
                }
                return view($this->view . 'edit', compact('holidayDetail'));
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(HolidayRequest $request, $id): RedirectResponse
    {
        if (\Auth::user()->can('edit-holidays')) {
            try {
                $validatedData = $request->validated();
                $this->holidayService->update($validatedData, $id);
                return redirect()->route('admin.holidays.index')->with('success', 'Holiday Detail Updated Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage())
                    ->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function toggleStatus($id): RedirectResponse
    {
        if (\Auth::user()->can('edit-holidays')) {
            try {
                $this->holidayService->toggleHolidayStatus($id);
                return redirect()->back()->with('success', 'Holiday Status Changed  Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function delete($id): RedirectResponse
    {
        if (\Auth::user()->can('delete-holidays')) {
            try {
                $this->holidayService->delete($id);
                return redirect()->back()->with('success', 'Holiday Removed Successfully');
            } catch (Exception $exception) {
                return redirect()->back()->with('danger', $exception->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function holidayImport(): Factory|View|Application
    {
        return view($this->view . 'importHolidays');
    }

    public function importHolidays(Request $request)
    {
        try {
            $validate = $request->validate([
                'file' => 'required|file|mimes:csv,txt'
            ]);
            $holidayCSV = $request->file;
            $handle = fopen($holidayCSV, "r");
            $header = fgetcsv($handle, 0, ',');
            $countheader = count($header);
            if ($countheader < 5 && in_array('event', $header) && in_array('event_date', $header) && in_array('note', $header)) {
                Excel::import(new HolidaysImport, $holidayCSV);
                return redirect()->route('admin.holidays.index')->with('success', 'Holidays Detail Imported Successfully');
            } else {
                return redirect()->route('admin.holidays.index')->with('danger', 'Your CSV files having unmatched Columns to our database. Your columns must be in this sequence  event, event_date, note and is_public_holiday only');
            }
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
