# Module Convention

Hướng dẫn module-hoá một chức năng từ web cũ (`legacy/`) thành **module hoàn chỉnh**:
tự quản lý dữ liệu, tự hiện lên dashboard, **và đăng ký controller thay thế legacy**
(khi `/legacy` bị loại bỏ).

## 0. Vòng đời một module

Một module có thể đảm nhận trọn vẹn một feature:

| Thành phần | Vai trò | Khai báo |
|------------|---------|----------|
| `schema.json` | Bảng dữ liệu (tự tạo/đồng bộ) | bắt buộc nếu có data |
| `admin` block | CRUD + nav trên dashboard | trong `module.json` |
| Controller + routes | **Thay thế trang/route legacy** | provider hoặc `module.json.routes` |
| Hooks (`render.head`...) | Nhúng vào trang khác | `ModuleContext` |

Khi `/legacy` bị gỡ, mọi URL public sẽ do controller của module phục vụ
(Witals router ưu tiên hơn fallback legacy).

## 1. Cấu trúc một module hoàn chỉnh

```
modules/<name>/
├── module.json                       # khai báo + admin block + providers
├── schema.json                       # bảng dữ liệu
└── src/
    ├── Providers/<Name>ServiceProvider.php   # đăng ký route public (thay legacy)
    └── Controllers/<Name>Controller.php      # controller public
```

Nếu **chỉ cần quản lý data** (không có route public riêng): bỏ `providers`,
chỉ cần `module.json` (có `admin.crud`) + `schema.json` — CRUD tự sinh.

## 2. module.json

```json
{
  "name": "newsletter",
  "version": "1.0.0",
  "type": "data",
  "enabled": true,
  "providers": [],
  "admin": {
    "group": "Marketing",
    "group_icon": "📣",
    "nav":  { "label": "Newsletter", "url": "/dashboard/newsletter", "icon": "✉️" },
    "crud": {
      "table": "newsletter_subscribers",
      "title": "Đăng ký nhận tin",
      "title_field": "email",
      "list_fields": ["email", "name", "created_at"]
    }
  }
}
```

- `admin.group`: nhóm nav trên dashboard (gộp theo tên). Bỏ trống → link top-level.
- `admin.nav`: item menu (label, url, icon).
- `admin.crud.table`: tên **logical** của bảng (đặt trong `schema.json`).
  Có thể override tên vật lý qua config `database.tables.<table>`.
- `admin.crud.pk`: tên cột khoá chính (mặc định `id`). Bảng legacy hay dùng
  `id_product`, `id_news`, `id_category`... — khai báo đúng để CRUD chạy.
- `admin.crud.list_fields`: các cột hiển thị ở màn list.
- `admin.crud.form_fields` (tuỳ chọn): giới hạn trường trong form thêm/sửa
  (mặc định mọi cột trừ khoá chính + created_at/updated_at).

## 3. schema.json

Khai báo bảng theo format của `ModuleSchemaManager`:

```json
{
  "version": "1.0.0",
  "tables": [
    {
      "name": "newsletter_subscribers",
      "columns": [
        { "name": "id",         "type": "primary" },
        { "name": "email",      "type": "string", "size": 255, "nullable": false },
        { "name": "name",       "type": "string", "size": 255, "nullable": true },
        { "name": "created_at", "type": "datetime", "nullable": true }
      ],
      "indexes": [
        { "name": "uq_email", "columns": ["email"], "unique": true }
      ]
    }
  ]
}
```

Bảng được **tự tạo/đồng bộ** khi khởi động (chỉ chạy khi hash schema đổi).

## 4. Route tự sinh (không cần code)

| Method | Path | Chức năng |
|--------|------|-----------|
| GET  | `/dashboard/{module}` | Danh sách |
| GET  | `/dashboard/{module}/create` | Form thêm |
| POST | `/dashboard/{module}` | Lưu mới |
| GET  | `/dashboard/{module}/{id}/edit` | Form sửa |
| POST | `/dashboard/{module}/{id}` | Cập nhật |
| POST | `/dashboard/{module}/{id}/delete` | Xóa |
| GET  | `/api/{module}` | JSON list |

Form thêm/sửa được sinh tự động từ cột trong `schema.json`
(bỏ qua `id`, `created_at`, `updated_at`).

## 5. Khi module-hoá feature từ web cũ

- **Bảng legacy đã tồn tại** (vd `newsletter`, `product`, `article`):
  khai báo `schema.json` **mirror đúng schema legacy** (đủ cột NOT NULL) để
  `ModuleSchemaManager` không phá dữ liệu cũ, và form CRUD có đủ trường.
  - *Lưu ý:* tránh trùng tên với bảng legacy nếu module cần schema riêng —
    đặt tên khác hoặc dùng override `database.tables.<logical>`.
- **Dữ liệu mới**: đặt tên bảng riêng, khai báo tự do.
- **Controller thay legacy**: đăng ký route public trong provider của module
  (vd `POST /newsletter/subscribe`). Witals router được ưu tiên hơn fallback
  `legacy/site.php`, nên route mới sẽ thay thế hành vi legacy.
- Khi `/legacy` bị gỡ hoàn toàn: chuyển toàn bộ `legacy/modules/*.php` thành
  controller module tương ứng; xoá fallback legacy trong `public/index.php`.

## 6. Tuỳ biến nâng cao

- Cần logic riêng (validate, upload, gọi API): thêm `src/Providers/<X>ServiceProvider.php`
  và khai báo trong `providers`, đăng ký route riêng.
- Override tên bảng: `config('database.tables.<logical>')`.
- Bật/tắt module: `modules.enabled.<name>` trong config, hoặc `"enabled"` trong module.json.

## 7. Ví dụ đã có

- `modules/newsletter` — data module mẫu (CRUD + dashboard).
- `modules/seo`, `modules/schema-org`, `modules/auto-index` — feature module
  (hook `render.head`, API riêng).

## 8. Template engine (Stempler) cho controller

Dự án dùng **Stempler** (RoadRunner) — compile ra PHP, cache ở
`storage/framework/views`, hiệu năng cao. View đặt tại `resources/views`.

- **Layout head/foot**: dùng engine native PHP (`layouts/head.php`,
  `layouts/foot.php`) vì Stempler yêu cầu HTML cân bằng mỗi template.
  Biến: `{{ }}`→`<?= htmlspecialchars($v ?? '', ENT_QUOTES) ?>`,
  `{!! !!}`→`<?= $v ?? '' ?>`.
- **Content**: template Stempler cân bằng (`news/index.stempler.php`) với
  `@if`, `@foreach`, `{{ $var }}`.
- **Render trong controller**:
  ```php
  $f = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
  $html = $f->make('layouts/head', $data)->render()
        . $f->make('news/index', $data)->render()
        . $f->make('layouts/foot', $data)->render();
  return Response::html($html);
  ```
- **Tên view**: bỏ đuôi file (`news/index` → `news/index.stempler.php`).
  `@include('layouts/head')` cũng bỏ đuôi.
- Stempler chỉ hỗ trợ `@include/@if/@foreach/@php/@json` (không có `@extends`).