# AGENTS.md

## Directive bắt buộc (đọc trước khi làm)

- **GIỮ NGUYÊN HTML / CSS / STYLE giống hệt site cũ.** Output render phải giống old-source 100% (markup, class, cấu trúc thẻ, link CSS/JS, đường dẫn asset).
- **KHÔNG** tự thiết kế lại (không design system mới, không đổi class, không đổi cấu trúc header/footer/menu/content).
- Chỉ **KHÔNG dùng cấu trúc code/dự án cũ** (procedural PHP legacy). Logic được viết lại theo Witals (modules, `SiteContext`, Stempler/native views) nhưng **markup sinh ra phải y hệt legacy**.
- Khi chuyển template legacy → view: giữ nguyên thẻ HTML, chỉ thay `<?php ?>` bằng `{{ }}` / `{!! !!}` tương ứng (raw cho biến HTML, escaped cho text).
- Mọi thay đổi phải verify parity bằng `curl -k https://xanhvina.localhost/...` so với old-source.

## Dev

- Server: `php -S 127.0.0.1:80XX -t public public/index.php`.
- Xóa `storage/framework/cache/routes.php` sau khi đổi route.
- DB Cycle sau `$app->boot()`; `where('col','IN',$arr)` (KHÔNG có `whereIn`).