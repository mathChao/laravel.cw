<nav>
    <ul id="topMenu">
        @foreach($navigation as $name => $item)
            <li class="{{isset($ttid) && $ttid == $item['ttid'] ? 'current' : ''}}">
                <a href="{{url($item['url'].($name !='首页' && $classid != 80 ? $classid : ''))}}">{{$name}}</a>
            </li>
        @endforeach
    </ul>
</nav>