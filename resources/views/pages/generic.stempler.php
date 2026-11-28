<div class="c5"></div>
<div class="crumb"><div class="grid"><a href="/{{ $lang_dir ?? '' }}">Trang chủ</a> <i class="fa fa-angle-right"></i> {{ $cat['name'] ?? '' }}</div></div>
<div class="c20"></div>

<div class="content-detail">
    <div class="c20"></div>
    <h1 class="title-page">{{ $cat['name'] ?? '' }}</h1>
    <div class="c10"></div>
    <div class="cat-content">{!! $cat['content'] ?? '' !!}</div>
    <div class="c20"></div>
</div>
