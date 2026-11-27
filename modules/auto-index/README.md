# Module: auto-index

Tự động **submit URL lên Google Index API** và theo dõi trạng thái. An toàn khi
chưa cấu hình (chỉ ghi nhận `skipped`, không lỗi).

## Chức năng
- `AutoIndexService::submit($url, $entityType, $entityId)`:
  - Lấy OAuth2 token từ service-account JSON → gọi
    `https://indexing.googleapis.com/v3/urlNotifications:publish`.
  - Ghi kết quả vào bảng `auto_index` (status: `ok|error|skipped`).
- **No-op an toàn**: chưa bật hoặc thiếu credentials → trả `skipped`, không ném lỗi.
- API JSON cho dashboard / script.

## File
```
modules/auto-index/
├── module.json
├── schema.json                              # bảng auto_index
└── src/
    ├── Providers/AutoIndexServiceProvider.php
    ├── Controllers/AutoIndexController.php
    └── Services/AutoIndexService.php
```

## Bảng dữ liệu
`auto_index` (xem `schema.json`): `url` (unique), `entity_type`, `entity_id`,
`status`, `response`, `last_submitted_at`.

> Đổi tên bảng: `database.tables.auto_index` trong config.

## Cấu hình (BẮT BUỘC để chạy thật)
Module đọc config qua khoá `services.auto_index.*`:

| Khoá | Ý nghĩa |
|------|---------|
| `services.auto_index.indexing_enabled` | bool — bật/tắt (mặc định `false`) |
| `services.auto_index.service_account_json` | đường dẫn file JSON của Google Service Account |

### Lấy Service Account JSON
1. Vào [Google Cloud Console](https://console.cloud.google.com/) → tạo/chọn project.
2. Bật **Web Search Indexing API** (APIs & Services → Library).
3. Tạo **Service Account** → **Keys** → **Add key → Create new key → JSON**.
4. Tải file JSON, đặt lên server (vd `storage/keys/indexing-sa.json`).
5. Cấp quyền: thêm service account làm **Owner** của tài sản trong
   [Google Search Console](https://search.google.com/search-console)
   (Settings → Users and permissions → Add user, email dạng
   `...@...iam.gserviceaccount.com`).

### Nạp config
Có 2 cách:

**A. Qua biến môi trường / file .env** (khuyến nghị):
```dotenv
AUTOINDEX_ENABLED=true
AUTOINDEX_SA_JSON=/duong/dan/storage/keys/indexing-sa.json
```

**B. Trực tiếp trong code** (nếu dự án có `config/services.php`):
```php
return [
    'auto_index' => [
        'indexing_enabled'     => true,
        'service_account_json' => base_path('storage/keys/indexing-sa.json'),
    ],
];
```

> Hiện dự án **chưa có** thư mục `config/` và **chưa có** màn hình cấu hình
> trong dashboard. Bạn cần đặt cấu hình bằng cách A/B ở trên, hoặc dùng API.

## API
| Method | Path | Mô tả |
|--------|------|-------|
| GET  | `/api/auto-index/status` | `{ "enabled": bool }` |
| POST | `/api/auto-index/submit` | body `{ url, entity_type?, entity_id? }` → `{ status, response }` |

Ví dụ:
```bash
curl -X POST http://localhost:8000/api/auto-index/submit \
  -H 'Content-Type: application/json' \
  -d '{"url":"https://xanhvina.com/san-pham/cay-canh","entity_type":"product","entity_id":42}'
```

Kết quả mẫu:
```json
{ "status": "ok", "response": "{...}" }
```

## Ghi chú
- Chỉ nên submit URL đã public, không submit trùng liên tục (Google giới hạn
  ~200 URL/ngày cho Index API).
- Lấy token OAuth2 qua JWT `RS256` ký bằng `private_key` trong file JSON.
- Mọi lỗi DB/credentials đều được nuốt an toàn (không làm sập request).