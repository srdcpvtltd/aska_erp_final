<?php

namespace App\DataTables;

use App\Models\FarmingDetail;
use App\Models\Plotdetail;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PlotdetailsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable($query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('g_code', function (FarmingDetail $farming_details) {
                return ($farming_details->farming != null) ? $farming_details->farming->old_g_code : '-';
            })
            ->addColumn('name', function (FarmingDetail $farming_details) {
                return ($farming_details->farming != null) ? $farming_details->farming->name : '-';
            })
            ->addColumn('father_name', function (FarmingDetail $farming_details) {
                return ($farming_details->farming != null) ? $farming_details->farming->father_name : '-';
            })
            ->editColumn('area_in_acar', function (FarmingDetail $farming_details) {
                return ($farming_details->area_in_acar != null) ? number_format($farming_details->area_in_acar, 2) : '-';
            })
            ->editColumn('date_of_harvesting', function (FarmingDetail $farming_details) {
                return ($farming_details->date_of_harvesting != null) ? date('d-m-Y', strtotime($farming_details->date_of_harvesting)) : '-';
            })
            ->editColumn('tentative_harvest_quantity', function (FarmingDetail $farming_details) {
                return ($farming_details->tentative_harvest_quantity != null) ? number_format($farming_details->tentative_harvest_quantity, 2) : '-';
            })
            ->editColumn('seed_category_id', function (FarmingDetail $farming_details) {
                return ($farming_details->seed_category_id != null) ? $farming_details->seed_category->name : '-';
            })
            ->editColumn('is_cutting_order', function (FarmingDetail $farming_details) {
                return ($farming_details->is_cutting_order != 0) ? '<span
                                                    class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">Yes</span>' : '<span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">No</span>';
            })
            ->editColumn('can_field_village_id', function (FarmingDetail $farming_details) {
                return ($farming_details->can_field_village_id != null) ? $farming_details->can_field_village->name : '-';
            })
            ->editColumn('can_field_center_id', function (FarmingDetail $farming_details) {
                return ($farming_details->can_field_center_id != null) ? $farming_details->can_field_center->name : '-';
            })
            ->editColumn('irregation', function (FarmingDetail $farming_details) {
                return ($farming_details->irregation != null) ? $farming_details->irrig->name : '-';
            })
            ->addColumn('irregation_code', function (FarmingDetail $farming_details) {
                return ($farming_details->irregation != null) ? $farming_details->irrig->code : '-';
            })
            ->editColumn('created_at', function (FarmingDetail $farming_details) {
                return ($farming_details->created_at != null) ? date('d-m-Y', strtotime($farming_details->created_at)) : '-';
            })
            ->rawColumns(['is_cutting_order'])
            ->addColumn('action', function (FarmingDetail $farming_detail) {
                return view('admin.farmer.farming_detail.action', compact('farming_detail'));
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\FarmingDetail $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FarmingDetail $model): QueryBuilder
    {
        $query = $model->newQuery()->select('farming_details.*')
            ->join('users', 'users.id', 'farming_details.created_by')
            ->where(function ($query) {
                $query->where('farming_details.created_by', Auth::user()->id)
                    ->orWhere('users.supervisor_id', Auth::user()->id);
            })
            ->orderBy('farming_details.id', 'ASC');

        $zone_id = $this->request()->get(key: 'zone_id');
        $center_id = $this->request()->get(key: 'center_id');

        if (!empty($zone_id)) {

            $query = $query->where('farming_details.zone_id', $zone_id);
        }
        if (!empty($center_id)) {

            $query = $query->where('farming_details.center_id', $center_id);
        }
        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('farming_details-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('export')->className('btn-success '),
                Button::make('print')->className('btn-light '),
                Button::make('pageLength')->className('btn-light ')
            )->language([
                'buttons' => [
                    'export' => __('Export'),
                    'print' => __('Print'),
                    'excel' => __('Excel'),
                    'csv' => __('CSV'),
                    'pageLength' => __('Show %d rows'),
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')
                ->title('Sl No.')
                ->render('meta.row + meta.settings._iDisplayStart + 1;')
                ->orderable(false),
            Column::make('g_code')->title('G Code'),
            Column::make('name')->title('Farmer'),
            Column::make('father_name')->title('Father Name'),
            Column::make('plot_number')->title('Plot Number'),
            Column::make('area_in_acar')->title('Area In Acre'),
            Column::make('date_of_harvesting')->title('Date Of Planting'),
            Column::make('tentative_harvest_quantity')->title('Tentative Plant Quantity'),
            Column::make('seed_category_id')->title('Seed Category'),
            Column::make('is_cutting_order')->title('Cutting Order'),
            Column::make('can_field_village_id')->title('Can Field Village'),
            Column::make('can_field_center_id')->title('Can Field Center'),
            Column::make('planting_category')->title('Planting Category'),
            Column::make('irregation')->title('Irrigation Name'),
            Column::make('irregation_code')->title('Irrigation Code'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Plotdetails_' . date('YmdHis');
    }
}
