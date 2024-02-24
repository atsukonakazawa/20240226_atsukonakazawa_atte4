<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => '6',
            'workDate' => '2024-02-15',
            'workIn' => '9:00:00',
        ];
        DB::table('works')->insert($param);

        $param = [
            'user_id' => '8',
            'workDate' => '2024-02-15',
            'workIn' => '9:00:00',
        ];
        DB::table('works')->insert($param);

    }
}
