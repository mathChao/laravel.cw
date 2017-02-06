<nav>
    <ul id="topMenu">
        @foreach($navigation as $name => $item)
            <li class="{{(isset($key) && $key == $item['key']) ? 'current' : ''}}">
                <a href="{{url($item['url'])}}">{{$name}}</a>
            </li>
        @endforeach
    </ul>
</nav>