@extends('layouts.content')

@section('user_css')
    <style>
        .article-content img {
            width: auto !important;
            height: auto !important;
        }
    </style>
@endsection

@section('content')
    <div class="p-content">
        <article class="detal">
            <h1>{{$article->title}}</h1>
            <span style="margin-right: 16px;">作者：{{$article->author}}</span>
            <time>{{date('Y-m-d H:m', $article->newstime)}}</time>
            <section class="js-article-content article-content">
                {!! $article->newstext !!}
            </section>
        </article>

        @if(count($related))
            <div class="mc" style="width: 100%;"><h2 style="width: 100%;text-align: left;">相关阅读</h2>
                <ul>
                    @foreach($related as $v)
                        <li>
                            <a href="{{url($v->url)}}">{{$v->title}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{--<div class="mood">--}}
        {{--<ul>--}}
            {{--@foreach($moodConfig as $key => $value)--}}
                {{--<label for="{{$key}}">--}}
                    {{--<li class="js-mood-list">--}}
                        {{--<span>{{$value['mood']}}</span>--}}
                        {{--<div class="js-pillar pillar{{$value['pillar']}}" style="height:{{$value['height']}}px;"></div>--}}
                        {{--<img src="{{url($value['img'])}}"><br>--}}
                        {{--{{$value['name']}}<br>--}}
                            {{--<input class="js-mood" type="radio" id="{{$key}}" name="mood" value="{{$key}}" data-id="{{$article->id}}">--}}
                    {{--</li>--}}
                {{--</label>--}}
            {{--@endforeach--}}
        {{--</ul>--}}
    {{--</div>--}}


    <!-- 多说评论框 start -->
    <div class="ds-thread" data-thread-key="{{$article->id}}" data-title="{{$article->title}}"
         data-url="{{url('/info/'.$article->id)}}"></div>
    <!-- 多说评论框 end -->
    <!-- 多说公共JS代码 start (一个网页只需插入一次) -->
    <script type="text/javascript">
        var duoshuoQuery = {short_name: "chawangzg"};
        (function () {
            var ds = document.createElement('script');
            ds.type = 'text/javascript';
            ds.async = true;
            ds.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//static.duoshuo.com/embed.js';
            ds.charset = 'UTF-8';
            (document.getElementsByTagName('head')[0]
            || document.getElementsByTagName('body')[0]).appendChild(ds);
        })();
    </script>
    <!-- 多说公共JS代码 end -->

@endsection