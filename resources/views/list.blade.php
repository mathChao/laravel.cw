@extends('layouts.list')

@section('content')
    <div class="p-list">
        <article class="article" >
            <section class="column js-article-list-wrap">
                <h2><a href="3-article.html" title="苏联崩塌的第一块版图：立陶宛;一个“教授”的作用" >苏联崩塌的第一块版图：立陶宛;一个“教授”的作用</a></h2>
                @foreach($articles as $article)
                    <section>
                        <a href="{{url($article->url)}}">
                            <h3 >{{$article->title}}</h3>

                            <div class="img-txt" >
                                <img src="{{$article->prefixImgTitlepic}}" >
                                <p >{{$article->smalltext}}</p >
                            </div >
                        </a>
                    </section>
                @endforeach
            </section>
            <div >
                <div class="moreFoot">
                    <a class="js-article-list-load ffsong" data-ttid="{{$ttid}}" data-classid="{{$classid}}" data-end="0" id="ffsong" href="javascript:void(0)" >加载更多 &gt;&gt;</a >
                </div >
            </div >
        </article >
    </div>
@endsection
