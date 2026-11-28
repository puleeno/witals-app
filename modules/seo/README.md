# Module: SEO

Quản lý thẻ SEO (title, description, keywords, canonical, Open Graph, Twitter,
robots) cho từng trang và **tự động render vào `<head>`** của trang legacy.

## Chức năng
- Lưu metadata SEO theo `url` hoặc theo `entity_type` + `entity_id`.
- Hook `render.head` trên `ModuleContext` → chèn thẻ `<link rel="canonical">`,
  `<meta name="robots">`, Open Graph (`og:*`) và Twitter Card (`twitter:*`).
- Hook `edit.fields` → cung cấp danh sách trường SEO cho màn hình edit của
  dashboard (module khác nhúng vào form).
- **Không có dữ liệu SEO → không xuất gì** (giữ nguyên HTML gốc).

## File
```
modules/seo/
├── module.json                          # khai báo module (feature, priority 20)
├── schema.json                          # bảng seo_meta
└── src/
    ├── Providers/SeoServiceProvider.php  # đăng ký hook render.head + edit.fields
    └── Services/SeoService.php           # đọc DB + render thẻ <head>
```

## Bảng dữ liệu
`seo_meta` (xem `schema.json`):

| Cột           | Kiểu      | Ghi chú                          |
|---------------|-----------|----------------------------------|
| id            | primary   |                                  |
| entity_type   | string    | vd: `article`, `product`         |
| entity_id     | integer   |                                  |
| url           | string    | unique — dùng để match trang     |
| title         | string    |                                  |
| description   | text      |                                  |
| keywords      | string    |                                  |
| canonical     | string    | URL canonical                    |
| og_image      | string    | ảnh Open Graph / Twitter         |
| robots        | string    | vd: `index,follow`               |
| updated_at    | datetime  |                                  |

> Đổi tên bảng: đặt `database.tables.seo_meta` trong config (không cần sửa code).

## Cách setup
1. Bật module: `modules/seo/module.json` → `"enabled": true`.
2. Module tự tạo bảng `seo_meta` qua `ModuleSchemaManager` khi khởi động.
3. Chèn dữ liệu SEO cho một trang (thủ công hoặc từ màn edit dashboard):
   ```sql
   INSERT INTO seo_meta (url, title, description, canonical, og_image, robots, updated_at)
   VALUES ('/san-pham/cay-canh', 'Cây cảnh đẹp', 'Mô tả...', 'https://xanhvina.com/san-pham/cay-canh',
           'https://xanhvina.com/upload/og.jpg', 'index,follow', NOW());
   ```
4. `legacy/site.php` tự share entity context (`url`, `type`, `id`) và gọi
   `ModuleContext::render('render.head')` trước `</head>` → thẻ SEO xuất hiện.

## Entity context
`site.php` đặt vào `ModuleContext`:
```php
$context->share('entity', ['url' => $uri, 'type' => $type, 'id' => $id]);
```
`SeoService::findCurrent()` ưu tiên match `entity_type`+`entity_id`, sau đó `url`.

## API / Hook
- Hook: `render.head`, `edit.fields`.
- Không có route riêng (module thuần render).