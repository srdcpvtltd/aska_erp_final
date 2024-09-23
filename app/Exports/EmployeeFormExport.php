<?php

namespace App\Exports;

use App\Models\Branch;
use App\Models\Department;
use App\Models\OfficeTime;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeFormExport implements FromCollection, WithHeadings, WithEvents, WithStyles
{
    protected  $selects;
    protected  $row_count;
    protected  $column_count;

    public function __construct()
    {
        $roles = Role::pluck('name', 'id')->toArray();
        $branches = Branch::pluck('name', 'id')->toArray();

        $gender = ['Male', 'Female', 'Others'];
        $marital_status = ['Single', 'Married'];
        $departments = Department::pluck('dept_name', 'id')->toArray();
        $posts = Post::pluck('post_name', 'id')->toArray();
        $supervisors = User::pluck('name', 'id')->toArray();
        $employment_type = ['Contract', 'Permanent', 'Temporary'];
        $office_time = OfficeTime::pluck('opening_time', 'id')->toArray();
        $workspaces = ['Field', 'Office'];
        $bank_account_type = ['Saving', 'Current', 'Salary'];


        $selects = [  //selects should have column_name and options
            ['columns_name' => 'F', 'options' => $gender],
            ['columns_name' => 'G', 'options' => $marital_status],
            ['columns_name' => 'L', 'options' => $roles], //Column L has heading departments. See headings() method below
            ['columns_name' => 'M', 'options' => $branches],
            ['columns_name' => 'N', 'options' => $departments],
            ['columns_name' => 'O', 'options' => $posts],
            ['columns_name' => 'P', 'options' => $supervisors],
            ['columns_name' => 'Q', 'options' => $employment_type],
            ['columns_name' => 'R', 'options' => $office_time],
            ['columns_name' => 'T', 'options' => $workspaces],
            ['columns_name' => 'Z', 'options' => $bank_account_type],
        ];

        $this->selects = $selects;
        $this->row_count = 100; //number of rows that will have the dropdown
        $this->column_count = 26; //number of columns to be auto sized
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Address',
            'Email',
            'Phone',
            'Dob',
            'Gender',
            'Marital_Status',
            'Avatar',
            'Description',
            'Username',
            'Password',
            'Role',
            'Branch',
            'Department',
            'Post',
            'Supervisor',
            'Employment Type',
            'Office Time',
            'Joining_Date',
            'Workspace Type',
            'Leave Allocated',
            'Leave Active',
            'Bank Name',
            'Bank Account Number',
            'Account Holder Name',
            'Bank Account Type',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFA0A0A0'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $row_count = $this->row_count;
                $column_count = $this->column_count;
                foreach ($this->selects as $select) {
                    $drop_column = $select['columns_name'];
                    $options = $select['options'];
                    // set dropdown list for first data row
                    $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Value is not in list.');
                    $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

                    // clone validation to remaining rows
                    for ($i = 3; $i <= $row_count; $i++) {
                        $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    }
                    // set columns to autosize
                    for ($i = 1; $i <= $column_count; $i++) {
                        $column = Coordinate::stringFromColumnIndex($i);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                    //make th 1st row disabld
                    $sheet = $event->sheet->getDelegate();
                    $sheet->getHighestColumn();
                    $sheet->getStyle('1:1')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
                }
            },
        ];
    }
}
