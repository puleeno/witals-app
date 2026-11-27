<?php return array (
  'metadata' => 
  array (
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
  ),
  '_paths' => 
  array (
    0 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules',
    1 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/framework/witals/modules',
    2 => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/framework/presto/Modules',
  ),
  'classmap' => 
  array (
    'Modules\\Email\\Providers\\EmailServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/email/src//Providers/EmailServiceProvider.php',
    'Modules\\Email\\Services\\MailSender' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/email/src//Services/MailSender.php',
    'Modules\\Sample\\Controllers\\SampleController' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample/src//Controllers/SampleController.php',
    'Modules\\Sample\\Providers\\SampleServiceProvider' => '/Users/puleeno/Projects/xanhvina.com/xanhvina.com/modules/sample/src//Providers/SampleServiceProvider.php',
  ),
);
