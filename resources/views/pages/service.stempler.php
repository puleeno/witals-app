<div class="c5"></div>
<div class="crumb"><div class="grid"><a href="/{{ $lang_dir ?? '' }}">Trang chủ</a> <i class="fa fa-angle-right"></i> {{ $cat['name'] ?? '' }}</div></div>
<div class="c20"></div>

<div class="content-detail">
    <div class="c20"></div>
    <div class="grid">
        <div class="flex-container flex-space-between">
            <div class="cell-1-4 hide-on-tab hide-on-mobile">
                <div class="c30"></div>
            </div>
            <div class="cell-3-4 tab-cell-1-1">
                <h1 class="title-page">{{ $cat['name'] ?? '' }}</h1>
                <div class="c10"></div>
                <div class="cat-content">{!! $cat['content'] ?? '' !!}</div>
                <div class="c10"></div>

                <div class="flex-container flex-space-between">
                    @foreach($children as $c)
                        <div class="cell-1-3" style="margin-bottom: 30px;">
                            <a href="/{{ trim((string)($c['url'] ?? ''), '/') }}">{!! $c['image'] ?? '' !!}</a>
                            <div class="c5"></div>
                            <h2 class="service-name"><a href="/{{ trim((string)($c['url'] ?? ''), '/') }}">{{ $c['name'] ?? '' }}</a></h2>
                            <div class="c10"></div>
                            {!! $c['intro'] ?? '' !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="c30"></div>
</div>
