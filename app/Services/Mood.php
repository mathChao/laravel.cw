<?php

namespace App\Services;

use App\Contracts\MoodInterface;
use App\BusinessModels\Article;
use DB;

class Mood implements MoodInterface{
    private $table;

    private function __construct()
    {
        $this->table = config('cwzg.edbPrefix').'ecmsextend_mood';
    }

    public function getMoodConfig(){
        return config('mood.setConfig');
    }

    public function getMoodData($moods){
        $height = config('mood.height');
        $maxMood = max($moods);
        $moodConfig = $this->getMoodConfig();
        $moodData = [];
        foreach($moodConfig as $mood => $name){
            if(isset($moods[$mood])){
                $moodHeight = $maxMood ? $height*($moods[$mood]/$maxMood) : 0;
                $moodData[$mood] = [
                    'name' => $name,
                    'height' =>$moodHeight
                ];
            }
        }
        return $moodData;
    }

    private function canArticleSetMood(Article $article){
        $result = [
            'success' => true,
            'message' => '',
        ];

        return $result;
    }

    public function addArticleMood(Article $article, $mood){
        $result = [
            'success' => false,
            'message' => '',
        ];

        $moodConfig = $this->getMoodConfig();
        if(!in_array($mood, array_keys($moodConfig))){
            $result['message'] = 'we does have mood '.$mood;
        }

        $canSetResult = $this->canArticleSetMood($article);
        if(!$canSetResult['success']){
            return $canSetResult;
        }

        $articleMood = $article->getArticleMood();
        $articleMood[$mood] += 1;
        $db = DB::table($this->table)->where('id', $article->id);
        if($db->count() > 0){
            DB::table($this->table)->where('id', $article->id)->update($articleMood);
        }else{
            $articleMood['id'] = $article->id;
            DB::table($this->table)->insert($articleMood);
        }

        $result['success'] = true;
        $result['message'] = '心情添加成功';
        $result['MoodData'] = $this->getMoodData($articleMood);
        return $result;
    }
}