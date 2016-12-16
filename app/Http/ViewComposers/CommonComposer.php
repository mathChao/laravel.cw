<?php

namespace App\Http\ViewComposers;
use Illuminate\View\View;

class CommonComposer{

    public function compose(View $view)
    {
        $navigation = [
            '首页' => [
                'ttid' => 12,
                'url' => '/'
            ],

            '头条' => [
                'ttid' => 34,
                'url' => '/list/34/',
            ],

            '智库' => [
                'ttid' => 4,
                'url' => '/list/4/',
            ],

            '时评' => [
                'ttid' => 3,
                'url' => '/list/3/',
            ],

            '深度' => [
                'ttid' => 2,
                'url' => '/list/2/',
            ],

            '争鸣' => [
                'ttid' => 5,
                'url' => '/list/5/'
            ],
        ];


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