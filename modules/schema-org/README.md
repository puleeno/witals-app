# Module: schema.org

Sinh **JSON-LD structured data** (schema.org) và chèn vào `<head>` để Google
hiểu nội dung trang (sản phẩm, bài viết, breadcrumb, tổ chức...).

## Chức năng
- Hook `render.head` → xuất `<script type="application/ld+json">` với `@graph`.
- Tự chọn loại node theo `entity_type`:
  - `product` → `Product`
  - `article` / `news` / `tin-tuc` → `Article`
  - khác → `WebPage`
- Luôn kèm `WebSite` + `Organization` (cấp site) và `BreadcrumbList` nếu có.
- Đọc `description` / `og_image` từ module **SEO** (phụ thuộc `seo`).
- **Không có entity → không xuất gì** (giữ nguyên HTML gốc).

## File
```
modules/schema-org/
├── module.json                              # depends: ["seo"]
└── src/
    ├── Providers/SchemaOrgServiceProvider.php
    └── Services/SchemaOrgService.php
```

## Phụ thuộc
- `seo` (đọc description/og_image). Đảm bảo `modules/seo` đã bật.

## Cách setup
1. Bật `modules/schema-org/module.json` → `"enabled": true`.
2. Không cần bảng DB — module chỉ đọc entity context + dữ liệu SEO.
3. `site.php` đã share `entity`; module tự render JSON-LD trước `</head>`.

## Entity context (tuỳ chọn nâng cao)
`SchemaOrgService` đọc thêm các khoá tuỳ chọn từ entity:
```php
$context->share('entity', [
    'url'         => $uri,
    'type'        => 'product',        // product | article | ...
    'title'       => 'Cây chuối cảnh',
    'breadcrumbs' => [
        ['name' => 'Trang chủ', 'url' => '/'],
        ['name' => 'Sản phẩm', 'url' => '/san-pham'],
    ],
]);
```
Nếu có `breadcrumbs` → sinh thêm `BreadcrumbList`.

## Kiểm tra
- Xem trang (`view-source`) → tìm `application/ld+json`.
- Dán URL vào [Google Rich Results Test](https://search.google.com/test/rich-results)
  để xác thực.

## Hook
- `render.head`.