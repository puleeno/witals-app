<style>
    .item-cart{ font-size: 14px; }
    .hd-cart{ background: #f1f2f2; padding: 12px 10px; font-weight: bold; text-transform: uppercase; }
    .cart-title-number { width: 40px; height: 40px; margin-bottom: -2px; float: left; background: #005aa0; border-radius: 50%; text-align: center; line-height: 40px; color: #FFF; font-size: 20px; }
    .cart-title { margin-left: 25px; padding-left: 25px; }
    .cart-title-name{ text-transform: uppercase; line-height: 40px; font-size: 18px; margin: 0; padding: 0; font-weight: normal; }
    .btn_red_bg { color: #FFF; font-size: 18px; padding: 12px 50px; border-radius: 5px; text-decoration: none; float: left; border: none; background: #005aa0; }
    .cart-color{ float:left; margin-right:10px; text-transform:uppercase; line-height:30px; border: solid 1px #DDD; border-radius: 3px; padding:0px 15px; }
    .cart-image img{ display: block; margin-right: 10px; }
    .total-payment { color: #333; font-family:"Muli-B"; font-weight: normal; font-size: 16px; padding: 10px; float: right; }
    .total-payment span{ color: #ea313e; font-size: 20px; }
    .line-frm{ line-height: 30px; }
    .cart-name{ text-transform: uppercase; font-size: 16px; color: #333; text-decoration: none; font-weight: bold; }
    .quantity .fa{ color: #FFF; font-size: 22px; background: #005aa0; display: block; float: left; padding: 3px 5px 1px 5px; margin-left: 5px; cursor: pointer; }
    .txt-quantity{ text-align:center; width:60px; height: 26px; }
    .buy-other{ display: block; float: left; text-decoration: none; padding: 8px 20px 7px 40px; border: solid 1px #005aa0; border-radius: 5px; color: #005aa0; font-weight: bold; font-size: 16px; margin-left: 20px; cursor: pointer; background: url(../images/cart-icon.png) 5px no-repeat; }
    .step-02{ padding: 0px 20px; font-size: 14px; }
    .contact_input{ margin-bottom: 15px; width: 100%; }
    .contact_input input{ height: 36px; border-radius: 5px; border:solid 1px #999; background: #EEE; }
    .contact_input textarea{ width: 100%; border-radius: 5px; border:solid 1px #999; background: #EEE; }
    .remove-cart{ color: #FF0000; font-size: 20px; }
    .price-mobile{ display:none; }
    @@media handheld, only screen and (max-width: 767px) {
        .price-mobile{ display:block; float: right; margin-bottom: 10px; font-weight: bold; }
        .cart-name{ text-transform: inherit; font-size: 14px; }
        .quantity{ float: left; }
        .buy-other{ float: right; }
        .btn_red_bg{ float: right; margin-bottom: 10px; }
    }
</style>
<script src="/js/jquery.form.min.js"></script>

<div class="c20"></div>
<div class="grid">
<div style="background: #FFF;">
    <div class="c15"></div>
    <div style="margin-left: 20px;">
        <div class="cart-title-number">01</div>
        <div class="cart-title">
            <h1 class="fl cart-title-name">Giỏ hàng</h1>
            <div class="c"></div>
        </div>
    </div>
    <div class="c15"></div>

    <div class="flex-container flex-space-between hd-cart hide-on-mobile">
        <div class="cell-6-12 col-hd-cart">Thông tin sản phẩm</div>
        <div class="cell-2-12 col-hd-cart hd-mb">Đơn giá</div>
        <div class="cell-3-12 col-hd-cart">Thành tiền</div>
        <div class="cell-1-12 col-hd-cart">Xóa</div>
        <div class="c"></div>
    </div>
    <div class="c10"></div>

    <div id="giohang_row">
        @php $tong = 0; @endphp
        @foreach(($cart ?? []) as $item)
        @php
            $price = (float)($item['price_num'] ?? preg_replace('/\D/','',(string)($item['price'] ?? '0')));
            $qty = max(1, (int)($item['qty'] ?? 1));
            $thanhtien = $price * $qty; $tong += $thanhtien;
            $id = (int)($item['id_product'] ?? 0);
        @endphp
        <div class="c5"></div>
        <div class="flex-container flex-space-between item-cart">
            <div class="cell-6-12 mobile-cell-1-1">
                <div class="flex-container flex-space-between">
                    <div class="cell-1-5">
                        <a href="{{ $item['link_detail'] ?? '' }}" class="cart-image" target="_blank"><img src="{{ $item['image'] ?? '' }}" width="80" alt=""></a>
                    </div>
                    <div class="cell-4-5">
                        <a href="{{ $item['link_detail'] ?? '' }}" class="cart-name" title="View product" target="_blank">{{ $item['name'] ?? '' }}</a>
                        <div class="c5"></div>
                        <div class="quantity">
                            <div class="price-mobile">{{ number_format($thanhtien) }}&nbsp;VNĐ&nbsp;</div>
                            <div style="float: left;">Số lượng: <input type="text" class="txt-quantity" name="quantity[]" ref="{{ $id }}" value="{{ $qty }}"></div>
                            <div style="float: left; margin-left: 10px;">
                                <i class="fa fa-plus" onClick="increaseQty($(this));"></i>
                                <i class="fa fa-minus" onClick="decreaseQty($(this));"></i>
                            </div>
                            <div class="c10"></div>
                            <div><div class="cart-color">{{ $item['color'] ?? '' }}</div></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id_product[]" value="{{ $id }}"/>
                <div class="c10"></div>
            </div>
            <div class="cell-2-12 hide-on-mobile cart-price hd-mb">{{ number_format($price) }} VNĐ&nbsp;</div>
            <div class="cell-3-12 hide-on-mobile cart-price">{{ number_format($thanhtien) }}&nbsp;VNĐ&nbsp;</div>
            <div class="cell-1-12 mobile-cell-1-1" style="text-align:left;">
                <a class="remove-cart" href="#" ref="{{ $id }}" onClick="delItemCart($(this)); return false;"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        </div>
        <div class="c5"></div>
        <div style="border-bottom:solid 1px #CCC"></div>
        <div class="c5"></div>
        @endforeach

        <div class="c10"></div>
        <div class="total-payment" align="right">Tổng cộng: <span>{{ number_format($tong) }} đ</span></div>
    </div>
    <div class="c10"></div>

    <script>
        function increaseQty(obj){ var i=obj.parent().parent().find(".txt-quantity"); i.val(parseInt(i.val())+1); updateItemQuantity(i.val(), i.attr("ref")); }
        function decreaseQty(obj){ var i=obj.parent().parent().find(".txt-quantity"); var q=parseInt(i.val())-1; if(q<1){q=1;}else{i.val(q);updateItemQuantity(i.val(), i.attr("ref"));} }
        $('#giohang_row').on('change', '.txt-quantity', function(){ updateItemQuantity($(this).val(), $(this).attr("ref")); });
        function delItemCart(obj){ if(confirm("Xóa sản phẩm này khỏi giỏ hàng ?")==false){return false;} var id=obj.attr("ref"); window.location.href="/cart/remove/"+id; }
        function updateItemQuantity(q,id){ window.location.href="/cart/update?uid="+id+"&uqty="+q; }
        $(".buy-other").click(function(){ window.history.back(); return false; });
    </script>

    <div class="c20"></div>
    <form action="/cart/checkout" method="post" id="cartform" onSubmit="return check_cart();">
        <input type="hidden" name="code" value="save" />
        <div class="step-02">
            <div class="cart-title-number">02</div>
            <div class="cart-title"><div class="fl cart-title-name">Xác nhận đặt hàng</div><div class="c"></div></div>
            <div class="c20"></div>
            <em> Vui lòng điền thông tin để đặt hàng </em>
            <div class="flex-container flex-space-between">
                <div class="cell-1-2 mobile-cell-1-1"><div class="contact_input"><input name="yourname" type="text" class="txt-contact notNull" id="yourname" placeholder="Họ tên*"></div></div>
                <div class="cell-1-2 mobile-cell-1-1"><div class="contact_input"><input name="phone" type="text" class="txt-contact notNull" placeholder="Điện thoại*"></div></div>
                <div class="cell-1-2 mobile-cell-1-1"><div class="contact_input"><input name="email" type="text" class="txt-contact" id="email" placeholder="Email"></div></div>
                <div class="cell-1-2 mobile-cell-1-1"><div class="contact_input"><input name="address" type="text" class="txt-contact" placeholder="Địa chỉ"></div></div>
                <div class="contact_input"><textarea name="other_request" rows="5" placeholder="Nội dung" style="height:60px;" class="txt-contact"></textarea></div>
                <div class="cell-1-3 mobile-cell-1-1">
                    <div class="cart-title-name">Hình thức thanh toán</div><div class="c10"></div>
                    <label><input type="radio" name="httt" value="1" checked> Chuyển khoản ngân hàng</label><div class="c10"></div>
                    <label><input type="radio" name="httt" value="2"> Thanh toán trực tuyến</label><div class="c10"></div>
                    <label><input type="radio" name="httt" value="3"> Thanh toán COD</label><div class="c10"></div>
                </div>
                <div class="cell-2-3 mobile-cell-1-1"></div>
            </div>
            <div class="c10"></div>
            <a type="button" onClick="$('#cartform').submit(); return false;" href="#" class="btn_red_bg"> MUA HÀNG </a>
        </div>
    </form>
    <div class="c20"></div>
</div>
</div>
