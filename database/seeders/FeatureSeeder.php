<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('features')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /** Do not change key for all features. if keys are changed need to update api. */
        $features = [

            [
                'group' => 'Office Desk',
                'name' => 'Project Management',
                'key' => Str::slug('Project Management'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'group' => 'Office Desk',
                'name' => 'Meeting',
                'key' => Str::slug('Meeting'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'group' => 'Finance',
                'name' => 'TADA',
                'key' => Str::slug('TADA'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'group' => 'Finance',
                'name' => 'Payroll Management',
                'key' => Str::slug('Payroll Management'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'group' => 'Finance',
                'name' => 'Advance Salary',
                'key' => Str::slug('Advance Salary'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'group' => 'Additional',
                'name' => 'Support',
                'key' => Str::slug('Support'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'group' => 'Additional',
                'name' => 'Dark Mode',
                'key' => Str::slug('Dark Mode'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'group' => 'Attendance',
                'name' => 'NFC & QR',
                'key' => Str::slug('NFC & QR'),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('features')->insert($features);
    }
}
