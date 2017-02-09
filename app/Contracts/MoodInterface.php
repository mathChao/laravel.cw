<?php

namespace App\Contracts;

use App\BusinessModels\Article;

interface MoodInterface{
    public function getMoodConfig();
    public function getMoodData($moods);
    public function addArticleMood(Article $article, $mood);
}