<nav>
    <ul id="topMenu">
        @foreach($navigation as $name => $item)
            <li class="{{isset($ttid) && $ttid == $item['ttid'] ? 'current' : ''}}">
                <a href="{{url($item['url'])}}">{{$name}}</a>
            </li>
        @endforeach
    </ul>
</nav>