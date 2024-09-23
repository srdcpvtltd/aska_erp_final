<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existingRows = DB::table('projectts')->get();

        foreach ($existingRows as $row) {
            DB::table('projectts')
                ->where('id', $row->id)
                ->update(['slug' => \Illuminate\Support\Str::uuid()]);
        }

    }
}
