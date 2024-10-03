<?php

namespace App\Exports;

use App\Models\Village;
use Maatwebsite\Excel\Concerns\FromCollection;

class VillagesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Village::all();
    }
}
