<?php
define('OJ_NAME', 'Codgic');
define('OJ_COPYRIGHT', '(C) 2015 - 2017 Codgic Team and Contributors');
define('CONTACT_EMAIL', 'info@codgi.cf');

define('POSTMESSAGE_LOCK', '/tmp/codgic_message.lock');

define('DISPLAY_LANGUAGE', 'en_US');
define('ACCESS_REQUIRES_LOGIN', false);
define('REGISTER_REQUIRES_CONFIRMATION', false);

define('TIME_ZONE', 'PRC');
define('DAY_STARTS', 7);
define('NIGHT_STARTS', 20);

define('CWOJ_SCORE_MIN', 50);

define('MATHJAX_INLINE_BEGIN',  '[inline]');
define('MATHJAX_INLINE_END',  '[/inline]');
define('MATHJAX_TEX_BEGIN',  '[tex]');
define('MATHJAX_TEX_END',  '[/tex]');

define('DB_HOST', 'localhost');
define('DB_NAME', 'codgic');
define('DB_USER', 'codgic');
define('DB_PASSWORD', 'YOURPASSWORD');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_SERVER', '');
define('SMTP_PORT', 999);
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
define('SMTP_DISPLAY', 'Codgic');

define("GRAVATAR_CDN",  '//cdn.v2ex.com/gravatar');

define("COOKIE_KEY", "C0DG1C_HELLO_WORLD!");
define("COOKIE_EXPIRE",31536000);
// define("BIND_DOMAIN", '.example.com');

/**
* 2. Password Encryption
* ----------------
* Currently Codgic uses an RSA public key to encrypt user password.
* Please destory the private key immediately after generating a new RSA key. 
* Should use a hash here, What a fucking piece of shit
*/
define("PASSWORD_PUBLIC_KEY", "
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMa44v2O2oZIgXL2PtdoxjTJ4ASWfGTL
d4VZ05MzsQAbNnQ+abT4otHnK7n6Ku4WKCbDAd3FKcsnNSv0eVhehxUCAwEAAQ==
-----END PUBLIC KEY-----
");


