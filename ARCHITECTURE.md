# Witals Framework - Architecture Overview

## 🏗️ Kiến trúc tổng quan

Framework được thiết kế để chạy song song trên cả **RoadRunner** (long-running) và **Traditional Web Server** (short-lived) với các design patterns hỗ trợ đầy đủ.

## 📐 Design Patterns

### 1. **Adapter Pattern** - Server Adapter Detection

Framework tự động detect môi trường và khởi tạo các components phù hợp:

```php
// Tự động detect
$app->setRoadRunnerMode($isRoadRunner);

// Các managers tự động khởi tạo đúng loại
$stateManager = $app->state();     // StatelessManager hoặc StatefulManager
$lifecycle = $app->lifecycle();     // TraditionalLifecycle hoặc RoadRunnerLifecycle
```

### 2. **Factory Pattern** - Component Creation

Sử dụng factories để tạo components phù hợp với môi trường:

```php
// State Manager Factory
$stateManager = StateManagerFactory::create($app);

// Lifecycle Factory
$lifecycle = LifecycleFactory::create($app);
```

### 3. **Strategy Pattern** - State Management

Hai strategies khác nhau cho state management:

**Stateless Strategy** (Traditional):
- State chỉ tồn tại trong 1 request
- Tự động clear khi request kết thúc
- Không lo memory leaks

**Stateful Strategy** (RoadRunner):
- Request-scoped state: Clear sau mỗi request
- Persistent state: Tồn tại qua nhiều requests
- Garbage collection tự động

### 4. **Template Method Pattern** - Lifecycle Hooks

Lifecycle managers định nghĩa template cho request lifecycle:

```php
interface LifecycleManager {
    public function onBoot(): void;
    public function onRequestStart(Request $request): void;
    public function onRequestEnd(Request $request, Response $response): void;
    public function onTerminate(): void;
}
```

Mỗi implementation có cách xử lý khác nhau:
- **TraditionalLifecycle**: Gọi tất cả hooks mỗi request
- **RoadRunnerLifecycle**: Boot once, loop request hooks

### 5. **Singleton Pattern** - Application Instance

Application sử dụng singleton pattern cho services:

```php
$app->singleton(Kernel::class, HttpKernel::class);
$app->instance(StateManager::class, $stateManager);
```

## 🔄 Lifecycle Flow

### Traditional Web Server
```
┌─────────────────────────────────────────┐
│ Request                                 │
│                                         │
│ 1. Process Start                        │
│ 2. Bootstrap App                        │
│ 3. onBoot()          ← Boot services    │
│ 4. onRequestStart()  ← Init request     │
│ 5. handle()          ← Process request  │
│ 6. onRequestEnd()    ← Cleanup          │
│ 7. onTerminate()     ← Final cleanup    │
│ 8. Process Dies                         │
│                                         │
└─────────────────────────────────────────┘
```

### RoadRunner Worker
```
┌─────────────────────────────────────────┐
│ Worker Lifetime                         │
│                                         │
│ 1. Worker Start                         │
│ 2. Bootstrap App                        │
│ 3. onBoot()          ← Boot ONCE        │
│                                         │
│ ┌─────────────────────────────────┐     │
│ │ Request Loop (nhiều lần)        │     │
│ │                                 │     │
│ │ 4. onRequestStart() ← Reset     │     │
│ │ 5. handle()         ← Process   │     │
│ │ 6. onRequestEnd()   ← Cleanup   │     │
│ │                                 │     │
│ │ (Repeat 4-6 nhiều lần)          │     │
│ └─────────────────────────────────┘     │
│                                         │
│ 7. onTerminate()     ← Worker shutdown  │
│ 8. Worker Dies                          │
│                                         │
└─────────────────────────────────────────┘
```

## 🗂️ Cấu trúc thư mục

```
witals-app/
├── app/
│   ├── Application.php              # Core application
│   ├── Contracts/                   # Interfaces
│   │   ├── Http/
│   │   │   └── Kernel.php          # HTTP Kernel interface
│   │   ├── StateManager.php        # State manager interface
│   │   └── LifecycleManager.php    # Lifecycle interface
│   ├── Http/                        # HTTP layer
│   │   ├── Kernel.php              # HTTP kernel implementation
│   │   ├── Request.php             # Request wrapper
│   │   ├── Response.php            # Response wrapper
│   │   └── KernelStateDemoTrait.php
│   ├── State/                       # State management
│   │   ├── StatelessManager.php    # Traditional state
│   │   ├── StatefulManager.php     # RoadRunner state
│   │   ├── StateManagerFactory.php
│   │   └── README.md
│   └── Lifecycle/                   # Lifecycle management
│       ├── TraditionalLifecycle.php
│       ├── RoadRunnerLifecycle.php
│       ├── LifecycleFactory.php
│       └── README.md
├── bootstrap/
│   └── app.php                      # Application bootstrap
├── public/
│   ├── index.php                    # Traditional entry point
│   ├── .htaccess                    # Apache config
│   └── nginx.conf.example           # Nginx config
├── worker.php                       # RoadRunner worker
├── .rr.yaml                         # RoadRunner config
├── .env.example                     # Environment template
└── composer.json                    # Dependencies
```

## 🎯 Key Concepts

### 1. Environment Detection
```php
if ($app->isRoadRunner()) {
    // Long-running worker mode
    // - Boot once
    // - Reuse services
    // - Manage memory carefully
} else {
    // Traditional mode
    // - Boot every request
    // - Fresh process
    // - No memory concerns
}
```

### 2. State Management
```php
$state = $app->state();

// Works in both modes
$state->set('key', 'value');
$value = $state->get('key');

// RoadRunner only - persistent across requests
if ($state->isStateful()) {
    $state->setPersistent('config', $data);
}
```

### 3. Lifecycle Hooks
```php
// Automatically called by framework
$lifecycle->onBoot();              // Once per worker (RR) or request (Traditional)
$lifecycle->onRequestStart($req);  // Before each request
$lifecycle->onRequestEnd($req, $res); // After each request
$lifecycle->onTerminate();         // End of worker (RR) or request (Traditional)
```

## 🚀 Performance Optimizations

### RoadRunner Mode
1. **Boot Once**: Services boot 1 lần, reuse cho tất cả requests
2. **Connection Pooling**: Database connections persist
3. **Compiled Assets**: Routes, views compile 1 lần
4. **Memory Management**: Automatic GC, health checks
5. **Hot Reload**: Auto reload khi code thay đổi

### Traditional Mode
1. **Simple**: Không cần quản lý state phức tạp
2. **Isolated**: Mỗi request độc lập
3. **Safe**: Không lo memory leaks
4. **Compatible**: Chạy mọi nơi

## 📊 Monitoring

### Worker Stats (RoadRunner)
```php
$stats = $app->lifecycle()->getWorkerStats();
// Returns: uptime, requests_handled, memory_usage, etc.
```

### State Stats
```php
$stats = $app->state()->getStats();
// Returns: request_state_count, persistent_state_count, memory, etc.
```

## 🔧 Extensibility

### Custom Lifecycle
```php
class CustomLifecycle extends RoadRunnerLifecycle
{
    protected function bootServices(): void
    {
        parent::bootServices();
        // Custom boot logic
    }
}
```

### Custom State Manager
```php
class CachedStateManager extends StatefulManager
{
    // Add Redis/Memcached support
}
```

## 📚 Documentation

- [State Management](app/State/README.md) - Chi tiết về state management
- [Lifecycle Management](app/Lifecycle/README.md) - Chi tiết về lifecycle
- [README.md](README.md) - Hướng dẫn sử dụng

## 🎓 Best Practices

1. **Always check environment**:
   ```php
   if ($app->isRoadRunner()) {
       // RoadRunner-specific code
   }
   ```

2. **Use lifecycle hooks properly**:
   - Boot expensive operations in `onBoot()`
   - Reset state in `onRequestStart()`
   - Cleanup in `onRequestEnd()`

3. **Manage state correctly**:
   - Use request-scoped for user data
   - Use persistent for app config (RR only)

4. **Monitor performance**:
   - Check worker stats
   - Monitor memory usage
   - Track request metrics

## 🔐 Security

1. **State Isolation**: Request state tự động clear (RR)
2. **No State Leakage**: Globals reset giữa requests
3. **Memory Limits**: Auto restart workers khi vượt limit
4. **Request Limits**: Max requests per worker
