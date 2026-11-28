<div class="c"></div>
<div class="crumb"><div class="grid"><a href="/{{ $lang_dir ?? '' }}"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right"></i> {{ $cat['name'] ?? '' }}</div><div class="c5"></div></div>
<div class="c"></div>

<div class="c20"></div>
<div class="grid">

<a name="map_here"></a>

<h1 class="title-page"><span>{{ $cat['name'] ?? '' }}</span></h1>

<div class="c5"></div>
<div class="c10"></div>
<div class="cat-content">{!! $cat['intro'] ?? '' !!}</div>
<div class="c10"></div>

<div class="c20"></div>

<div class="contact_input" style="margin-bottom:0px;">
    <form action="#map_here" method="GET">
        <div class="flex-container flex-space-between">
            <div class="cell-3-10 tab-cell-1-1">
                <select name="prv" id="tinhthanh" class="txt-contact">
                    <option value="">----Chọn tỉnh/thành phố----</option>
                </select>
            </div>
            <div class="cell-3-10 tab-cell-1-1">
                <select name="dst" id="quanhuyen" class="txt-contact">
                    <option value="">----Chọn quận huyện----</option>
                </select>
            </div>
            <div class="cell-3-10 tab-cell-1-1">
                <input name="mapkw" type="text" class="txt-contact" placeholder="Tên đường, tỉnh thành, quận huyện, tên đại lý" value="{{ $_GET['mapkw'] ?? '' }}" />
            </div>
            <div class="cell-1-10 tab-cell-1-1">
                <input class="btn-contact" type="submit" value="Tìm kiếm" />
            </div>
        </div>
        <div class="c20"></div>
    </form>
</div>

<div class="c10"></div>

<div class="flex-container flex-space-between">
    <div class="cell-1-2 tab-cell-1-1" style="height:500px">
        <div id="gmap" style="width:100%; height:100%;"></div>
    </div>
    <div class="cell-1-2 tab-cell-1-1">
        <div style="padding:15px; background:#0f75bc; color:#FFF; font-family:'Muli-B' ">Tìm thấy 0 kết quả</div>
        <div class="c"></div>
        <div id="controls-tabs"></div>
    </div>
</div>

</div>

<div class="c10"></div>
