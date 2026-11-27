# Module: dashboard

Bảng điều khiển admin dạng **headless CMS**: SPA SolidJS mount vào shell do
RoadRunner phục vụ, hydrate dữ liệu từ API JSON `/api/dashboard/*`.

## Chức năng
- Shell admin dùng chung (`AdminController`): chrome, nav, design-system CSS.
- SPA SolidJS build sẵn tại `public/js/admin-solid-core.js` +
  `public/css/admin-core.css` / `admin-dashboard.css`.
- API JSON: `GET /api/dashboard/stats`.
- Auth: `/login` (GET/POST), `/logout` (POST) — tái dùng `AuthController`.

## File
```
modules/dashboard/
├── module.json
└── src/
    ├── Providers/DashboardServiceProvider.php   # đăng ký route dashboard + auth + api
    └── Controllers/DashboardController.php       # render shell + API stats
```

## Route
| Method | Path | Mô tả |
|--------|------|-------|
| GET  | `/dashboard` | Shell SPA |
| GET  | `/login`     | Form đăng nhập |
| POST | `/login`     | Xử lý đăng nhập |
| POST | `/logout`    | Đăng xuất |
| GET  | `/api/dashboard/stats` | JSON thống kê |

## Cách setup
1. Bật `modules/dashboard/module.json` → `"enabled": true` (mặc định đã bật).
2. Server chạy qua RoadRunner hoặc `php -S 127.0.0.1:8000 -t public public/index.php`.
3. Truy cập `/dashboard`.
4. Nếu `config('admin.auth.enabled', false)` = `false` (mặc định) → bỏ qua auth,
   vào thẳng dashboard. Bật `true` để yêu cầu đăng nhập.

## Build lại SPA (khi sửa code SolidJS)
```bash
# trong thư mục source của SPA (nếu có package.json)
npm install
npm run build
# output copy vào public/js/admin-solid-core.js + public/css/admin-*.css
```

## Mở rộng
- Module khác nhúng trường edit qua hook `edit.fields` của `ModuleContext`
  (xem module `seo`).
- Thêm API mới: đăng ký route trong `DashboardServiceProvider::boot()`.