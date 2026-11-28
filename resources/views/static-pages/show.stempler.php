<div class="c"></div>

<div class="c10"></div>

<div class="grid">
    <div class="flex-container flex-space-between">

        <div class="cell-3-4 tab-cell-1-1">
            <div class="c20"></div>

            <h1 class="page-name">{{ $cat['name'] ?? '' }}</h1>
            <div class="c15"></div>
            <div class="content-detail">{!! $cat['content'] ?? '' !!}</div>
            <div class="c20"></div>

            @if($info)
                <h2 class="page-name">{{ $info['name'] }}</h2>
                <div class="c15"></div>
                <div class="content-detail">{!! $info['content'] ?? '' !!}</div>
                <div class="c20"></div>
                <div class="line-gray"></div>
                <Div class="c20"></Div>
                <div class="addthis_toolbox addthis_default_style ">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
                    <a class="addthis_counter addthis_pill_style"></a>
                </div>
                <div class="c10"></div>
            @endif

            @if($others)
                <div style="font-weight:bold; color:#FF0000">Các bài viết khác</div>
                <div class="c5"></div>
                <ul class="other-article">
                    @foreach($others as $o)
                        <li><a href="{{ $o['link_detail'] ?? '' }}"><i class="fa fa-angle-double-right"></i>{{ $o['name'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="cell-1-4 tab-cell-1-1">
            {!! $leftcol ?? '' !!}
        </div>

    </div>

    <div class="c10"></div>
</div>
