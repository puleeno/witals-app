<div class="container" style="padding:20px 0;">
    <h1 class="title-main">{{ $page_title }}</h1>

    @if(count($items) === 0)
        <p>Chưa có tin tức.</p>
    @else
        <div class="news-list">
            @foreach($items as $item)
                <div class="news-item">
                    @if(!empty($item['image']))
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="max-width:200px; float:left; margin-right:15px;">
                    @endif
                    <h2><a href="{{ $item['url'] ?? '' }}">{{ $item['name'] }}</a></h2>
                    @if(!empty($item['intro']))
                        <p>{{ $item['intro'] }}</p>
                    @endif
                    <div class="c"></div>
                </div>
            @endforeach
        </div>
    @endif
</div>