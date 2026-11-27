<?php
/**
 * Legacy front-end runner.
 *
 * This file is `require`d from public/index.php **at global scope** on purpose:
 * the original endcms code (legacy/modules/*.php) is fully procedural and relies
 * on PHP's global symbol table. Keeping the include chain at global scope means
 * the 20+ year old templates/modules run unchanged.
 *
 * Everything that is *not* a public site page (dashboard, api, health, assets)
 * still goes through the normal Witals pipeline in public/index.php.
 */

use Witals\Framework\Application;

defined('_VALID_NVB') or define('_VALID_NVB', '1');

/* @var Application $app */
$app = $GLOBALS['__witals_app'] ?? null;

require __DIR__ . '/conf.php';

// The PHP built-in server (and PHP-FPM with some configs) runs with the
// document root as cwd; the legacy code resolves every relative path
// (templates/, lib/, modules/, uploaded/) from the project root.
chdir(dirname(__DIR__));

error_reporting(CMS_DEBUG ? (E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING) : 0);
ini_set('display_errors', CMS_DEBUG ? '1' : '0');
ini_set('memory_limit', '500M');

umask(0);

if (!function_exists('get_microtime')) {
    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ($usec + $sec);
    }
}

$start_time = get_microtime();

// ---------------------------------------------------------------------------
// Boot the legacy framework (port of initcms.php)
// ---------------------------------------------------------------------------

require __DIR__ . '/lib/mySQL.php';
require __DIR__ . '/lib/class.Template.inc.minify.php';
require __DIR__ . '/lib/topost.php';
require __DIR__ . '/lib/functions.php';
require __DIR__ . '/lib/link_pass.php';
require __DIR__ . '/lib/cat_tree.php';
require __DIR__ . '/lib/class_tree.php';
require __DIR__ . '/lib/imagelib/WideImage.php';
require __DIR__ . '/lib/class.php';
require __DIR__ . '/lib/configfunction.php';
require __DIR__ . '/lib/classurl.php';
require __DIR__ . '/lib/clsUrl.php';
require __DIR__ . '/lib/cropimage.php';
require __DIR__ . '/lib/define_module.php';
require __DIR__ . '/Mobile_Detect.php';
require __DIR__ . '/lang/lang.php';

// Database ---------------------------------------------------------------

$DB = new db_driver;
$DB->obj['sql_database'] = $config['dbname'];
$DB->obj['sql_user']     = $config['username'];
$DB->obj['sql_pass']     = $config['password'];
$DB->obj['sql_host']     = $config['host'];
$DB->connect();

$mysqli = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$mysqli->set_charset('utf8');
$DBi = new DBi();

// Settings ---------------------------------------------------------------

$CONFIG = [];
$a = $DB->query('select * from settings');
while ($b = @mysqli_fetch_array($a)) {
    $CONFIG[$b['setting_name']] = $b['setting_value'];
}

$CONFIG['allowed_mediatypes'] = str_replace(' ', '', $CONFIG['allowed_mediatypes']);
$CONFIG['allowed_mediatypes_array'] = explode(',', $CONFIG['allowed_mediatypes']);
$CONFIG['allowed_mediatypes_match'] = str_replace(',', '|', $CONFIG['allowed_mediatypes']);
$CONFIG['time_offset'] = intval($CONFIG['time_offset']) * 60 * 60;

if (!$CONFIG['document_root']) {
    $CONFIG['root_path'] = ($_SERVER['DOCUMENT_ROOT'] ?? '') . $CONFIG['dir_path'] . '/';
} else {
    $CONFIG['root_path'] = $CONFIG['document_root'] . $CONFIG['dir_path'] . '/';
}
$CONFIG['root_path_dev'] = substr($CONFIG['root_path'], 0, -1);

$sqlactive = 'active=1';

$clsUrl = new Url;
$address_url = [];
$guid_arr = explode(';', $CONFIG['guid']);
foreach ($guid_arr as $guid) {
    $address_url[] = strrev(base64_decode($guid));
}

// "settingshow" (hotline, social links, ...) ------------------------------

$SETTING = null;
$db = $DB->query('SELECT * FROM settingshow');
if ($rs = mysqli_fetch_array($db)) {
    $SETTING = json_decode($rs['content']);
}

// Language labels ---------------------------------------------------------

$LANG = null;
$db = $DB->query('SELECT * FROM lang WHERE id = 1');
if ($rs = mysqli_fetch_array($db)) {
    $LANG = json_decode($rs['lang'], true);
}

define('GZIPSUPPORT', extension_loaded('zlib'));

if (PHP_VERSION_ID >= 80100) {
    $PHP_SELF = $_SERVER['PHP_SELF'] ?? '';
}

header('P3P: CP="CAO DSP COR CURa ADMa DEVa OUR IND PHY ONL UNI COM NAV INT DEM PRE"');

// ---------------------------------------------------------------------------
// Routing (port of index.php)
// ---------------------------------------------------------------------------

session_start();

$mobile_temp = '';

// 301 redirects stored in the `direct` table
$redirects = [];
$db = $DB->query('SELECT * FROM direct');
while ($rs = mysqli_fetch_array($db)) {
    $redirects[$rs['url_old']] = $rs['url_new'];
}
foreach ($redirects as $nguon => $dich) {
    if (($_SERVER['REQUEST_URI'] ?? '') == trim($nguon)) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://' . ($_SERVER['HTTP_HOST'] ?? '') . trim($dich));
        exit;
    }
}

// Language prefix: /en/ /cn/ /kr/
$params = explode('/', (string)($_SERVER['REQUEST_URI'] ?? '/'));

if (($params[1] ?? '') === 'en') {
    $lang = 'en';
    $_GET['lang'] = 'en';
    $_SESSION['lang'] = 'en';
    $lang_dir = 'en/';
} elseif (($params[1] ?? '') === 'cn') {
    $lang = 'cn';
    $_GET['lang'] = 'cn';
    $_SESSION['lang'] = 'cn';
    $lang_dir = 'cn/';
} elseif (($params[1] ?? '') === 'kr') {
    $lang = 'kr';
    $_GET['lang'] = 'kr';
    $_SESSION['lang'] = 'kr';
    $lang_dir = 'kr/';
} else {
    $lang = '';
    $_GET['lang'] = '';
    $_SESSION['lang'] = '';
    $lang_dir = '';
}

if ($lang === 'en') {
    $language = " AND lang='en' ";
    $dklang = " AND lang='en' ";
} elseif ($lang === 'cn') {
    $language = " AND lang='cn' ";
    $dklang = " AND lang='cn' ";
} elseif ($lang === 'kr') {
    $language = " AND lang='kr' ";
    $dklang = " AND lang='kr' ";
} else {
    $language = " AND ( lang<>'en' AND lang<>'cn' AND lang<>'kr'  )";
    $dklang = " AND ( lang<>'en' AND lang<>'cn' AND lang<>'kr'  )";
}

if ($CONFIG['active_site'] != 1) {
    echo 'Website đang tạm đóng.';
    exit;
}

$page = '';
$page1 = '';
$uri = '';
$cateinfo = [];
$meta = [];
$url_filename = '';
$idc = 0;
$id = 0;
$p = 1;

if (!empty($_GET['page'])) {
    $page = $_GET['page'];
    $page1 = $_GET['page'];
} else {
    $getUrl = new url_process;
    $url_request = urldecode($_SERVER['REQUEST_URI'] ?? '/');
    $url_request = str_replace("'", '', $url_request);
    $url_request = str_replace('"', ' ', $url_request);

    $x = explode('?', $url_request);
    $uri = $x[0];
    $cateinfo = $getUrl->Url_Type($uri);
    $page1 = $cateinfo['page'] ?? '';
    $page = $page1;

    $_GET['id'] = intval($getUrl->get_id_item($uri));
    $_GET['id_product'] = intval($getUrl->get_id_item($uri));
    $_GET['idc'] = intval($cateinfo['id_category'] ?? 0);
    $_GET['keyword'] = $cateinfo['keyword'] ?? '';
    $_GET['title'] = $cateinfo['title'] ?? '';
    $_GET['p'] = intval($cateinfo['p'] ?? 0);
    $_GET['code'] = $cateinfo['code'] ?? '';

    $url_filename = $getUrl->get_filename($uri);
    $_GET['id'] = $getUrl->get_id_article($cateinfo['data_type'] ?? '', $url_filename);
    $idc = $_GET['idc'];
    $id = $_GET['id'];
    $p = $_GET['p'];

    if ($p && $p <= 0) {
        $p = 1;
        $_GET['p'] = 1;
    }

    $meta = $getUrl->get_meta_item($cateinfo['data_type'] ?? '');
}

if ($url_filename && intval($_GET['id']) == 0 && ($page1 !== 'cart')) {
    header('HTTP/1.1 404 Not Found');
    $page1 = '404';
    $page = '404';
}

if ($page1 === '' && strlen($uri) > 1) {
    header('HTTP/1.1 404 Not Found');
    $page1 = '404';
    $page = '404';
}

$detect = new Mobile_Detect;
if ($detect->isMobile()) {
    $mobile_temp = 'mobile_';
}

if ($page1 === '' || $page1 === null) {
    $page1 = 'home';
    $page = 'home';
}

// Make sure we never travel outside modules/
$pageFile = __DIR__ . '/modules/' . basename($page1) . '.php';

if (!is_file($pageFile)) {
    header('HTTP/1.1 404 Not Found');
    $pageFile = __DIR__ . '/modules/404.php';
    $page = '404';
}

// ---------------------------------------------------------------------------
// Render: header layout + page module + footer layout
// ---------------------------------------------------------------------------

if (CMS_DEBUG) {
    header('X-Legacy-Page: ' . $page . '; id=' . $id . '; idc=' . $idc . '; file=' . basename($pageFile));
}

// Shared entity context so modules (seo, schema.org, ...) know what is being
// rendered. Modules read this via ModuleContext and hook into render.head.
if ($app !== null && class_exists(\App\Foundation\Modules\ModuleContext::class)) {
    $moduleContext = $app->make(\App\Foundation\Modules\ModuleContext::class);
    $moduleContext->share('entity', [
        'url'  => $uri,
        'type' => $cateinfo['data_type'] ?? '',
        'id'   => $id,
    ]);
}

// Render into a buffer so modules can inject additional <head> tags before
// </head> without touching the legacy templates (output parity preserved when
// no module contributes anything).
ob_start();
require __DIR__ . '/modules/header.php';
require $pageFile;
require __DIR__ . '/modules/footer.php';
$rendered = (string) ob_get_clean();

if ($app !== null && isset($moduleContext) && $moduleContext instanceof \App\Foundation\Modules\ModuleContext) {
    $head = $moduleContext->render('render.head', ['url' => $uri]);
    if ($head !== '') {
        $pos = stripos($rendered, '</head>');
        if ($pos !== false) {
            $rendered = substr($rendered, 0, $pos) . $head . substr($rendered, $pos);
        }
    }
}

echo $rendered;

// endcms.php (gzip/flush + exit) is intentionally not included: public/index.php
// owns the shutdown of the request.
