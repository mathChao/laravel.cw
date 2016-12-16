<nav>
    <ul id="topMenu">
        @foreach($navigation as $name => $item)
            <li class="{{isset($ttid) && $ttid == $item['ttid'] ? 'current' : ''}}">
                <a href="{{url($item['url'].($classid != 80 ? $classid : ''))}}">{{$name}}</a>
            </li>
        @endforeach
    </ul>
</nav>