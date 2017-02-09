<?php

namespace App\Http\ViewComposers;
use Illuminate\View\View;

class CommonComposer{

    public function compose(View $view)
    {   $navigation = [];
        $view->with('navigation', $navigation);
    }
}