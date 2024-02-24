<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Day;
use App\Models\User;
use App\Models\Breaktime;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;


class WorkController extends Controller
{

    public function workIn(Request $request)
    {
        //同じ日に２回出勤ボタンを押せないようにする
        //その日、そのuserの出勤記録がworksテーブルにあるか探す
        $userSearch = Work::where('workDate',$request->workDate)
                    ->where('user_id',$request->user_id)
                    ->first();

        if($userSearch !== null)
        {
            //出勤記録がすでにある場合は、メッセージと共に打刻ページを再表示する
            session()->flash('message','今日は出勤打刻済みです!');
            return view('index');
        }else{
            //出勤記録がなければ、出勤Ok
            //viewから受け取った全データをworksテーブルに保存する
            $workIn = $request->all();
            Work::create($workIn);

            //二重送信対策
            $request->session()->regenerateToken();

            session()->flash('message','勤務を開始しました！');
            return view('index');
        }
    }

    public function workOut(Request $request)
    {
        //worksテーブルで、今回終了したい勤務のレコードを絞る
        $works = Work::where('user_id',$request->user_id)
            ->whereNull('workOut')
            ->get();

        foreach($works as $work)
        {
            //日を跨いだ場合、翌日の出勤操作に切り替えるために
            //勤務開始日と終了日が不一致だったら、一旦２３：５９：５９で勤務終了とする
            if($work->workDate !== $request->workDate)
            {
                $carbon1 = new Carbon($work->workIn);
                $carbon2 = new Carbon('23:59:59');
                $diff1 = $carbon2->diffInSeconds($carbon1);
                $results = [
                    'workOut' => $carbon2,
                    'wholeWorkTime' => new Carbon($diff1),
                ];
                Work::where('user_id',$request->user_id)
                        ->whereNull('workOut')
                        ->update($results);

                //日付を跨ぐ前の日の休憩時間合計sumBreaktimeを計算するため
                //対象のレコードを絞る
                $Work2 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$work->workDate)
                    ->whereNull('sumBreaktime')
                    ->get();

                //日付を跨ぐ前の日の該当userの休憩時間を合計する
                $wholeBreaktimes = Breaktime::whereDate('breakDate',$work->workDate)
                            ->where('user_id',$request->user_id)
                            ->select('user_id')
                            ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime')
                            ->groupBy('user_id')
                            ->get();

                foreach($wholeBreaktimes as $wholeBreaktime)
                {
                    //次の通り、sumBreaktimeカラムをupdateする
                    $sumBreaktime = $wholeBreaktime->totalBreaktime;
                    $results2 = [
                        'sumBreaktime' => $sumBreaktime,
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$work->workDate)
                            ->whereNull('sumBreakTime')
                            ->update($results2);
                }

                //次に、実働時間actualWorkTimeを計算したいレコードを絞る
                $works3 = Work::where('user_id',$request->user_id)
                ->whereNull('actualWorkTime')
                ->get();

                foreach($works3 as $work3)
                {
                    //日を跨ぐ前の日に休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                    if($work3->sumBreaktime == null)
                    {
                        $results3 = [
                            'actualWorkTime' => $work3->wholeWorkTime,
                        ];
                        Work::where('user_id',$request->user_id)
                                ->whereDate('workDate',$work3->workDate)
                                ->whereNull('actualWorkTime')
                                ->update($results3);
                    }else{
                        //休憩があった人は、wholeWorkTimeとsumBreaktimeの差=実働時間を計算する
                        $carbon3 = new Carbon($work3->sumBreaktime);
                        $carbon4 = new Carbon($work3->wholeWorkTime);
                        $diff2 = $carbon4->diffInSeconds($carbon3);

                        //以下の通り、actualWorkTimeカラムをupdateする
                        $results4 = [
                            'actualWorkTime' => new Carbon($diff2),
                        ];
                        Work::where('user_id',$request->user_id)
                                ->whereDate('workDate',$work3->workDate)
                                ->whereNull('actualWorkTime')
                                ->update($results4);
                    }
                }

                //そして翌日の出勤開始を００：００：００から退勤までとする
                $carbon5 = new Carbon('00:00:00');
                $carbon6 = new Carbon($request->workOut);
                $diff3 = $carbon6->diffInSeconds($carbon5);

                $results5 = [
                    'user_id' => $request->user_id,
                    'workDate' => $request->workDate,
                    'workIn' => $carbon5,
                    'workOut' => $request->workOut,
                    'wholeWorkTime' => new Carbon($diff3),
                ];
                Work::create($results5);

                //次に、日付を跨いだ後の日の休憩時間の合計sumBreaktimeを計算するため
                //対象のレコードを絞る
                $works4 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('sumBreaktime')
                    ->get();

                foreach($works4 as $work4)
                {
                    //該当日、該当userの休憩時間を合計する
                    $wholeBreaktimes2 = Breaktime::whereDate('breakDate',$request->workDate)
                                ->where('user_id',$request->user_id)
                                ->select('user_id')
                                ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime2')
                                ->groupBy('user_id')
                                ->get();

                    foreach($wholeBreaktimes2 as $wholeBreaktime2)
                    {
                        //次の通り、sumBreaktimeカラムをupdateする
                        $sumBreaktime2 = $wholeBreaktime2->totalBreaktime2;
                        $results6 = [
                            'sumBreaktime' => $sumBreaktime2,
                        ];
                        Work::where('user_id',$request->user_id)
                                ->whereDate('workDate',$request->workDate)
                                ->whereNull('sumBreakTime')
                                ->update($results6);
                    }

                    //次に、実働時間actualWorkTimeを計算したいレコードを絞る
                    $works5 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('actualWorkTime')
                    ->get();

                    foreach($works5 as $work5)
                    {
                        //休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                        if($work5->sumBreaktime == null)
                        {
                            $results7 = [
                                'actualWorkTime' => $work5->wholeWorkTime,
                            ];
                            Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($results7);
                        }else{
                            //休憩があった人は、wholeWorkTimeとsumBreaktimeの差=実働時間を計算する
                            $carbon7 = new Carbon($work5->sumBreaktime);
                            $carbon8 = new Carbon($work5->wholeWorkTime);
                            $diff4 = $carbon8->diffInSeconds($carbon7);

                            //以下の通り、actualWorkTimeカラムをupdateする
                            $results8 = [
                                'actualWorkTime' => new Carbon($diff4),
                            ];
                            Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($results8);
                        }
                    }
                }


            }else{
                //絞ったレコードの勤務時間（休憩含む）wholeWorkTimeを計算する
                $carbon9 = new Carbon($work5->workIn);
                $carbon10 = new Carbon($request->workOut);
                $diff5 = $carbon10->diffInSeconds($carbon9);

                //次の通り、workOutとwholeWorkTimeカラムをupdateする
                $results9 = [
                    'workOut' => $request->workOut,
                    'wholeWorkTime' => new Carbon($diff5),
                ];
                Work::where('user_id',$request->user_id)
                        ->whereDate('workDate',$request->workDate)
                        ->whereNull('workOut')
                        ->update($results9);

                //worksテーブルで上記のカラムをupdate後、
                //休憩時間の合計sumBreaktimeを計算したいレコードを絞る
                $works6 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('sumBreaktime')
                    ->get();

                foreach($works6 as $work6)
                {
                    //該当日、該当userの休憩時間を合計する
                    $wholeBreaktimes3 = Breaktime::whereDate('breakDate',$request->workDate)
                                ->where('user_id',$request->user_id)
                                ->select('user_id')
                                ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime3')
                                ->groupBy('user_id')
                                ->get();

                    foreach($wholeBreaktimes3 as $wholeBreaktime3)
                    {
                        //次の通り、sumBreaktimeカラムをupdateする
                        $sumBreaktime3 = $wholeBreaktime3->totalBreaktime3;
                        $results10 = [
                            'sumBreaktime' => $sumBreaktime3,
                        ];
                        Work::where('user_id',$request->user_id)
                                ->whereDate('workDate',$request->workDate)
                                ->whereNull('sumBreakTime')
                                ->update($results10);
                    }

                    //worksテーブルで、上記のカラムをupdate後、
                    //実働時間actualWorkTimeを計算したいレコードを絞る
                    $works7 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('actualWorkTime')
                    ->get();

                    foreach($works7 as $work7)
                    {
                        //休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                        if($work7->sumBreaktime == null)
                        {
                            $result11 = [
                                'actualWorkTime' => $work7->wholeWorkTime,
                            ];
                            Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result11);
                        }else{
                            //休憩があった人は、wholeWorkTimeとsumBreaktimeの差=実働時間を計算する
                            $carbon11 = new Carbon($work7->sumBreaktime);
                            $carbon12 = new Carbon($work7->wholeWorkTime);
                            $diff6 = $carbon12->diffInSeconds($carbon11);

                            //以下の通り、actualWorkTimeカラムをupdateする
                            $result12 = [
                                'actualWorkTime' => new Carbon($diff6),
                            ];
                            Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result12);
                        }
                    }
                }
            }
        }


        //worksテーブルで上記のカラムをupdate後、
        //休憩時間の合計sumBreaktimeを計算したいレコードを絞る
        $works8 = Work::where('user_id',$request->user_id)
            ->whereDate('workDate',$request->workDate)
            ->whereNull('sumBreaktime')
            ->get();

        foreach($works8 as $work8)
        {
            //該当日、該当userの休憩時間を合計する
            $wholeBreaktimes4 = Breaktime::whereDate('breakDate',$request->workDate)
                        ->where('user_id',$request->user_id)
                        ->select('user_id')
                        ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime4')
                        ->groupBy('user_id')
                        ->get();

            foreach($wholeBreaktimes4 as $wholeBreaktime4)
            {
                //次の通り、sumBreaktimeカラムをupdateする
                $sumBreaktime4 = $wholeBreaktime4->totalBreaktime4;
                $result13 = [
                    'sumBreaktime' => $sumBreaktime4,
                ];
                Work::where('user_id',$request->user_id)
                        ->whereDate('workDate',$request->workDate)
                        ->whereNull('sumBreakTime')
                        ->update($result13);
            }

            //worksテーブルで、上記のカラムをupdate後、
            //実働時間actualWorkTimeを計算したいレコードを絞る
            $works9 = Work::where('user_id',$request->user_id)
            ->whereDate('workDate',$request->workDate)
            ->whereNull('actualWorkTime')
            ->get();

            foreach($works9 as $work9)
            {
                //休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                if($work9->sumBreaktime == null)
                {
                    $result14 = [
                        'actualWorkTime' => $work9->wholeWorkTime,
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$request->workDate)
                            ->whereNull('actualWorkTime')
                            ->update($result14);
                }else{
                    //休憩があった人は、wholeWorkTimeとsumBreaktimeの差=実働時間を計算する
                    $carbon13 = new Carbon($work9->sumBreaktime);
                    $carbon14 = new Carbon($work9->wholeWorkTime);
                    $diff7 = $carbon14->diffInSeconds($carbon13);

                    //以下の通り、actualWorkTimeカラムをupdateする
                    $result15 = [
                        'actualWorkTime' => new Carbon($diff7),
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$request->workDate)
                            ->whereNull('actualWorkTime')
                            ->update($result15);
                }
            }
        }

        //二重送信対策
        $request->session()->regenerateToken();

        //メッセージと共に打刻ページを再度表示する
        session()->flash('message','勤務を終了しました！');
        return view('index');
    }

    public function calendar()
    {
        //カレンダーのページを表示する
        return view('calendar');
    }


    public function sendDay(Request $request)
    {
        //calendarページで指定された日付を受け取る
        $date = $request->send_day;

        //worksテーブルから指定された日付のレコードを５件ずつ表示する
        $workItems = Work::with('breaktime')
                ->whereDate('workDate',$date)
                ->paginate(5);

        //日付・レコードの各内容と共に日付別一覧ページを表示する
        return view('attendance', compact('date','workItems'));
    }


}

