<?php

namespace App\Http\ViewComposers;
use Illuminate\View\View;

class CommonComposer{

    public function compose(View $view)
    {
        $navigation = config('cwzg.navigation');
        
        $site = [
            'name' => config('cwzg.sitename'),
            'keywords' => config('cwzg.keywords'),
            'description' => config('cwzg.description'),
            'copyright' => config('cwzg.copyright'),
        ];

        $week = [
            '星期天',
            '星期一',
            '星期二',
            '星期三',
            '星期四',
            '星期五',
            '星期六',
        ];
        $date = date('Y年m月d日 ').$week[date('w')];

        $view->with('navigation', $navigation);
        $view->with('site', $site);
        $view->with('date', $date);
    }
}