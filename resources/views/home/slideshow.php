<div>
    <div class="swiper-container" id="slide_home">
        <div class="homeslide-next">  </div>
        <div class="homeslide-prev">  </div>
        <div class="swiper-wrapper">
            <?php foreach (($slides ?? []) as $s): ?>
            <div class="swiper-slide">
                <img src="<?= htmlspecialchars((string)($s['image'] ?? ''), ENT_QUOTES) ?>" alt="<?= htmlspecialchars((string)($s['name'] ?? ''), ENT_QUOTES) ?>" style="width:100%;display:block"/>
            </div>
            <?php endforeach; ?>
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