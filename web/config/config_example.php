<?php
define('OJ_NAME', 'Codgic');
define('OJ_COPYRIGHT', '(C) 2015 - 2017 Codgic Team and Contributors');
define('CONTACT_EMAIL', 'info@codgi.cf');

/*
* Temporary Location
* ----------------
* You'll need to define a temporary directory for Codgic to store a temporary file named "Codgic_postmessage.lock"
* This acts as the mutex lock, which is necessary for message board to work.
* Please ensure that PHP has the permission to R/W in the directory you've defined.
*/
define('POSTMESSAGE_LOCK', '/tmp/codgic_message.lock');

define('DISPLAY_LANGUAGE', 'en_US');
// If this option is set to `true`, all users will have to login to access the whole website.
define('ACCESS_REQUIRES_LOGIN', false);
// If this option is set to `true`, all newly registered users will be disabled and have to be enabled manually.
define('REGISTER_REQUIRES_CONFIRMATION', false);

define('TIME_ZONE', 'PRC');

// Set the start and end for automated night mode switching.
define('DAY_STARTS', 7);
define('NIGHT_STARTS', 20);

// Set the score lower limit for the CODGIC contest.
define('CODGIC_SCORE_MIN', 50);

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

// Please change your cookie key to a random value.
define("COOKIE_KEY", "C0DG1C_HELLO_WORLD!");
define("COOKIE_EXPIRE", 31536000);
// define("BIND_DOMAIN", '.example.com');

/**
* 2. Password Encryption
* ----------------
* Currently Codgic uses an RSA public key to encrypt user password.
* Please destory the private key immediately after generating a new RSA key. 
* Should use a hash here, but we cannot change it now.
*/
define("PASSWORD_PUBLIC_KEY", "
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMa44v2O2oZIgXL2PtdoxjTJ4ASWfGTL
d4VZ05MzsQAbNnQ+abT4otHnK7n6Ku4WKCbDAd3FKcsnNSv0eVhehxUCAwEAAQ==
-----END PUBLIC KEY-----
");


