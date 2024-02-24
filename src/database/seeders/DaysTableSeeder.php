<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'days' => '2024-02-01'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-02'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-03'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-04'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-05'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-06'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-07'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-08'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-09'
        ];
        DB::table('days')->insert($param);
        $param = [
            'days' => '2024-02-10'
        ];
        DB::table('days')->insert($param);

    }
}
