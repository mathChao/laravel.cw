$(function(){
    var $list = $('.p-list');
    var listPage = 2;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    function createArticleElement(article){
        var content = '<section>';
        content += '<a href="'+article.url+'">';
        content += '<h3 >'+article.title+'</h3>';
        content += '<div class="img-txt" >';
        content += '<img src="'+article.titlepic+'" >';
        content += '<p>'+article.smalltext+'</p >';
        content += '</div >';
        content += '</a >';
        content += '</section>';
        return content;
    }


    function AjaxLoadArticleList($this){
        var request = $.ajax({
            url: '/ajax/list/load',
            method: 'post',
            async: 'false',
            dataType: 'json',
            data: {
                page : listPage,
                ttid : $this.data('ttid'),
                classid : $this.data('classid')
            }
        });

        request.done(function (result) {
            if (result.code == 200) {
                var $articleWrap = $('.js-article-list-wrap');
                if(result.data.articles){
                    $.each(result.data.articles, function(id, article){
                        $articleWrap.append($(createArticleElement(article)));
                    });
                }

                listPage ++;
            }else if(result.code == 210){
                $this.html('没有了');
                $this.data('end', '1');
            }
        });
    }

    if($list.length){

        $list.on('click', '.js-article-list-load', function(){
            var $this = $(this);
            if($this.data('end') == '0'){
                AjaxLoadArticleList($this);
            }
        });
    }


    function createArticleContentLoad(text){
        var content = '<div class="js-article-content-load—wrap">';
        content += '<div class="moreFoot">';
        content += '<a class="ffsong js-article-content-load" id="ffsong" href="javascript:void(0)" >'+text+' &gt;&gt;</a >';
        content += '</div>';
        content += '</div>';
        return content;
    }

    function ctrlShowText() {
        var text = text1 = text2 = '';
        var html1 ='';
        var showTextCount = 1000;
        var $article  = $('.js-article-content');
        var $element = $('.js-article-content>*');

        html = $article.html();
        if (html != undefined) {
            if (html.length > 2200) {
                for (var i = 0; i < $element.length; i++) {
                    if (text1.length < showTextCount) {
                        text1 += $($element[i]).text();
                        html1 += $element[i].outerHTML;
                    } else {
                        text2 += $($element[i]).text();
                    }
                    text += $($element[i]).text();
                }

                $article.empty();
                $article.append(html1);
                var percent = Math.round(text2.length / text.length * 100);
                if (percent > 0) {
                    $article.after(createArticleContentLoad('余下 '+percent+'%'));
                }
            }
        }
    }

    var $content = $('.p-content');
    if($content.length){
        var html = '';
        ctrlShowText();

        $content.on('click', '.js-article-content-load', function(){
            var $article  = $('.js-article-content');
            $article.empty();
            $article.append($(html));
            $('.js-article-content-load—wrap').css('display', 'none');
        });
    }
});