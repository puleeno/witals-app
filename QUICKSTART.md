# Quick Start Guide

## 🚀 Cài đặt nhanh

### 1. Install dependencies
```bash
composer install
```

### 2. Tạo file .env
```bash
cp .env.example .env
```

### 3. Chọn cách chạy

#### Option A: RoadRunner (Khuyến nghị - Performance cao)
```bash
# Download RoadRunner binary
composer rr:download

# Chạy development mode (với hot reload)
composer rr:serve:dev

# Hoặc production mode
composer rr:serve
```

Truy cập: http://localhost:8080

#### Option B: PHP Built-in Server (Đơn giản - Development)
```bash
composer serve
```

Truy cập: http://localhost:8000

#### Option C: Apache/Nginx (Production - Traditional)
1. Point document root → `public/`
2. Enable `.htaccess` (Apache) hoặc dùng `nginx.conf.example`
3. Truy cập qua domain của bạn

## 📍 Routes có sẵn

- `GET /` - Home page với thông tin hệ thống
- `GET /health` - Health check (JSON)
- `GET /info` - System information (JSON)
- `GET /state-demo` - State management demo (interactive)

## 🎯 Demo State Management

Truy cập `/state-demo` để xem sự khác biệt giữa:
- **Stateless** (Traditional): Counter reset mỗi request
- **Stateful** (RoadRunner): Persistent counter tăng qua các requests

## 🔍 Kiểm tra môi trường

Framework tự động detect và hiển thị:
- Environment type (RoadRunner vs Traditional)
- PHP version
- Server software
- Memory usage
- Uptime

## 📖 Tài liệu chi tiết

- [README.md](README.md) - Hướng dẫn đầy đủ
- [ARCHITECTURE.md](ARCHITECTURE.md) - Kiến trúc và design patterns
- [app/State/README.md](app/State/README.md) - State management
- [app/Lifecycle/README.md](app/Lifecycle/README.md) - Lifecycle management

## 🛠️ Development

### Hot Reload (RoadRunner)
Code tự động reload khi thay đổi file `.php` trong `app/` và `public/`:
```bash
composer rr:serve:dev
```

### Debug Mode
Set trong `.env`:
```env
APP_DEBUG=true
APP_ENV=development
```

## 🎨 Tùy chỉnh

### Thêm routes mới
Edit `app/Http/Kernel.php`:
```php
if ($path === '/my-route') {
    return $this->handleMyRoute($request);
}
```

### Sử dụng State
```php
$state = $app->state();
$state->set('key', 'value');
$value = $state->get('key');
```

### Sử dụng Lifecycle Hooks
```php
class CustomLifecycle extends RoadRunnerLifecycle
{
    protected function bootServices(): void
    {
        // Your custom boot logic
    }
}
```

## ⚡ Performance Tips

### RoadRunner Mode
- ✅ Services boot 1 lần, reuse cho tất cả requests
- ✅ Nhanh hơn 5-10x so với PHP-FPM
- ✅ Connection pooling tự động
- ✅ Hot reload trong development

### Traditional Mode
- ✅ Dễ debug
- ✅ Chạy trên shared hosting
- ✅ Không cần cài đặt thêm
- ✅ Compatible với mọi web server

## 🐛 Troubleshooting

### RoadRunner không start
```bash
# Re-download binary
composer rr:download

# Check config
cat .rr.yaml
```

### Port đã được sử dụng
Edit `.rr.yaml`:
```yaml
http:
  address: 127.0.0.1:8081  # Đổi port
```

### Memory issues
Edit `.rr.yaml`:
```yaml
http:
  pool:
    max_worker_memory: 256  # Tăng limit
```

## 📊 Monitoring

### Worker Stats (RoadRunner)
```php
$stats = $app->lifecycle()->getWorkerStats();
```

### Metrics Endpoint
```
http://localhost:2112/metrics
```

## 🎓 Next Steps

1. Đọc [ARCHITECTURE.md](ARCHITECTURE.md) để hiểu design patterns
2. Xem [State Management](app/State/README.md) để quản lý state
3. Tìm hiểu [Lifecycle](app/Lifecycle/README.md) để tối ưu performance
4. Bắt đầu code! 🚀
