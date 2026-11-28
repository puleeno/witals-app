# Module: home

Trang chủ **động**: render theo danh sách section trong `module.json` → `config.sections`. Mỗi section là một component độc lập (type + data), nên có thể render bất kỳ nội dung/thành phần nào, thêm/bớt/đổi thứ tự chỉ bằng config.

## Path

`module.json` → `config.path` (mặc định `/`).

## Sections có sẵn

| type | Mô tả | data |
|---|---|---|
| `slideshow` | Banner/slide (rỗng nếu chưa có) | `slides` (tùy chọn) |
| `products` | Danh sách sản phẩm | `limit`, `title`, `category` |
| `news` | Danh sách tin tức | `limit`, `title` |
| `html` | HTML thô bất kỳ | `content` |
| `view` | Render view bất kỳ | `view`, `data` |
| `cathome` | Giống home cũ: đọc `category.vitri='cathome'`, render theo `data_type` | — |

## cathome (giống home cũ)

Mỗi row `category` có `vitri` chứa `cathome` sẽ render 1 block theo `data_type`:
`product` → `home/cathome/product.php`, `news` → `home/cathome/news.php`, `video/album/info/service/article/logo` → `home/cathome/<type>.php` (nếu có).

Có thể **register handler** động (override view mặc định):

```php
/** @var \Modules\Home\Sections\CathomeSection $s */
$s->register('product', fn(array $row, array $ctx) => '<div>...</div>');
```

## Cấu hình

```json
"config": {
  "path": "/",
  "sections": [
    { "type": "slideshow" },
    { "type": "products", "data": { "limit": 8, "title": "Sản phẩm nổi bật" } },
    { "type": "html", "data": { "content": "<div class='banner'>...</div>" } },
    { "type": "view", "data": { "view": "home/section-foo", "data": {} } }
  ]
}
```

## Mở rộng

Thêm 1 class implement `SectionInterface` (`getType()` + `render($data,$context)`) vào `src/Sections/`, rồi đăng ký trong `SectionRegistry::__construct()`. Section mới tự động dùng được qua config.

View section: `resources/views/home/section-*.php`.