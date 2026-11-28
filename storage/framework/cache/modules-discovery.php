<?php return array (
  'metadata' => 
  array (
    'auto-index' => 
    array (
      'type' => 'feature',
      'priority' => 30,
      'enabled' => true,
      'description' => 'Auto index: submits URLs to Google Index API and tracks submission status. Defensive no-op without credentials.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\AutoIndex\\Providers\\AutoIndexServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'auto-index.service',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\AutoIndex\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'auto-index',
      'version' => '1.0.0',
      'depends' => 
      array (
        0 => 'seo',
      ),
      '_type' => 'feature',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/auto-index',
    ),
    'category' => 
    array (
      'type' => 'data',
      'priority' => 44,
      'enabled' => true,
      'description' => 'Danh mục (category) — quản lý từ dashboard qua generic CRUD (pk id_category).',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'category.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Category\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'category',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'data_type' => 'Danh mục',
        'group' => 'Nội dung',
        'group_icon' => '📂',
        'nav' => 
        array (
          'label' => 'Danh mục',
          'url' => '/dashboard/category',
          'icon' => '📂',
        ),
        'crud' => 
        array (
          'table' => 'category',
          'pk' => 'id_category',
          'title' => 'Danh mục',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'url',
            2 => 'active',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'parentid',
            2 => 'active',
            3 => 'thu_tu',
            4 => 'url',
            5 => 'title',
            6 => 'keywords',
            7 => 'description',
            8 => 'data_type',
            9 => 'lang',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/category',
    ),
    'comment' => 
    array (
      'type' => 'data',
      'priority' => 43,
      'enabled' => true,
      'description' => 'Product comments — submit handler (POST /comment/post). Render list still in legacy product page.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Comment\\Providers\\CommentServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'comment.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Comment\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'comment',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/comment',
    ),
    'contact' => 
    array (
      'type' => 'data',
      'priority' => 41,
      'enabled' => true,
      'description' => 'Contact form submissions — replaces legacy contact module (POST /contact/submit).',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Contact\\Providers\\ContactServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'contact.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Contact\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'contact',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'data_type' => 'Liên hệ',
        'group_icon' => '✉️',
        'nav' => 
        array (
          'label' => 'Liên hệ',
          'url' => '/dashboard/contact',
          'icon' => '✉️',
        ),
        'crud' => 
        array (
          'table' => 'contact',
          'pk' => 'id_contact',
          'title' => 'Liên hệ',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'email',
            2 => 'xem',
            3 => 'createdate',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'email',
            2 => 'content',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/contact',
    ),
    'dashboard' => 
    array (
      'type' => 'admin',
      'priority' => 10,
      'enabled' => true,
      'description' => 'Headless-CMS style admin dashboard (SPA shell served by RoadRunner).',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Dashboard\\Providers\\DashboardServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '/dashboard',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'dashboard.context',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Dashboard\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'dashboard',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      '_type' => 'admin',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/dashboard',
    ),
    'ecommerce' => 
    array (
      'type' => 'data',
      'priority' => 47,
      'enabled' => true,
      'description' => 'Ecommerce — dữ liệu sản phẩm, danh mục sản phẩm, giỏ hàng và thêm giỏ hàng. Path có thể đổi qua config.paths.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Ecommerce\\Providers\\EcommerceServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'ecommerce.data',
        1 => 'ecommerce.cart',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Ecommerce\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'ecommerce',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'config' => 
      array (
        'paths' => 
        array (
          'products' => '/san-pham',
          'cart' => '/gio-hang',
          'addcart' => '/addcart',
        ),
      ),
      'admin' => 
      array (
        'data_type' => 'Sản phẩm',
        'group_icon' => '🛒',
        'group' => 'Nội dung',
        'nav' => 
        array (
          'label' => 'Ecommerce',
          'url' => '/dashboard/product',
          'icon' => '🛒',
        ),
        'crud' => 
        array (
          'table' => 'product',
          'pk' => 'id_product',
          'title' => 'Sản phẩm',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'price',
            2 => 'active',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'id_category',
            2 => 'price',
            3 => 'km',
            4 => 'pricekm',
            5 => 'active',
            6 => 'intro',
            7 => 'content',
            8 => 'image',
            9 => 'url',
            10 => 'ma',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/ecommerce',
    ),
    'email' => 
    array (
      'type' => 'support',
      'priority' => 10,
      'enabled' => false,
      'description' => 'Email delivery service — provides mail sender for other modules',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Email\\Providers\\EmailServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'email.sender',
        1 => 'email.template',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Email\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'email',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'consumes' => 
      array (
      ),
      '_type' => 'support',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/email',
    ),
    'faq' => 
    array (
      'type' => 'data',
      'priority' => 42,
      'enabled' => true,
      'description' => 'FAQ / customer questions — replaces legacy sendquestion submit (POST /faq/send).',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Faq\\Providers\\FaqServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'faq.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Faq\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'faq',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/faq',
    ),
    'home' => 
    array (
      'type' => 'data',
      'priority' => 60,
      'enabled' => true,
      'description' => 'Trang chủ động — render theo danh sách section cấu hình (config.sections). Mỗi section là một component độc lập, có thể là HTML thô, view bất kỳ, danh sách sản phẩm/tin tức, slideshow... Dễ mở rộng.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Home\\Providers\\HomeServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'home.page',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Home\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'home',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'config' => 
      array (
        'path' => '/',
        'sections' => 
        array (
          0 => 
          array (
            'type' => 'cathome',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home',
    ),
    'news' => 
    array (
      'type' => 'data',
      'priority' => 45,
      'enabled' => true,
      'description' => 'Tin tức (news) — public list controller render bằng Stempler + dashboard CRUD.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\News\\Providers\\NewsServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'news.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\News\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'news',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'data_type' => 'Tin tức',
        'group_icon' => '📰',
        'group' => 'Nội dung',
        'nav' => 
        array (
          'label' => 'Tin tức',
          'url' => '/dashboard/news',
          'icon' => '📰',
        ),
        'crud' => 
        array (
          'table' => 'news',
          'pk' => 'id_news',
          'title' => 'Tin tức',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'url',
            2 => 'active',
            3 => 'ngay_dang',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'title',
            2 => 'url',
            3 => 'active',
            4 => 'intro',
            5 => 'content',
            6 => 'keywords',
            7 => 'description',
            8 => 'image',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/news',
    ),
    'newsletter' => 
    array (
      'type' => 'data',
      'priority' => 40,
      'enabled' => true,
      'description' => 'Newsletter subscribers — data module managed from the dashboard via generic CRUD.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Newsletter\\Providers\\NewsletterServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'newsletter.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Newsletter\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'newsletter',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'data_type' => 'Marketing',
        'group_icon' => '📣',
        'group' => 'Marketing',
        'nav' => 
        array (
          'label' => 'Newsletter',
          'url' => '/dashboard/newsletter',
          'icon' => '✉️',
        ),
        'crud' => 
        array (
          'table' => 'newsletter',
          'title' => 'Đăng ký nhận tin',
          'title_field' => 'email',
          'list_fields' => 
          array (
            0 => 'email',
            1 => 'phone',
            2 => 'created_at',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/newsletter',
    ),
    'product' => 
    array (
      'type' => 'data',
      'priority' => 46,
      'enabled' => false,
      'description' => 'Sản phẩm (product) — list + detail render bằng Stempler + dashboard CRUD.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Product\\Providers\\ProductServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'product.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Product\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'product',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'group' => 'Nội dung',
        'group_icon' => '📦',
        'nav' => 
        array (
          'label' => 'Sản phẩm',
          'url' => '/dashboard/product',
          'icon' => '📦',
        ),
        'crud' => 
        array (
          'table' => 'product',
          'pk' => 'id_product',
          'title' => 'Sản phẩm',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'price',
            2 => 'active',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'id_category',
            2 => 'price',
            3 => 'km',
            4 => 'pricekm',
            5 => 'active',
            6 => 'intro',
            7 => 'content',
            8 => 'image',
            9 => 'url',
            10 => 'ma',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/product',
    ),
    'sample' => 
    array (
      'type' => 'route',
      'priority' => 50,
      'enabled' => true,
      'description' => 'Example route module — handles /api/sample/* endpoints',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Sample\\Providers\\SampleServiceProvider',
      ),
      'routes' => 
      array (
        0 => 
        array (
          'method' => 'GET',
          'path' => '/hello',
          'handler' => 
          array (
            0 => 'Modules\\Sample\\Controllers\\SampleController',
            1 => 'hello',
          ),
        ),
        1 => 
        array (
          'method' => 'GET',
          'path' => '/hello/{name}',
          'handler' => 
          array (
            0 => 'Modules\\Sample\\Controllers\\SampleController',
            1 => 'greet',
          ),
        ),
      ),
      'route_prefix' => '/api/sample',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'sample.greeting',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Sample\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
        'greeter' => 
        array (
          'description' => 'Greeting functions',
          'type' => 'route',
          'routes' => 
          array (
            0 => 
            array (
              'method' => 'GET',
              'path' => '/greet/{name}',
              'handler' => 
              array (
                0 => 'Modules\\Sample\\Controllers\\SampleController',
                1 => 'greet',
              ),
            ),
          ),
          'provides' => 
          array (
            0 => 'sample.greeter',
          ),
        ),
        'admin' => 
        array (
          'description' => 'Admin functions',
          'type' => 'route',
          'route_prefix' => '/admin',
          'routes' => 
          array (
            0 => 
            array (
              'method' => 'GET',
              'path' => '/dashboard',
              'handler' => 
              array (
                0 => 'Modules\\Sample\\Controllers\\SampleController',
                1 => 'hello',
              ),
            ),
          ),
          'functions' => 
          array (
            'users' => 
            array (
              'description' => 'User management',
              'routes' => 
              array (
                0 => 
                array (
                  'method' => 'GET',
                  'path' => '/users',
                  'handler' => 
                  array (
                    0 => 'Modules\\Sample\\Controllers\\SampleController',
                    1 => 'hello',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'sample',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'consumes' => 
      array (
      ),
      '_type' => 'route',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample',
    ),
    'sample.greeter' => 
    array (
      'description' => 'Greeting functions',
      'type' => 'route',
      'routes' => 
      array (
        0 => 
        array (
          'method' => 'GET',
          'path' => '/greet/{name}',
          'handler' => 
          array (
            0 => 'Modules\\Sample\\Controllers\\SampleController',
            1 => 'greet',
          ),
        ),
      ),
      'provides' => 
      array (
        0 => 'sample.greeter',
      ),
      'name' => 'sample.greeter',
      'enabled' => true,
      'priority' => 50,
      '_function' => true,
      '_module' => 'sample',
      '_chain' => 
      array (
        0 => 'greeter',
      ),
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample',
      '_parent' => NULL,
      'route_prefix' => '',
    ),
    'sample.admin' => 
    array (
      'description' => 'Admin functions',
      'type' => 'route',
      'route_prefix' => '/admin',
      'routes' => 
      array (
        0 => 
        array (
          'method' => 'GET',
          'path' => '/dashboard',
          'handler' => 
          array (
            0 => 'Modules\\Sample\\Controllers\\SampleController',
            1 => 'hello',
          ),
        ),
      ),
      'name' => 'sample.admin',
      'enabled' => true,
      'priority' => 50,
      '_function' => true,
      '_module' => 'sample',
      '_chain' => 
      array (
        0 => 'admin',
      ),
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample',
      '_parent' => NULL,
    ),
    'sample.admin.users' => 
    array (
      'description' => 'User management',
      'routes' => 
      array (
        0 => 
        array (
          'method' => 'GET',
          'path' => '/users',
          'handler' => 
          array (
            0 => 'Modules\\Sample\\Controllers\\SampleController',
            1 => 'hello',
          ),
        ),
      ),
      'name' => 'sample.admin.users',
      'type' => 'support',
      'enabled' => true,
      'priority' => 50,
      '_function' => true,
      '_module' => 'sample',
      '_chain' => 
      array (
        0 => 'admin',
        1 => 'users',
      ),
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample',
      '_parent' => 'sample.admin',
      'route_prefix' => '',
    ),
    'schema-org' => 
    array (
      'type' => 'feature',
      'priority' => 21,
      'enabled' => true,
      'description' => 'schema.org JSON-LD structured data injected into <head> via the shared render.head hook.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\SchemaOrg\\Providers\\SchemaOrgServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'schema-org.render',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\SchemaOrg\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'schema-org',
      'version' => '1.0.0',
      'depends' => 
      array (
        0 => 'seo',
      ),
      '_type' => 'feature',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/schema-org',
    ),
    'seo' => 
    array (
      'type' => 'feature',
      'priority' => 20,
      'enabled' => true,
      'description' => 'SEO metadata: edit-screen fields + <head> rendering via shared ModuleContext hooks.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\Seo\\Providers\\SeoServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'seo.service',
        1 => 'seo.edit_fields',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\Seo\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'seo',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      '_type' => 'feature',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/seo',
    ),
    'static-pages' => 
    array (
      'type' => 'data',
      'priority' => 46,
      'enabled' => true,
      'description' => 'Trang tĩnh (static pages) — tái dùng category (data_type=info) + bảng info. CRUD dashboard + render frontend thay legacy info.php.',
      'namespace' => '',
      'entry' => 'Module.php',
      'providers' => 
      array (
        0 => 'Modules\\StaticPages\\Providers\\StaticPagesServiceProvider',
      ),
      'routes' => 
      array (
      ),
      'route_prefix' => '',
      'requires' => 
      array (
        'php' => '^8.1',
      ),
      'dependencies' => 
      array (
      ),
      'provides' => 
      array (
        0 => 'static-pages.data',
      ),
      'autoload' => 
      array (
        'psr-4' => 
        array (
          'Modules\\StaticPages\\' => 'src/',
        ),
      ),
      'functions' => 
      array (
      ),
      'keywords' => 
      array (
      ),
      'composer' => false,
      'name' => 'static-pages',
      'version' => '1.0.0',
      'depends' => 
      array (
      ),
      'admin' => 
      array (
        'data_type' => 'Trang tĩnh',
        'group_icon' => '📄',
        'group' => 'Nội dung',
        'nav' => 
        array (
          'label' => 'Trang tĩnh',
          'url' => '/dashboard/static-pages',
          'icon' => '📄',
        ),
        'crud' => 
        array (
          'table' => 'info',
          'pk' => 'id_info',
          'title' => 'Trang tĩnh',
          'title_field' => 'name',
          'list_fields' => 
          array (
            0 => 'name',
            1 => 'url',
            2 => 'id_category',
            3 => 'active',
            4 => 'thu_tu',
          ),
          'form_fields' => 
          array (
            0 => 'name',
            1 => 'title',
            2 => 'url',
            3 => 'id_category',
            4 => 'active',
            5 => 'intro',
            6 => 'content',
            7 => 'keywords',
            8 => 'description',
            9 => 'image',
            10 => 'thu_tu',
          ),
        ),
      ),
      '_type' => 'data',
      '_path' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/static-pages',
    ),
  ),
  '_paths' => 
  array (
    0 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules',
    1 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/framework/witals/modules',
    2 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/framework/presto/Modules',
  ),
  'classmap' => 
  array (
    'Modules\\AutoIndex\\Controllers\\AutoIndexController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/auto-index/src//Controllers/AutoIndexController.php',
    'Modules\\AutoIndex\\Providers\\AutoIndexServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/auto-index/src//Providers/AutoIndexServiceProvider.php',
    'Modules\\AutoIndex\\Services\\AutoIndexService' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/auto-index/src//Services/AutoIndexService.php',
    'Modules\\Comment\\Controllers\\CommentController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/comment/src//Controllers/CommentController.php',
    'Modules\\Comment\\Providers\\CommentServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/comment/src//Providers/CommentServiceProvider.php',
    'Modules\\Contact\\Controllers\\ContactController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/contact/src//Controllers/ContactController.php',
    'Modules\\Contact\\Events\\ContactSubmitted' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/contact/src//Events/ContactSubmitted.php',
    'Modules\\Contact\\Listeners\\SendContactMail' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/contact/src//Listeners/SendContactMail.php',
    'Modules\\Contact\\Providers\\ContactServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/contact/src//Providers/ContactServiceProvider.php',
    'Modules\\Dashboard\\Controllers\\DashboardController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/dashboard/src//Controllers/DashboardController.php',
    'Modules\\Dashboard\\Providers\\DashboardServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/dashboard/src//Providers/DashboardServiceProvider.php',
    'Modules\\Ecommerce\\Controllers\\CartController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/ecommerce/src//Controllers/CartController.php',
    'Modules\\Ecommerce\\Controllers\\ProductController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/ecommerce/src//Controllers/ProductController.php',
    'Modules\\Ecommerce\\Providers\\EcommerceServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/ecommerce/src//Providers/EcommerceServiceProvider.php',
    'Modules\\Email\\Providers\\EmailServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/email/src//Providers/EmailServiceProvider.php',
    'Modules\\Email\\Services\\MailSender' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/email/src//Services/MailSender.php',
    'Modules\\Faq\\Controllers\\FaqController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/faq/src//Controllers/FaqController.php',
    'Modules\\Faq\\Providers\\FaqServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/faq/src//Providers/FaqServiceProvider.php',
    'Modules\\Home\\Controllers\\HomeController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Controllers/HomeController.php',
    'Modules\\Home\\Providers\\HomeServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Providers/HomeServiceProvider.php',
    'Modules\\Home\\Sections\\CathomeSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/CathomeSection.php',
    'Modules\\Home\\Sections\\HtmlSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/HtmlSection.php',
    'Modules\\Home\\Sections\\NewsSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/NewsSection.php',
    'Modules\\Home\\Sections\\ProductsSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/ProductsSection.php',
    'Modules\\Home\\Sections\\SectionInterface' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/SectionInterface.php',
    'Modules\\Home\\Sections\\SectionRegistry' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/SectionRegistry.php',
    'Modules\\Home\\Sections\\SlideshowSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/SlideshowSection.php',
    'Modules\\Home\\Sections\\ViewSection' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/home/src//Sections/ViewSection.php',
    'Modules\\News\\Controllers\\NewsController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/news/src//Controllers/NewsController.php',
    'Modules\\News\\Providers\\NewsServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/news/src//Providers/NewsServiceProvider.php',
    'Modules\\Newsletter\\Controllers\\SubscribeController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/newsletter/src//Controllers/SubscribeController.php',
    'Modules\\Newsletter\\Providers\\NewsletterServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/newsletter/src//Providers/NewsletterServiceProvider.php',
    'Modules\\Product\\Controllers\\ProductController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/product/src//Controllers/ProductController.php',
    'Modules\\Product\\Providers\\ProductServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/product/src//Providers/ProductServiceProvider.php',
    'Modules\\Sample\\Controllers\\SampleController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample/src//Controllers/SampleController.php',
    'Modules\\Sample\\Providers\\SampleServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample/src//Providers/SampleServiceProvider.php',
    'Modules\\SchemaOrg\\Providers\\SchemaOrgServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/schema-org/src//Providers/SchemaOrgServiceProvider.php',
    'Modules\\SchemaOrg\\Services\\SchemaOrgService' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/schema-org/src//Services/SchemaOrgService.php',
    'Modules\\Seo\\Providers\\SeoServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/seo/src//Providers/SeoServiceProvider.php',
    'Modules\\Seo\\Services\\SeoService' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/seo/src//Services/SeoService.php',
    'Modules\\StaticPages\\Controllers\\StaticPagesController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/static-pages/src//Controllers/StaticPagesController.php',
    'Modules\\StaticPages\\Providers\\StaticPagesServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/static-pages/src//Providers/StaticPagesServiceProvider.php',
    'Modules\\StaticPages\\Repositories\\StaticPagesRepository' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/static-pages/src//Repositories/StaticPagesRepository.php',
  ),
);
