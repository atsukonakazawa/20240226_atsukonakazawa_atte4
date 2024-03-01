<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\User;
use App\Models\Breaktime;
use App\Models\Month;
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
            session()->flash('message','今日はすでに出勤済みです！');
            return view('index');
        }else{
            //出勤記録がなければ、出勤Ok
            //viewから受け取った全データをworksテーブルに保存する
            $workIn = $request->all();
            //$workIns = [
            //    'user_id' => $request->user_id,
            //    'workDate' => $request->workDate,
            //    'yearMonth' => $request->yearMonth,
            //    'workIn' => $request->workIn,
            //];
            Work::create($workIn);

            //二重送信対策
            //$request->session()->regenerateToken();

            //ボタン無効化に必要なflashをsessionに格納する
            session()->flash('disableWorkIn' , true);
            //メッセージと共に打刻ページを再度表示する
            session()->flash('message','勤務を開始しました！');
            return view('index');
        }
    }

    public function workOut(Request $request)
    {
        //出勤前に押せないようにする
        //worksテーブルで、その日、そのuserの出勤記録があるか探す
        $workSearch = Work::where('user_id',$request->user_id)
                    ->whereNull('workOut')
                    ->first();

        //休憩中に押せないようにする
        //breaktimesテーブルで、休憩終了の押し忘れがないか確認する
        $breakSearch = Breaktime::where('breakDate',$request->workDate)
                    ->where('user_id',$request->user_id)
                    ->whereNotNull('breakIn')
                    ->whereNull('breakOut')
                    ->first();

        if($workSearch == null)
        {
            //【エラー１】出勤記録がない場合、以下のメッセージを表示する
            session()->flash('message','出勤開始ボタンを先に押してください！');
            return view('index');

        }elseif($breakSearch !== null)
        {   //【エラー２】休憩中の場合、以下のメッセージを表示する
            session()->flash('message','休憩終了ボタンを先に押してください！');
            return view('index');

        }else{
            //【エラーなし】通常通りの処理をする
            //worksテーブルで、終了したい勤務のレコードを絞る
            $work = Work::where('user_id',$request->user_id)
                    ->whereNull('workOut')
                    ->first();
            $workDate1 = $work->workDate;
            $workDate2 = $request->workDate;

            //絞ったレコード$workのworkDateが今日なら日を跨いでいない為、
            //出勤した日に退勤する処理をする
            if($workDate1 == $workDate2)
            {
                //workOutと拘束時間wholeWorkTimeをupdateする
                $carbon1 = new Carbon($work->workIn);
                $carbon2 = new Carbon($request->workOut);
                $diff1 = $carbon2->diffInSeconds($carbon1);
                $result = [
                    'workOut' => $request->workOut,
                    'wholeWorkTime' => new Carbon($diff1),
                ];
                Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$request->workDate)
                            ->whereNull('workOut')
                            ->update($result);

                //休憩時間の合計sumBreaktimeを計算する
                $sumBreaks = Breaktime::whereDate('breakDate',$request->workDate)
                        ->where('user_id',$request->user_id)
                        ->select('user_id')
                        ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime')
                        ->groupBy('user_id')
                        ->get();

                foreach($sumBreaks as $sumBreak)
                {
                    //次の通り、sumBreaktimeカラムをupdateする
                    $result1 = [
                        'sumBreaktime' => $sumBreak->totalBreaktime,
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$request->workDate)
                            ->whereNull('sumBreakTime')
                            ->update($result1);
                }

                //実働時間actualWorkTimeを計算する
                //計算したいレコードを絞る
                $works1 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('actualWorkTime')
                    ->get();

                foreach($works1 as $work1)
                {
                    //休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                    if($work1->sumBreaktime == null)
                    {
                        $result2 = [
                            'actualWorkTime' => $work1->wholeWorkTime,
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result2);
                    }else{
                        //休憩があった場合は、wholeWorkTimeとsumBreaktimeの差である
                        //実働時間actualWorkTimeを計算する
                        $carbon3 = new Carbon($work1->sumBreaktime);
                        $carbon4 = new Carbon($work1->wholeWorkTime);
                        $diff3 = $carbon4->diffInSeconds($carbon3);

                        //以下の通り、actualWorkTimeカラムをupdateする
                        $result3 = [
                            'actualWorkTime' => new Carbon($diff3),
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result3);
                    }
                }

            }else{
                //絞ったレコード$workのworkDateが昨日なら
                //日を跨いでいる為、
                //昨日のworkOutを23:59:59にする
                $carbon5 = new Carbon($work->workIn);
                $carbon6 = new Carbon('23:59:59');
                $diff5 = $carbon6->diffInSeconds($carbon5);

                //昨日のworkOutとwholeWorkTimeをupdateする
                $result5 = [
                    'workOut' => $carbon6,
                    'wholeWorkTime' => new Carbon($diff5),
                ];
                Work::where('user_id',$request->user_id)
                            ->whereNull('workOut')
                            ->update($result5);

                //昨日の休憩時間の合計sumBreaktimeを計算する
                $today = new Carbon($request->workDate);
                $subDay = $today->subDay();
                $sumBreaks2 = Breaktime::where('user_id',$request->user_id)
                        ->whereDate('breakDate',$subDay)
                        ->select('user_id')
                        ->selectRaw('SUM(wholeBreaktime) AS total_Breaktime')
                        ->groupBy('user_id')
                        ->get();
                foreach($sumBreaks2 as $sumBreak2)
                {
                    //昨日のsumBreaktimeカラムをupdateする
                    $result6 = [
                        'sumBreaktime' => $sumBreak2->total_Breaktime,
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$subDay)
                            ->whereNull('sumBreakTime')
                            ->update($result6);
                }

                //昨日の実働時間actualWorkTimeを計算する
                //計算したいレコードを絞る
                $works2 = Work::where('user_id',$request->user_id)
                    ->whereNull('actualWorkTime')
                    ->get();

                foreach($works2 as $work2)
                {
                    //昨日の休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                    if($work2->sumBreaktime == null)
                    {
                        $result7 = [
                            'actualWorkTime' => $work2->wholeWorkTime,
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereNull('actualWorkTime')
                                    ->update($result7);
                    }else{
                        //昨日の休憩があった人は、wholeWorkTimeとsumBreaktimeの差、
                        //実働時間actualWorkTimeを計算する
                        $carbon9 = new Carbon($work2->sumBreaktime);
                        $carbon10 = new Carbon($work2->wholeWorkTime);
                        $diff9 = $carbon10->diffInSeconds($carbon9);

                        //昨日のactualWorkTimeカラムをupdateする
                        $result9 = [
                            'actualWorkTime' => new Carbon($diff9),
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereNull('actualWorkTime')
                                    ->update($result9);
                    }
                }

                //今日のworkInを00:00:00にする
                $carbon11 = new Carbon('00:00:00');
                $carbon12 = new Carbon($request->workOut);
                $diff11 = $carbon12->diffInSeconds($carbon11);

                //以下の通り、5つのカラムをcreateする
                $result11 = [
                    'user_id' => $request->user_id,
                    'workDate' => $request->workDate,
                    'workIn' => $carbon11,
                    'workOut' => $request->workOut,
                    'wholeWorkTime' => new Carbon($diff11),
                ];
                Work::create($result11);

                //今日の休憩時間の合計sumBreaktimeを計算する
                $sumBreaks3 = Breaktime::whereDate('breakDate',$request->workDate)
                        ->where('user_id',$request->user_id)
                        ->select('user_id')
                        ->selectRaw('SUM(wholeBreaktime) AS totalBreaktime3')
                        ->groupBy('user_id')
                        ->get();

                foreach($sumBreaks3 as $sumBreak3)
                {
                    //次の通り、sumBreaktimeカラムをupdateする
                    $result12 = [
                        'sumBreaktime' => $sumBreak3->totalBreaktime3,
                    ];
                    Work::where('user_id',$request->user_id)
                            ->whereDate('workDate',$request->workDate)
                            ->whereNull('sumBreakTime')
                            ->update($result12);
                }

                //今日の実働時間actualWorkTimeを計算する
                //計算したいレコードを絞る
                $works4 = Work::where('user_id',$request->user_id)
                    ->whereDate('workDate',$request->workDate)
                    ->whereNull('actualWorkTime')
                    ->get();

                foreach($works4 as $work4)
                {
                    //今日休憩がなかった場合はactualWorkTimeにwholeWorkTimeをそのまま入れる
                    if($work4->sumBreaktime == null)
                    {
                        $result13 = [
                            'actualWorkTime' => $work4->wholeWorkTime,
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result13);
                    }else{
                        //今日休憩があった人は、wholeWorkTimeとsumBreaktimeの差
                        //実働時間actualWorkTimeを計算する
                        $carbon15 = new Carbon($work4->sumBreaktime);
                        $carbon16 = new Carbon($work4->wholeWorkTime);
                        $diff15 = $carbon16->diffInSeconds($carbon15);

                        //以下の通り、actualWorkTimeカラムをupdateする
                        $result15 = [
                            'actualWorkTime' => new Carbon($diff15),
                        ];
                        Work::where('user_id',$request->user_id)
                                    ->whereDate('workDate',$request->workDate)
                                    ->whereNull('actualWorkTime')
                                    ->update($result15);
                    }
                }
            }

            //二重送信対策
            //$request->session()->regenerateToken();

            //ボタン無効化に必要なflashをsessionに格納する
            session()->flash('disableWorkOut' , true);
            //メッセージと共に打刻ページを再度表示する
            session()->flash('message','勤務を終了しました！');
            return view('index');
        }
    }

    public function sendDay(Request $request)
    {
        //今日の日付を取得
        $date = $request->send_day;

        //worksテーブルとbreaktimesテーブルから今日の勤務記録を取得
        $workItems = Work::with('breaktime')
                ->whereDate('workDate',$date)
                ->paginate(5);

        return view('attendance',compact('date','workItems'));
    }

    public function dayBefore(Request $request)
    {
        $dayBefore = new Carbon($request->date);
        $subDay = $dayBefore->subDay()->format('Y-m-d');
        $date = $subDay;

        //worksテーブルとbreaktimesテーブルから今日の勤務記録を取得
        $workItems = Work::with('breaktime')
                ->whereDate('workDate',$date)
                ->paginate(5);

        return view('attendance',compact('date','workItems'));
    }

    public function nextDay(Request $request)
    {
        $nextDay = new Carbon($request->date);
        $addDay = $nextDay->addDay()->format('Y-m-d');
        $date = $addDay;

        //worksテーブルとbreaktimesテーブルから今日の勤務記録を取得
        $workItems = Work::with('breaktime')
                ->whereDate('workDate',$date)
                ->paginate(5);

        return view('attendance',compact('date','workItems'));
    }

    public function userlist(Request $request)
    {
        //usersテーブルの全データを取得する
        $users = User::paginate(5);

        return view('userlist',compact('users'));
    }

    public function sendMonth(Request $request)
    {
        //今日の年月を取得する
        $month = $request->send_month;
        //Carbonで年月の表示にする
        $Carbon = new Carbon($month);
        $YearMonth = $Carbon->format("Y-m");

        //該当userの名前をusersテーブルから取得する
        $eachUserId = $request->eachUserId;
        $eachUsers = User::where('id',$eachUserId)
                    ->paginate(4);

        //該当月の勤務情報がworksテーブルにあるか確認する
        $nullOrNot = Work::where('user_id',$eachUserId)
                    ->whereDate('yearMonth',$month)
                    ->first();

        if($nullOrNot !== null)
        {
            //該当月の勤務情報をworksテーブルから取得する
            $eachUserWorks = Work::where('user_id',$eachUserId)
                        ->whereDate('yearMonth',$month)
                        ->paginate(4);

            //１ヶ月の実働時間合計を出す
            $monthlys = Work::where('user_id',$eachUserId)
                        ->where('yearMonth',$month)
                        ->select('user_id')
                        ->selectRaw('SUM(actualWorkTime) AS totalmonthly')
                        ->groupBy('user_id')
                        ->get();

            foreach($monthlys as $monthly)
            {
                //１ヶ月の実働時間合計をmonthlyカラムにupdateする
                $resultM = [
                    'monthly' => $monthly->totalmonthly,
                ];
                Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$month)
                    ->whereNull('monthly')
                    ->update($resultM);

                //updateされたworksテーブルで
                //該当user、該当月のmonthlyカラムから最大値を取得する
                $maxValue = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$month)
                    ->max('monthly');
                $maxRecord = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$month)
                    ->where('monthly',$maxValue)
                    ->first();
            }
                return view('eachattendance',compact('YearMonth','eachUsers','eachUserWorks','maxRecord'));

        }else{
            //もしこのuserの勤務情報がなかったら、nothingページへ
            return view('nothing');
        }
    }

    public function monthBefore(Request $request)
    {
        //今月の年月から１ヶ月前を算出する
        $month = new Carbon($request->YearMonth);
        $subMonth = $month->subMonth();
        $YearMonth = $subMonth->format('Y-m');


        //該当userの名前をusersテーブルから取得する
        $eachUserId = $request->eachUserId;
        $eachUsers = User::where('id',$eachUserId)
                    ->paginate(5);

        //該当月の勤務情報がworksテーブルにあるか確認する
        $nullOrNot = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$subMonth)
                    ->first();

        if($nullOrNot !== null)
        {
            //該当月の勤務情報をworksテーブルから取得する
            $eachUserWorks = Work::where('user_id',$eachUserId)
                        ->where('yearMonth',$subMonth)
                        ->paginate(5);

            //１ヶ月の実働時間合計を出す
            $monthlys = Work::where('user_id',$eachUserId)
                        ->where('yearMonth',$subMonth)
                        ->select('user_id')
                        ->selectRaw('SUM(actualWorkTime) AS totalmonthly')
                        ->groupBy('user_id')
                        ->get();

            foreach($monthlys as $monthly)
            {
                //１ヶ月の実働時間合計をmonthlyカラムにupdateする
                $resultM = [
                    'monthly' => $monthly->totalmonthly,
                ];
                Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$submonth)
                    ->whereNull('monthly')
                    ->update($resultM);

                //updateされたworksテーブルで
                //該当user、該当月のmonthlyカラムから最大値を取得する
                $maxValue = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$month)
                    ->max('monthly');
                $maxRecord = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$submonth)
                    ->where('monthly',$maxValue)
                    ->first();
            }
                return view('eachattendance',compact('YearMonth','eachUsers','eachUserWorks','maxRecord'));

        }else{

            //もしこのuserの勤務情報がなかったら、nothingページへ
            return view('nothing');
        }
    }

    public function nextMonth(Request $request)
    {
        //今月の年月から１ヶ月前を算出する
        $month = new Carbon($request->YearMonth);
        $addMonth = $month->addMonth();
        $YearMonth = $addMonth->format('Y-m');


        //該当userの名前をusersテーブルから取得する
        $eachUserId = $request->eachUserId;
        $eachUsers = User::where('id',$eachUserId)
                    ->paginate(5);

        //該当月の勤務情報をworksテーブルから取得する
        $nullOrNot = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$addMonth)
                    ->first();

        if($nullOrNot !== null)
        {
            //該当月の勤務情報をworksテーブルから取得する
            $eachUserWorks = Work::where('user_id',$eachUserId)
                        ->where('yearMonth',$addMonth)
                        ->paginate(5);

            //１ヶ月の実働時間合計を出す
            $monthlys = Work::where('user_id',$eachUserId)
                        ->where('yearMonth',$addMonth)
                        ->select('user_id')
                        ->selectRaw('SUM(actualWorkTime) AS totalmonthly')
                        ->groupBy('user_id')
                        ->get();

            foreach($monthlys as $monthly)
            {
                //１ヶ月の実働時間合計をmonthlyカラムにupdateする
                $resultM = [
                    'monthly' => $monthly->totalmonthly,
                ];
                Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$addmonth)
                    ->whereNull('monthly')
                    ->update($resultM);

                //updateされたworksテーブルで
                //該当user、該当月のmonthlyカラムから最大値を取得する
                $maxValue = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$month)
                    ->max('monthly');
                $maxRecord = Work::where('user_id',$eachUserId)
                    ->where('yearMonth',$addmonth)
                    ->where('monthly',$maxValue)
                    ->first();
            }
                return view('eachattendance',compact('YearMonth','eachUsers','eachUserWorks','maxRecord'));

        }else{
            //もしこのuserの勤務情報がなかったら、nothingページへ
            return view('nothing');
        }
    }

}