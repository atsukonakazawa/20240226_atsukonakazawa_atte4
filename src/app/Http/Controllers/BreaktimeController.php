<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Breaktime;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BreaktimeController extends Controller
{

    public function breakIn(Request $request)
    {
        //出勤開始前に押せないようにする
        //その日、そのuserの出勤記録がworksテーブルにあるか探す
        $userWorkSearch = Work::where('user_id',$request->user_id)
                    ->whereNull('workOut')
                    ->first();

        if($userWorkSearch == null)
        {
            //出勤記録がない場合は、メッセージと共に打刻ページを再表示する
            session()->flash('message','出勤開始ボタンを先に押してください！');
            return view('index');

        }else{
            //worksテーブルの中で、今回の休憩と紐づけたいレコードを絞る
            $breaktimes = breaktime::with('work')->get();
            $work = Work::where('user_id',$request->user_id)
                    ->whereNull('workOut')
                    ->first();

            //次の通り、user_id〜breakInカラムをcreateする
            $breakIn = [
            'user_id' => $request->user_id,
            'work_id' => $work->id,
            'breakDate' => $request->breakDate,
            'breakIn' => $request->breakIn,
            ];
            Breaktime::create($breakIn);

            //二重送信対策
            //$request->session()->regenerateToken();

            //ボタン無効化に必要なflashをsessionに格納する
            session()->flash('disableBreakIn' , true);
            //メッセージと共に打刻ページを再度表示する
            session()->flash('message','休憩を開始しました！');
            return view('index');
        }
    }

    public function breakOut(Request $request)
    {
        //breaktimesテーブルの中で、終了したい休憩のレコードを絞る
        $breaktime = Breaktime::where('user_id',$request->user_id)
                ->whereNull('breakOut')
                ->first();
        $breakDate1 = $breaktime->breakDate;
        $breakDate2 = $request->breakDate;

        //絞ったレコードのbreakDateが今日なら
        //日を跨がないので
        //絞ったレコードにbreakOutをupdateするだけ
        if($breakDate1 == $breakDate2)
        {
            //breakOutをupdateする
            $result = [
                'breakOut' => $request->breakOut,
            ];
            Breaktime::where('user_id',$request->user_id)
                        ->whereDate('breakDate',$request->breakDate)
                        ->whereNull('breakOut')
                        ->update($result);
        }else{
            //絞ったレコードのbreakDateが昨日なら
            //休憩が日を跨いでいるので
            //昨日のbreakOutを23:59:59にする
            $carbon1 = new Carbon('23:59:59');
            $result1 = [
                'breakOut' => $carbon1,
            ];
            Breaktime::where('user_id',$request->user_id)
                        ->whereNull('breakOut')
                        ->update($result1);

            //昨日の23:59:59までの休憩時間を計算する
            $breakBefore = Breaktime::where('user_id',$request->user_id)
                        ->whereNull('wholeBreaktime')
                        //->where('breakOut','23:59:59')
                        ->first();
            $carbon5 = new Carbon($breakBefore->breakIn);
            $diffInSeconds2 = $carbon1->diffInSeconds($carbon5);

            //次の通り、wholeBreaktimeカラムをupdateする
            $result4 = [
                'wholeBreaktime' => new Carbon($diffInSeconds2),
            ];
            Breaktime::where('user_id',$request->user_id)
                        ->whereNull('wholeBreaktime')
                        //->where('breakOut','23:59:59')
                        ->update($result4);

            //そして翌日のbreakInを00:00:00にする
            $carbon2 = new Carbon('00:00:00');
            $result2 = [
                'user_id' => $request->user_id,
                'work_id' => $breaktime->work_id,
                'breakDate' => $request->breakDate,
                'breakIn' => $carbon2,
                'breakOut' => $request->breakOut,
            ];
            Breaktime::create($result2);
        }

        //絞った各レコードの休憩時間を秒で計算する
        $breaktimes = Breaktime::where('user_id',$request->user_id)
                    ->whereDate('breakDate',$request->breakDate)
                    ->whereNull('wholeBreaktime')
                    ->get();
        foreach($breaktimes as $breaktime)
        {
            $carbon3 = new Carbon($breaktime->breakIn);
            $carbon4 = new Carbon($request->breakOut);
            $diffInSeconds = $carbon4->diffInSeconds($carbon3);

        //次の通り、wholeBreaktimeカラムをupdateする
            $result3 = [
                'wholeBreaktime' => new Carbon($diffInSeconds),
            ];
            Breaktime::where('user_id',$request->user_id)
                    ->whereDate('breakDate',$request->breakDate)
                    ->whereNull('wholeBreaktime')
                    ->update($result3);
        }

        //二重送信対策
        //$request->session()->regenerateToken();

        //ボタン無効化に必要なflashをsessionに格納する
        session()->flash('disableBreakOut' , true);
        //メッセージと共に打刻ページを再度表示する
        session()->flash('message','休憩を終了しました！');
        return view('index');
    }

}
