@php
$product = $product ?? [];
$news = $news ?? [];
$video = $video ?? [];
$other = $other ?? [];
$dir = (string)($dir_path ?? '');
$productBase = '/san-pham';
$addcartBase = '/addcart';
$newsBase = '/tin-tuc';
@endphp

<style>
    .flashsale-header { display: flex; align-items: center; gap: 10px; }
    .flashsale-header h2 { margin: 0; }
</style>

@foreach($product as $grp)
@php $cat = $grp['cat']; $items = $grp['items']; $catimage = $cat['image'] ? $dir . '/' . ltrim((string)$cat['image'], '/') : ''; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); @endphp
<!-- START BLOCK : product -->
<div class="flashsale-wrapper">
    <div class="c50"></div>
    <div class="grid">
        <div class="homecat-title">
            <div class="flashsale-header">
                <img class="flas-sales" src="{{ $catimage ?: 'data:image/gif;base64,R0lGODlhAQABAAAAACwAAAAAAQABAAA=' }}" width="80" alt="{{ $catname ?? '' }}">
                <h2><a href="{{ $link }}">{{ $cat['name'] ?? '' }}</a></h2>
                <div class="flashsale-timer"><div class="countdown"></div></div>
            </div>
        </div>
        <div class="c30"></div>
        <div class="flex-container">
            @foreach($items as $i)
            @php
                $price = (float)($i['price'] ?? 0);
                $pricekm = (float)($i['pricekm'] ?? 0);
                $priceOut = $pricekm > 0 ? number_format($pricekm) . ' đ' : ($price > 0 ? number_format($price) . ' đ' : (string)($i['price'] ?? ''));
                $pricekmOut = $pricekm > 0 ? number_format($price) . ' đ' : '';
                $sale = '';
                if ($pricekm > 0 && $price > 0) {
                    $disc = (int)floor((($price - $pricekm) / $price) * 100);
                    if ($disc > 0) {
                        $sale = '<div class="home-product-item__sale-off"><span class="home-product-item__sale-off-percent">-' . $disc . '%</span><span class="home-product-item__sale-off-label">GIẢM</span></div>';
                    }
                }
            @endphp
            <div class="cell-1-4 tab-cell-1-2">
                <div class="producthome-item">
                    <div class="img-hv">
                        <a href="{{ $productBase }}/{{ $i['url'] ?? '' }}" class="img-shine" title="{{ $i['name'] ?? '' }}">
                            <img src="{{ $i['image'] ?? '' }}" alt="{{ $i['name'] ?? '' }}" style="width:100%">
                        </a>
                        <a class="addcart" href="{{ $addcartBase }}/{{ $i['url'] ?? '' }}" title="Thêm vào giỏ hàng"><i class="fas fa-cart-plus"></i></a>
                    </div>
                    <div class="producthot-box">
                        <h3>
                            <a href="{{ $productBase }}/{{ $i['url'] ?? '' }}" class="producthot-item__name text-line-2" title="{{ $i['name'] ?? '' }}">
                                {{ $i['name'] ?? '' }}
                            </a>
                        </h3>
                        <div class="c10"></div>
                        <div class="producthot-item__intro text-line-2">{!! strip_tags((string)($i['intro'] ?? '')) !!}</div>
                        <div class="producthot__price">
                            <span class="producthot__price-old">{{ $pricekmOut }}</span>
                            <span class="producthot__price-current">{{ $priceOut }}</span>
                        </div>
                    </div>
                    <div class="c10"></div>
                    {!! $sale !!}
                </div>
            </div>
            @endforeach
        </div>
        <div class="c30"></div>
        <div class="text-center"><a href="{{ $link }}" class="btn-seeall">Xem Tất cả</a></div>
        <div class="c30"></div>
    </div>
    <div class="c20"></div>
</div>
<!-- END BLOCK : product -->
@endforeach

@foreach($news as $grp)
@php $cat = $grp['cat']; $items = $grp['items']; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); @endphp
<!-- START BLOCK : news -->
<div class="servicehome-wrapper" style="background-color: #3b3b3b0a;">
    <div class="c50"></div>
    <div class="grid">
        <h2 class="homecat-title homecat-title__service"><a href="{{ $link }}">{{ $cat['name'] ?? '' }}</a></h2>
        <div class="c30"></div>
    </div>
    <div class="swiper-container slider-homenews">
        <div class="swiper-wrapper">
            @foreach($items as $i)
            <div class="swiper-slide">
                <div class="service-item">
                    <a href="{{ $newsBase }}/{{ $i['url'] ?? $i['id_news'] ?? '' }}">
                        <img src="{{ $i['image'] ?? '' }}" style="width:100%" alt="{{ $i['name'] ?? '' }}">
                    </a>
                    <div class="service-item__content">
                        <h3 class="service-item__name">
                            <a href="{{ $newsBase }}/{{ $i['url'] ?? $i['id_news'] ?? '' }}" class="text-line-2">{!! $i['name'] ?? '' !!}</a>
                        </h3>
                        <div class="text-line-3">{!! $i['intro'] ?? '' !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="service-button-next"><i class="fas fa-chevron-right"></i></div>
        <div class="service-button-prev"><i class="fas fa-chevron-left"></i></div>
    </div>
    <div class="c30"></div>
    <div class="c50"></div>
</div>
<!-- END BLOCK : news -->
@endforeach

@foreach($video as $grp)
@php $cat = $grp['cat']; $items = $grp['items']; $link = '/' . trim((string)($cat['url'] ?? ''), '/'); @endphp
<!-- START BLOCK : video -->
<div class="grid">
    <div class="c20"></div>
    <div class="title-cat-home"><a href="{{ $link }}">{{ $cat['name'] ?? '' }}</a></div>
    <a class="view-more-home" href="{{ $link }}">Xem thêm <i class="fa fa-angle-right"></i></a>
    <div class="c"></div>
    <div class="title-cat-line"></div>
    <div class="c20"></div>
    <div class="flex-container flex-space-between video-home">
        @foreach($items as $i)
        @php $vc = ''; if (preg_match('%(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]+)%', (string)($i['video'] ?? ''), $m)) $vc = $m[1]; @endphp
        <div class="cell-1-3 tab-cell-1-2 video-home-item">
            <div style="position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;"><iframe allowfullscreen frameborder="0" height="360" src="//www.youtube.com/embed/{{ $vc }}?modestbranding=1&amp;fs=1&amp;rel=0" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;" width="300"></iframe></div>
            <div class="c5"></div>
            <h3 class="video-home-name"><a href="{{ $newsBase }}/{{ $i['url'] ?? '' }}">{{ $i['name'] ?? '' }}</a></h3>
        </div>
        @endforeach
    </div>
    <div class="c20"></div>
</div>
<!-- END BLOCK : video -->
@endforeach

@foreach($other as $o)
{!! is_string($o) ? $o : '' !!}
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countdownElements = document.querySelectorAll('.countdown');
        let endTime = localStorage.getItem('flashsale_endTime');
        const now = Date.now();
        if (!endTime) { endTime = now + 4 * 60 * 60 * 1000; localStorage.setItem('flashsale_endTime', endTime); }
        else { endTime = parseInt(endTime); }
        countdownElements.forEach((el, index) => {
            if (index !== 0) { el.style.display = 'none'; return; }
            function updateCountdown() {
                const distance = endTime - Date.now();
                if (distance < 0) { endTime = Date.now() + 4 * 60 * 60 * 1000; localStorage.setItem('flashsale_endTime', endTime); }
                else {
                    const h = String(Math.floor(distance / 3600000)).padStart(2, '0');
                    const m = String(Math.floor((distance % 3600000) / 60000)).padStart(2, '0');
                    const s = String(Math.floor((distance % 60000) / 1000)).padStart(2, '0');
                    el.innerHTML = h + ':' + m + ':' + s;
                }
            }
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    });
</script>
<script>
    $(function () {
        if (typeof Swiper === 'undefined') return;
        document.querySelectorAll('.slider-homenews').forEach(function (el) {
            new Swiper(el, {
                loop: true, slidesPerView: 4, spaceBetween: 20,
                autoplay: { delay: 2000 },
                pagination: { clickable: true },
                navigation: { nextEl: '.service-button-next', prevEl: '.service-button-prev' },
                breakpoints: { 320: { slidesPerView: 1, spaceBetween: 20 }, 768: { slidesPerView: 2.5, spaceBetween: 30 }, 1024: { slidesPerView: 3, spaceBetween: 20 } }
            });
        });
    });
</script>
