# Module: ecommerce

Gom toàn bộ phần bán hàng: **dữ liệu sản phẩm**, **danh mục sản phẩm**, **giỏ hàng** và **thêm giỏ hàng**. Thay thế module `product` cũ (đã `enabled: false`).

## Path (có thể đổi)

Đổi trong `module.json` → `config.paths`:

```json
{ "config": { "paths": { "products": "/san-pham", "cart": "/gio-hang", "addcart": "/addcart" } } }
```

Provider đọc trực tiếp `module.json` khi boot và đăng ký route theo các path này.

## Routes

| Method | Path | Controller |
|---|---|---|
| GET | `{products}` | `ProductController@index` (list, hỗ trợ `?cat=<id>`) |
| GET | `{products}/{id\|url}` | `ProductController@show` (product theo url/id; nếu không có → category theo url) |
| GET | `{cart}` | `CartController@index` (giỏ hàng từ session) |
| GET | `{addcart}/{id\|url}` | `CartController@add` (thêm vào session, redirect về `{cart}`) |

## Bảng

- `product`, `category` (override qua `database.tables.*`).
- Giỏ hàng lưu trong session (`ecommerce_cart`), không dùng bảng.

## View

- `resources/views/product/index.php`, `product/show.php` (markup legacy).
- `resources/views/cart/index.php`.