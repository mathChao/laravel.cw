@extends('layouts.content')

@section('content')
    <div class="p-content">
        <article class="detal" >
            <h1 >{{$article->title}}</h1 >
            <span style="margin-right: 16px;" >作者：{{$article->author}}</span >
            <time >{{date('Y-m-d H:m', $article->newstime)}}</time >
            <section class="js-article-content">
                {!! $article->newstext !!}
            </section >
        </article>

        @if(count($related))
        <div class="mc" style="width: 100%;" ><h2 style="width: 100%;text-align: left;" >相关阅读</h2 >
            <ul >
                @foreach($related as $v)
                <li >
                    <a href="{{url($v->url)}}" >{{$v->title}}</a >
                </li >
                @endforeach
            </ul >
        </div >
        @endif
    </div>
@endsection
