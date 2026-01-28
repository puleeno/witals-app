<?php
// Mock wp-config.php for testing Zero Migrate strategy
define('DB_NAME', 'prestoworld_wp_legacy');
define('DB_USER', 'wp_user_legacy');
define('DB_PASSWORD', 'secret_legacy');
define('DB_HOST', '127.0.0.1:3307');
define('DB_CHARSET', 'utf8');
define('WP_DEBUG', true);

// Auth Keys & Salts (Complex characters test)
define('AUTH_KEY', 'QucD3FV{.D#a;J^;;Ar_vl870q#pj9hUP^[ 0YuL]Bt9^<H$qbkX:a(@v!DQo7?C');
define('SECURE_AUTH_KEY', 'Xmm;!;JX1h<9|L;mWOm>Wv+FD-?%B;y4!Kz+NDO+9<|l=4H7w8Ww0>N*[f&i>3Xl');
define('LOGGED_IN_KEY', 'K{^J|y80@.gpWYnCINcdlEi@-DWIg*f4iklS>_M<P0dsc/%KI{n![_3_G#.brX%k');
define('NONCE_KEY', '<Tzr&1OLOW@:khbnJ0C)hfQv7{P>Z<WXd4|)wk[t$]c1!fWkcv,]p:+Fs_R7%Nhi');
define('AUTH_SALT', '1u<EV{{kb7c(E-RWo1~kvIH,dFQKK..+cl?JCmNOQtH;i0iNIJ^<WcC6_ch8Gcbo');
define('SECURE_AUTH_SALT', '(u@gIMw2J<q#G#g0KTGtEHmRi:?]7BS]91-?Lt}FFob@i+5#h#vx%~F{*ofukX-H');
define('LOGGED_IN_SALT', 'C8_Y4k*.2_[>.XT(o1)?@OOhSCekln?$=fX_xYj8b{o,zB@4DFL_7<8g]acbE`uH');
define('NONCE_SALT', '8sH}T#`6YMB{TW~f?bp1M|1>:B{of~zDlhy0e=&3is=mQDFZN&+;-vJ5gI19hQFi');

// Some extra WP constants
define('WP_HOME', 'http://localhost:8080');
define('WP_SITEURL', 'http://localhost:8080');
$table_prefix = 'wp_legacy_';
