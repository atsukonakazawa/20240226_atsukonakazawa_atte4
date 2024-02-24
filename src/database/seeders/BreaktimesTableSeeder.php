<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreaktimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => '1',
            'breakDate' => '2024-02-01',
            'breakIn' => '10:00:00',
            'created_at'=>'2024-02-01 10:00:00',
            'updated_at' =>'2024-02-01 10:00:00'
        ];
        DB::table('breaktimes')->insert($param);

        $param = [
            'user_id' => '1',
            'breakDate' => '2024-02-01',
            'breakOut' => '11:00:00',
            'created_at'=>'2024-02-01 11:00:00',
            'updated_at' =>'2024-02-01 11:00:00'
        ];
        DB::table('breaktimes')->insert($param);

        $param = [
            'user_id' => '1',
            'breakDate' => '2024-02-03',
            'breakIn' => '10:00:00',
            'created_at'=>'2024-02-03 10:00:00',
            'updated_at' =>'2024-02-03 10:00:00'
        ];
        DB::table('breaktimes')->insert($param);

        $param = [
            'user_id' => '1',
            'breakDate' => '2024-02-03',
            'breakOut' => '11:00:00',
            'created_at'=>'2024-02-03 11:00:00',
            'updated_at' =>'2024-02-03 11:00:00'
        ];
        DB::table('breaktimes')->insert($param);



    }
}
