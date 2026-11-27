# Data Module Convention

Hướng dẫn module-hoá một chức năng từ web cũ (`legacy/`) thành **data module**:
tự quản lý dữ liệu của mình và **tự hiện lên dashboard** mà không cần viết route/controller.

## 1. Cấu trúc một data module

```
modules/<name>/
├── module.json   # khai báo + block "admin" để register vào dashboard
└── schema.json   # khai báo bảng (ModuleSchemaManager tự tạo/đồng bộ)
```

Không cần provider/controller — `ModuleCrudServiceProvider` tự lo CRUD + API.

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
- `admin.crud.list_fields`: các cột hiển thị ở màn list.

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

- **Nếu bảng legacy đã tồn tại** (vd `newsletter`, `product`, `article`):
  khai báo `schema.json` **mirror đúng schema legacy** (đủ cột NOT NULL) để
  `ModuleSchemaManager` không phá dữ liệu cũ, và form CRUD có đủ trường.
  - *Lưu ý:* tránh trùng tên với bảng legacy nếu module cần schema riêng —
    đặt tên khác (vd `newsletter_subscribers`) hoặc dùng override
    `database.tables.<logical>`.
- **Nếu là dữ liệu mới**: đặt tên bảng riêng, khai báo tự do.
- Giữ nguyên phần render legacy (`legacy/modules/*.php`) để đảm bảo parity
  HTML/CSS/JS; module chỉ đảm nhận **quản lý dữ liệu + dashboard**.

## 6. Tuỳ biến nâng cao

- Cần logic riêng (validate, upload, gọi API): thêm `src/Providers/<X>ServiceProvider.php`
  và khai báo trong `providers`, đăng ký route riêng.
- Override tên bảng: `config('database.tables.<logical>')`.
- Bật/tắt module: `modules.enabled.<name>` trong config, hoặc `"enabled"` trong module.json.

## 7. Ví dụ đã có

- `modules/newsletter` — data module mẫu (CRUD + dashboard).
- `modules/seo`, `modules/schema-org`, `modules/auto-index` — feature module
  (hook `render.head`, API riêng).