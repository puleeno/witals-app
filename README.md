# Witals Framework

Framework PHP hiện đại có thể chạy song song trên cả **RoadRunner** và **Traditional Web Server** (Apache, Nginx, PHP Built-in Server).

## ✨ Tính năng

- 🚀 **Dual Runtime Support**: Chạy trên cả RoadRunner và traditional web servers
- ⚡ **High Performance**: Tận dụng RoadRunner worker pool để tăng hiệu suất
- 🔄 **Hot Reload**: Tự động reload khi code thay đổi (RoadRunner mode)
- 📊 **Metrics**: Built-in metrics endpoint
- 🎯 **PSR-7 Compatible**: Hỗ trợ PSR-7 HTTP messages
- 🛠️ **Environment Detection**: Tự động detect và tối ưu cho từng môi trường

## 📋 Yêu cầu

- PHP >= 8.1
- Composer
- (Optional) RoadRunner binary

## 🚀 Cài đặt

### 1. Clone và cài đặt dependencies

```bash
composer install
```

### 2. Cấu hình environment

```bash
cp .env.example .env
```

### 3. Download RoadRunner binary (nếu muốn dùng RoadRunner)

```bash
composer rr:download
```

## 🎯 Sử dụng

### Chạy với RoadRunner (Khuyến nghị cho production)

```bash
# Development mode với hot reload
composer rr:serve:dev

# Production mode
composer rr:serve
```

Truy cập: http://localhost:8080

### Chạy với PHP Built-in Server

```bash
composer serve
```

Truy cập: http://localhost:8000

### Chạy với Apache/Nginx

1. Point document root đến thư mục `public/`
2. Đảm bảo `.htaccess` được enable (Apache) hoặc cấu hình nginx rewrite rules
3. Truy cập qua domain/virtual host của bạn

## 📁 Cấu trúc thư mục

```
witals-app/
├── app/                    # Application code
│   ├── Application.php     # Core application class
│   ├── Contracts/          # Interfaces
│   └── Http/               # HTTP layer
│       ├── Kernel.php      # HTTP kernel
│       ├── Request.php     # Request wrapper
│       └── Response.php    # Response wrapper
├── bootstrap/              # Bootstrap files
│   └── app.php            # Application bootstrap
├── public/                 # Public directory
│   ├── index.php          # Traditional web server entry
│   └── .htaccess          # Apache configuration
├── worker.php             # RoadRunner worker entry
├── .rr.yaml               # RoadRunner configuration
├── .env.example           # Environment variables example
└── composer.json          # Dependencies
```

## 🔧 Cấu hình

### RoadRunner Configuration (`.rr.yaml`)

File `.rr.yaml` chứa cấu hình cho RoadRunner:
- HTTP server settings
- Worker pool configuration
- Hot reload settings
- Metrics endpoint

### Environment Variables (`.env`)

Cấu hình ứng dụng thông qua file `.env`:
- App settings
- RoadRunner settings
- Database configuration
- Cache & session settings

## 🌐 API Endpoints

- `GET /` - Home page với thông tin hệ thống
- `GET /health` - Health check endpoint
- `GET /info` - Detailed system information

## 🔍 Environment Detection

Framework tự động detect môi trường đang chạy:

```php
if ($app->isRoadRunner()) {
    // RoadRunner specific code
} else {
    // Traditional web server code
}
```

## ⚡ Performance Tips

### RoadRunner Mode
- Worker pool tái sử dụng PHP processes
- Không cần khởi tạo lại framework mỗi request
- Tốc độ nhanh hơn 5-10 lần so với PHP-FPM

### Traditional Mode
- Phù hợp cho shared hosting
- Dễ dàng debug
- Không cần cài đặt thêm

## 📊 Metrics

Khi chạy RoadRunner, metrics có sẵn tại:
```
http://localhost:2112/metrics
```

## 🛠️ Development

### Hot Reload

RoadRunner tự động reload khi file `.php` thay đổi trong thư mục `app/` và `public/`:

```yaml
reload:
  interval: 1s
  patterns: [".php"]
  services:
    http:
      dirs: ["app", "public"]
```

### Debug Mode

Set `APP_DEBUG=true` trong `.env` để enable debug mode.

## 📝 License

MIT License

## 👨‍💻 Author

Puleeno Nguyen <puleeno@gmail.com>
