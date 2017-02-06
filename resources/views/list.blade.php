@extends('layouts.list')

@section('content')
    <div class="p-list">
        <article class="article">
            <section class="column js-article-list-wrap">
                @if(isset($topArticle) && $topArticle)
                    <h2>
                        <a href="{{url($topArticle->url)}}" title="{{$topArticle->title}}">{{$topArticle->title}}</a>
                    </h2>
                @endif
                @foreach($articles as $article)
                    @if(!$article->isEmpty())
                        <section>
                            <a href="{{url($article->url)}}">
                                <h3>{{$article->title}}</h3>

                                <div class="img-txt">
                                    <img src="{{urlImg('131x87',$article->titlepic)}}">
                                    <p>{{$article->smalltext}}</p>
                                </div>
                            </a>
                        </section>
                    @endif
                @endforeach
            </section>
            <div>
                <div class="moreFoot">
                    <a class="js-article-list-load ffsong"
                       data-key="{{$key}}"
                       data-classid="{{isset($classid) ? $classid : 0}}"
                       data-ttid="{{isset($ttid) ? $ttid : 0}}"
                       data-end="0"
                       id="ffsong"
                       href="javascript:void(0)">加载更多&gt;&gt;
                    </a>
                </div>
            </div>
        </article>
    </div>
@endsection
