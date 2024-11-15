<?php

namespace App\DataTables;

use App\Models\Farmer;
use App\Models\Farming;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class FarmerDataTable extends DataTable
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
            ->editColumn('g_code', function (Farming $farming) {
                return ($farming->g_code != null) ? $farming->g_code : '<span
                                                    class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">Not
                                                    Assigned</span>';
            })
            ->editColumn('state_id', function (Farming $farming) {
                return ($farming->state_id != null) ? $farming->state->name:'-';
            })
            ->editColumn('district_id', function (Farming $farming) {
                return ($farming->district_id != null) ? $farming->district->name:'-';
            })
            ->editColumn('block_id', function (Farming $farming) {
                return ($farming->block_id != null) ? $farming->block->name:'-';
            })
            ->editColumn('gram_panchyat_id', function (Farming $farming) {
                return ($farming->gram_panchyat_id != null) ? $farming->gram_panchyat->name:'-';
            })
            ->editColumn('village_id', function (Farming $farming) {
                return ($farming->village_id != null) ? $farming->village->name:'-';
            })
            ->editColumn('zone_id', function (Farming $farming) {
                return ($farming->zone_id != null) ? $farming->zone->name:'-';
            })
            ->editColumn('center_id', function (Farming $farming) {
                return ($farming->center_id != null) ? $farming->center->name:'-';
            })
            ->editColumn('bank', function (Farming $farming) {
                return ($farming->bank != null) ? $farming->bank_data->name:'-';
            })
            ->editColumn('branch', function (Farming $farming) {
                return ($farming->branch != null) ? $farming->bank_branch->name:'-';
            })
            ->editColumn('is_validate', function (Farming $farming) {
                return ($farming->is_validate != 0) ? 
                '<span class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">Validated</span>' : 
                '<span class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">Not
                                                    Validated</span>';
            })
            ->rawColumns(['is_validate','g_code'])
            ->addColumn('action', function (Farming $farming) {
                return view('admin.farmer.registration.action', compact('farming'));
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Farming $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Farming $model): QueryBuilder
    {
        $query = $model->newQuery()->select('farmings.*')
            ->join('users', 'users.id', 'farmings.created_by')
            ->where('farmings.created_by', Auth::user()->id)
            ->orWhere('users.supervisor_id', Auth::user()->id);

        $block_id =  $this->request()->get(key: 'block_id');
        $gp_id = $this->request()->get(key: 'grampanchyat_id');
        $village_id = $this->request()->get(key: 'village_id');
        $zone_id = $this->request()->get(key: 'zone_id');
        $center_id = $this->request()->get(key: 'center_id');

        if (!empty($block_id)){

            $query = $query->where('farmings.block_id',$block_id);
        }
        if (!empty($gp_id)){

            $query = $query->where('farmings.gram_panchyat_id',$gp_id);
        }
        if (!empty($village_id)){

            $query = $query->where('farmings.village_id',$village_id);
        }
        if (!empty($zone_id)){

            $query = $query->where('farmings.zone_id',$zone_id);
        }
        if (!empty($center_id)){

            $query = $query->where('farmings.center_id',$center_id);
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
            ->setTableId('farmings-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('export')->className('btn-light '),
                Button::make('print')->className('btn-light '),
                Button::make('reset')->className('btn-light '),
                Button::make('reload')->className('btn-light '),
                Button::make('pageLength')->className('btn-light ')
            )->language([
                'buttons' => [
                    'export' => __('Export'),
                    'print' => __('Print'),
                    'reset' => __('Reset'),
                    'reload' => __('Reload'),
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
            Column::make('name')->title('Name'),
            Column::make('father_name')->title('Father Name'),
            Column::make('old_g_code')->title('G. Code'),
            Column::make('mobile')->title('Mobile'),
            Column::make('age')->title('Age'),
            Column::make('gender')->title('Gender'),
            Column::make('farmer_category')->title('Category'),
            Column::make('qualification')->title('Qualification'),
            Column::make('state_id')->title('State'),
            Column::make('district_id')->title('District'),
            Column::make('block_id')->title('Block'),
            Column::make('gram_panchyat_id')->title('GP'),
            Column::make('village_id')->title('Village'),
            Column::make('zone_id')->title('Zone'),
            Column::make('center_id')->title('Center'),
            Column::make('bank')->title('Bank'),
            Column::make('branch')->title('Bank Branch'),
            Column::make('is_validate')->title('Status'),
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
        return 'Farmer_' . date('YmdHis');
    }
}
