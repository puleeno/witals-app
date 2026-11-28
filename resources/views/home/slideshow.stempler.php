<div>
    <div class="swiper-container" id="slide_home">
        <div class="homeslide-next">  </div>
        <div class="homeslide-prev">  </div>
        <div class="swiper-wrapper">
            @foreach(($slides ?? []) as $s)
            <div class="swiper-slide">
                <img src="{{ $s['image'] ?? '' }}" alt="{{ $s['name'] ?? '' }}" style="width:100%;display:block"/>
            </div>
            @endforeach
        </div>
        <div class="c"></div>
    </div>
</div>
<div class="c"></div>
<script>
    $(function () {
        if (typeof Swiper === 'undefined') return;
        new Swiper('#slide_home', {
            loop: true,
            slidesPerView: 1,
            autoplay: { delay: 3000, disableOnInteraction: false },
            spaceBetween: 0,
            navigation: { nextEl: '.homeslide-prev', prevEl: '.homeslide-next' },
            speed: 500,
        });
    });
</script>