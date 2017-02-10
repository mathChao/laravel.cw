<?php

return [
    'edbPrefix'=>'cwcms_',
    'pdbPrefix' => 'cw_',
    'imageUrl' => 'http://www.cwzg.cn',
    'sitename' => '察网中国触屏版',
    'keywords' => '察网，新闻网站，民营，爱国网站，人物资料',
    'description' => '“察网中国”是由海南察网文化传媒有限公司运营的民间性质爱国网民网站。',
    'copyright' => 'Copyright © 2016 察网中国',
    'newsTextPageLength'=>2000,
    'classMap' => [
            '2' => [
                'name_en' => 'politics',
                'url' => '/politics/'
            ],
            '3' => [
                'name_en' => 'expose',
                'url' => '/expose/'
            ],
            '4' => [
                'name_en' => 'theory',
                'url' => '/theory/'
            ],
            '5' => [
                'name_en' => 'history',
                'url' => '/history/'
            ],
            '6' => [
                'name_en' => 'photos',
                'url' => '/photos/'
            ]
    ],
    'navigation' => [
        '首页' => [
            'key' => 'index',
            'url' => '/'
        ],

        '头条' => [
            'key' => 'headline',
            'url' => '/headline/',
        ],

        '智库' => [
            'key' => 'thinktank',
            'url' => '/thinktank/',
        ],

        '时评' => [
            'key' => 'opinion',
            'url' => '/opinion/',
        ],

        '深度' => [
            'key' => 'view',
            'url' => '/view/',
        ],

        '争鸣' => [
            'key' => 'debate',
            'url' => '/debate/'
        ],
    ],
    'mood' => [
        'mood1'=>[
            'name'=>'震惊',
            'img'=>'/image/a1.gif',
            'pillar'=>'2',
        ],
        'mood2'=>[
            'name'=>'不解',
            'img'=>'/image/a2.gif',
            'pillar'=>'2',
        ],
        'mood3'=>[
            'name'=>'愤怒',
            'img'=>'/image/a3.gif',
            'pillar'=>'2',
        ],
        'mood4'=>[
            'name'=>'高兴',
            'img'=>'/image/a6.gif',
            'pillar'=>'1',
        ],
        'mood5'=>[
            'name'=>'支持',
            'img'=>'/image/a7.gif',
            'pillar'=>'1',
        ],
        'mood6'=>[
            'name'=>'超赞',
            'img'=>'/image/a8.gif',
            'pillar'=>'1',
        ],
    ],
];
