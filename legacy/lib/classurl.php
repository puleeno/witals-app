<?php

//Develop Team: Binh Minh group
//binhminhgroup@gmail.com

defined('_VALID_NVB') or die('Direct Access to this location is not allowed.');

class url_process {

    public static function type_Info($type) {
        $str = array();
        switch ($type) {
            case 'news':
                $str['page'] = 'news';
                $str['tableitem'] = 'news';
                $str['id_item'] = 'id_news';
                $str['adminpage'] = 'news';
                $str['titlemenu'] = 'Tin tức';
                $str['data_type'] = "news";
                break;

            case 'tuvan':
                $str['page'] = 'tuvan';
                $str['tableitem'] = 'tuvan';
                $str['id_item'] = 'id_tuvan';
                $str['adminpage'] = 'tuvan';
                $str['titlemenu'] = 'Tư vấn';
                $str['data_type'] = "tuvan";
                break;

            case 'tuyendung':
                $str['page'] = 'tuyendung';
                $str['tableitem'] = 'tuyendung';
                $str['id_item'] = 'id_tuyendung';
                $str['adminpage'] = 'tuyendung';
                $str['titlemenu'] = 'Tuyển dụng';
                $str['data_type'] = "tuyendung";
                break;

            case 'service':
                $str['page'] = 'service';
                $str['tableitem'] = 'service';
                $str['id_item'] = 'id_service';
                $str['adminpage'] = 'service';
                $str['titlemenu'] = 'Dịch vụ';
                $str['data_type'] = "service";
                break;

            case 'article':
                $str['page'] = 'article';
                $str['tableitem'] = 'article';
                $str['id_item'] = 'id_article';
                $str['adminpage'] = 'article';
                $str['titlemenu'] = 'Bài viết';
                $str['data_type'] = "article";
                break;

            case 'du_an':
                $str['page'] = 'du_an';
                $str['tableitem'] = 'du_an';
                $str['id_item'] = 'id_duan';
                $str['adminpage'] = 'du_an';
                $str['titlemenu'] = 'Dự án';
                $str['data_type'] = "du_an";
                break;
            case 'product':
                $str['page'] = 'product';
                $str['tableitem'] = 'product';
                $str['id_item'] = 'id_product';
                $str['adminpage'] = 'product';
                $str['titlemenu'] = 'Sản phẩm';
                $str['data_type'] = "product";
                break;
            case 'video':
                $str['page'] = 'video';
                $str['tableitem'] = 'video';
                $str['id_item'] = 'id_video';
                $str['adminpage'] = 'video';
                $str['titlemenu'] = 'Video';
                $str['data_type'] = "video";
                break;
            case 'info':
                $str['page'] = 'info';
                $str['tableitem'] = 'info';
                $str['id_item'] = 'id_info';
                $str['adminpage'] = 'info';
                $str['titlemenu'] = 'Nội dung';
                $str['data_type'] = "info";
                break;

            case 'info_leader':
                $str['page'] = 'info_leader';
                $str['tableitem'] = 'info_leader';
                $str['id_item'] = 'id_info';
                $str['adminpage'] = 'info_leader';
                $str['titlemenu'] = 'Lãnh đạo';
                $str['data_type'] = "info_leader";
                break;

            case 'info_donvi':
                $str['page'] = 'info_donvi';
                $str['tableitem'] = 'info_donvi';
                $str['id_item'] = 'id_info';
                $str['adminpage'] = 'info_donvi';
                $str['titlemenu'] = 'Đơn vị thành viên';
                $str['data_type'] = "info_donvi";
                break;

            case 'daily':
                $str['page'] = 'daily';
                $str['tableitem'] = 'daily';
                $str['id_item'] = 'id_daily';
                $str['adminpage'] = 'daily';
                $str['titlemenu'] = 'Đại lý';
                $str['data_type'] = "daily";
                break;
            case 'logo':
                $str['page'] = 'logo';
                $str['tableitem'] = 'logo';
                $str['id_item'] = 'id_logo';
                $str['adminpage'] = 'logo';
                $str['titlemenu'] = 'logo';
                $str['data_type'] = "logo";
                break;


            case 'album':
                $str['page'] = 'album';
                $str['tableitem'] = 'album';
                $str['id_item'] = 'id_album';
                $str['adminpage'] = 'album';
                $str['titlemenu'] = 'Trang ảnh';
                $str['data_type'] = "album";
                break;

            case 'download':
                $str['page'] = 'download';
                $str['tableitem'] = 'download';
                $str['id_item'] = 'id_download';
                $str['adminpage'] = 'download';
                $str['titlemenu'] = 'trang download';
                $str['data_type'] = "download";
                break;

            case 'home':
                $str['page'] = 'home';
                $str['tableitem'] = '';
                $str['id_item'] = '';
                $str['adminpage'] = '';
                $str['titlemenu'] = 'Trang chủ';
                $str['data_type'] = "home";
                break;
            case 'contact':
                $str['page'] = 'contact';
                $str['tableitem'] = '';
                $str['id_item'] = '';
                $str['adminpage'] = 'contact';
                $str['titlemenu'] = 'Liên hệ';
                $str['data_type'] = "contact";
                break;
            case 'sendquestion':
                $str['page'] = 'sendquestion';
                $str['tableitem'] = '';
                $str['id_item'] = '';
                $str['adminpage'] = 'sendquestion';
                $str['titlemenu'] = 'Gửi câu hỏi';
                $str['data_type'] = "sendquestion";
                break;
            case 'faq':
                $str['page'] = 'faq';
                $str['tableitem'] = 'faq';
                $str['id_item'] = 'id_faq';
                $str['adminpage'] = 'faq';
                $str['titlemenu'] = 'Hỏi đáp';
                $str['data_type'] = "faq";
                break;
            case 'sitemap':
                $str['page'] = 'sitemap';
                $str['tableitem'] = '';
                $str['id_item'] = '';
                $str['adminpage'] = '';
                $str['titlemenu'] = 'sitemap';
                $str['data_type'] = "sitemap";
                break;
            case 'link':
                $str['page'] = '';
                $str['tableitem'] = '';
                $str['id_item'] = '';
                $str['adminpage'] = '';
                $str['titlemenu'] = 'liên kết';
                $str['data_type'] = "link";
                break;
        }
        return $str;
    }

    function cont_page($uri) {


        $page['p'] = 1;
        if (intval($this->get_number_page($uri)) > 0) {
            $page['p'] = intval($this->get_number_page($uri));
            $uri = substr($uri, 0, strlen($uri) - 1);
            $a = explode("/", $uri);
            $uri = substr($uri, 0, strlen($uri) - strlen(end($a)));
        }


        if ($this->get_dir($uri)) { // category
            switch ($this->get_dir($uri)) {


                case 'addcart/':
                    $page['page'] = 'cart';
                    break;

                case 'cart-success/':
                    $page['page'] = 'cart_success';
                    break;

				case 'payment/':
					$page['page'] = 'payment';
					break;

				case 'payment-result/':
					$page['page'] = 'payment_result';
					break;


				case 'contact-success/':
                    $page['page'] = 'contact_success';
                    break;


                case 'service-book/':
                    $page['page'] = 'service_book';
                    break;


                case 'search/':
                    $page['page'] = 'search';
                    break;
                case 'en/search/':
                    $page['page'] = 'search';
                    break;

                case 'signup/':
                    $page['page'] = 'signup';
                    break;
                case 'en/signup/':
                    $page['page'] = 'signup';
                    break;
                case 'signin/':
                    $page['page'] = 'signin';
                    break;
                case 'en/signin/':
                    $page['page'] = 'signin';
                    break;
				case 'tra-cuu-bao-hanh/':
                    $page['page'] = 'tracuubaohanh';
                    break;



                case 'sitemap/':
                    $page['page'] = 'sitemap';
                    break;
            }
        }
        return $page;
    }

    public function Url_Type($url) {
        global $DB, $dir_path, $dklang, $address_url, $site_address;

        $qPos = strpos($url, '?');
        $pathOnly = $qPos !== false ? substr($url, 0, $qPos) : $url;
        if ($pathOnly !== '' && $pathOnly !== '/' && substr($pathOnly, -1) !== '/' && strpos(basename($pathOnly), '.') === false) {
            $url = $pathOnly . '/' . ($qPos !== false ? substr($url, $qPos) : '');
        }

        $uri = $url;
        $cateinfo = array();
        $url = $this->get_dir($url);
        if ($url) {
            $cateinfo['p'] = 1;
            if (intval($this->get_number_page($url)) > 0) {
                $cateinfo['p'] = intval($this->get_number_page($url));
                $url = substr($url, 0, strlen($url) - 1);
                $a = explode("/", $url);
                $url = substr($url, 0, strlen($url) - strlen(end($a)));
            }
            $sql = "SELECT * FROM category WHERE url='" . $url . "' $dklang";
            $db = $DB->query($sql);
            if ($rs = mysqli_fetch_array($db)) {
                if ($rs['data_type'] == '') {
                    $cateinfo['data_type'] = 'home';
                } else {
                    $cateinfo['data_type'] = $rs['data_type'];
                }
                $cateinfo['id_category'] = $rs['id_category'];
                $cateinfo['name'] = $rs['name'];
                $cateinfo['description'] = $rs['description'];
                $cateinfo['keyword'] = $rs['keyword'];
                if ($rs['title']) {
                    $cateinfo['title'] = $rs['title'];
                } else {
                    $cateinfo['title'] = $rs['name'];
                }
                $cateinfo['template_name'] = $rs['template_name'];
                $page = $this->type_Info($rs['data_type']);
                $cateinfo['page'] = $page['page'];
            } else {
                $cateinfo = $this->cont_page($uri);
            }
        } else {
            $uri = $this->trimdirpath($uri);
            $cateinfo = $this->cont_page($uri);
        }
        return $cateinfo;
    }

    function trimdirpath($uri) {
        global $dir_path;
        $lendirpath = strlen($dir_path) + 1;
        $uri = substr($uri, $lendirpath, strlen($uri));
        return $uri;
    }

    public function get_dir($uri) {
        $uri = $this->trimdirpath($uri);
        $str = explode('/', $uri);
        $dir = substr($uri, 0, strlen($uri) - strlen(end($str)));
        return $dir;
    }

    function get_number_page($uri) {
        $uri = substr($uri, 0, strlen($uri) - 1);
        $str = explode('/', $uri);
        $strtrang = end($str);
        $x = explode('-', $strtrang);
        if ($x[0] == 'trang' || $x[0] == 'page') {
            return $x[1];
        }
    }

    function check_page($uri) {
        $uri = substr($uri, 0, strlen($uri) - 1);
        $str = explode('/', $uri);
        $strtrang = end($str);
        $x = explode('-', $strtrang);
        if ($x[0] == 'trang' || $x[0] == 'page') {
            return $x[0];
        }
    }

    function get_filename($uri) {
        global $dir_path;
        $lendirpath = strlen($dir_path);
        $uri = substr($uri, $lendirpath, strlen($uri));
        $str = explode('/', $uri);
        return $filename = end($str);
    }

    public function get_id_item($uri) {
        $filename = $this->get_filename($uri);
        $str = explode('_', $filename);
        $a = explode('.', end($str));
        return $a[0];
    }

    function get_id_article($data_type, $filename) {
        global $DB;
        $article = $this->type_Info($data_type);
        $table = $article['tableitem'];
        if ($table) {
            if ($filename) {
                $sql = "SELECT * FROM $table WHERE url='" . $filename . "'";
                $db = $DB->query($sql);
                if ($rs = mysqli_fetch_array($db)) {
                    return $rs[$article['id_item']];
                }
            }
        }
    }

    function get_meta_item($type) {
        global $DB, $id, $idc, $CONFIG, $dklang;
        $typeinfo = $this->type_Info($type);
        $str = array();
        if ($id) {
            if ($typeinfo['tableitem'] && $typeinfo['id_item']) {
                $sql = "SELECT * FROM " . $typeinfo['tableitem'] . " WHERE " . $typeinfo['id_item'] . "=" . $id;
                $db = $DB->query($sql);
                if ($rs = mysqli_fetch_array($db)) {
                    if ($rs['title']) {
                        //$str['title'] = strip_tags($rs['title']) . " - " . $CONFIG['site_name'];
                        $str['title'] = strip_tags($rs['title']);
                    } else {
                        //$str['title'] = strip_tags($rs['name']) . " - " . $CONFIG['site_name'];
                        $str['title'] = strip_tags($rs['name']);
                    }
                    if (strlen(strip_tags($rs['description'])) > 2) {
                        $str['description'] = strip_tags($rs['description']);
                    } elseif (strlen(strip_tags($rs['ttkhuyenmai'])) > 7) {
                        $str['description'] = strip_tags($rs['ttkhuyenmai']);
                    } elseif (strlen(strip_tags($rs['intro'])) > 7) {
                        $str['description'] = strip_tags($rs['intro']);
                    } elseif (strlen(strip_tags($rs['content'])) > 7) {
                        $str['description'] = strstrim(strip_tags($rs['content']), 160);
                    } else {
                        $str['description'] = strip_tags($CONFIG['site_description']);
                    }

                    if ($rs['keywords']) {
                        $str['keywords'] = $rs['keywords'];
                    } else {
                        $str['keywords'] = $CONFIG['site_keywords'];
                    }
                    if ($rs['image']) {
                        $str['image'] = $rs['image'];
                    }

					if ($rs['header_tag']) {
                        $str['header_tag'] = $rs['header_tag'];
                    }

					if ($rs['canonical']) {
                        $str['canonical'] = $rs['canonical'];
                    }

                }
            }
        } else {
            $sql = "SELECT * FROM category WHERE id_category=$idc $dklang";
            $db = $DB->query($sql);
            if ($rs = mysqli_fetch_array($db)) {
                if ($rs['title']) {
                    $str['title'] = strip_tags($rs['title']);
                } else {
                    $str['title'] = strip_tags($rs['name']);
                }
                if (strlen(strip_tags($rs['description'])) > 7) {
                    $str['description'] = strip_tags($rs['description']);
                } else {
                    $str['description'] = strip_tags($CONFIG['site_description']);
                }
                if ($rs['keywords']) {
                    $str['keywords'] = $rs['keywords'];
                } else {
                    $str['keywords'] = $CONFIG['site_keywords'];
                }
                if ($rs['image']) {
                    $str['image'] = $rs['image'];
                }
				if ($rs['header_tag']) {
					$str['header_tag'] = $rs['header_tag'];
				}
				if ($rs['canonical']) {
					$str['canonical'] = $rs['canonical'];
				}
            }
        }
        return $str;
    }

    public function builUrl($idc) {
        global $DB, $lang, $dir_path;
        $idc = intval($idc);
        $sql = "SELECT * FROM category WHERE id_category = $idc ";
        $db = $DB->query($sql);
        if ($rs = mysqli_fetch_array($db)) {
            $link = url_process::type_Info($rs['data_type']);
            if ($lang == 'jp') {
                $url = $dir_path . "/index.php?page=" . $link['page'] . "&idc=" . $rs['id_category'];
            } else {
                $url = $dir_path . "/" . $rs['url'];
            }
        }
        return $url;
    }

    public function builUrlArticle($id, $idc) {
        global $DB, $lang, $dir_path;
        $idc = intval($idc);
        $id = intval($id);
        if ($idc) {
            $item = url_process::type_Info(url_process::idCatToDataType($idc));
            $sql = "SELECT * FROM " . $item['tableitem'] . " WHERE " . $item['id_item'] . " = $id ";
            $db = $DB->query($sql);
            if ($rs = mysqli_fetch_array($db)) {
                if ($lang == 'jp') {
                    $url = $dir_path . "/index.php?page=" . $item['page'] . "&idc=" . $rs['id_category'] . "&id=$id";
                } else {
                    $url = $dir_path . "/" . url_process::getUrlCategory($idc) . $rs['url'];
                }
            }
        }
        return $url;
    }

    public static function idCatToDataType($idc) {
        global $DB;
        $idc = intval($idc);
        $sql = "SELECT * FROM category WHERE id_category = $idc";
        $db = $DB->query($sql);
        if ($rs = mysqli_fetch_array($db)) {
            return $rs['data_type'];
        }
    }

    public static function getUrlCategory($idc) {
        global $DB, $tpl, $lang, $dir_lang;
        $idc = intval($idc);
        $sql = "SELECT * FROM category WHERE id_category = $idc";
        $db = $DB->query($sql);
        if ($rs = mysqli_fetch_array($db)) {
            return $rs['url'];
        }
    }

    function check_link($table, $id_item, $id, $idc = 0) {
        global $DB;
        if ($table) {
            if ($idc == 0) {
                $sql = "SELECT url,id_category FROM $table WHERE $id_item = $id";
                $db = $DB->query($sql);
                if ($rs = mysqli_fetch_array($db)) {
                    return get_url_category($rs['id_category']) . $rs['url'];
                } else {
                    return '';
                }
            } else {
                $sql = "SELECT url FROM category WHERE id_category = $idc";
                $db = $DB->query($sql);
                if ($rs = mysqli_fetch_array($db)) {
                    return $rs['url'];
                } else {
                    return '';
                }
            }
        }
    }

}

class redirects301 {

    var $redirects = array();

    function redirects301() {
        global $redirects;
        /* if ( is_readable( './redirects_301.php' ) ){
          include_once  './redirects_301.php';
          $this->redirects = $redirects;
          } */
        $this->redirects = $redirects;
        $this->doRedirects();
    }

    function doRedirects() {
        if (!empty($this->redirects)) {
            foreach ($this->redirects as $target => $redirect) {
                // if ( false !== strpos( $_SERVER['REQUEST_URI'], $target ) ){
                if (trim($_SERVER['REQUEST_URI']) == trim($target)) {
                    $this->redirect(trim($redirect));
                }
            }
        }
    }

    function redirect($redirect) {
        $uri = $redirect;
        session_write_close();
        header("HTTP/1.0 301 Moved Permanently");
        header('Location: ' . $uri);
        exit;
    }

}

?>