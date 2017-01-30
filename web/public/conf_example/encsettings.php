<?php
/**
* Codgic Encryption Configuration File
* =======================
* This configuration file defines serval encryption keys. 
* PLEASE ALWAYS KEEP IT IN A SAFE PLACE!
* 1. Cookie Encryption
* ----------------
* "cookie_key" defines the encryption key of the cookies. Please change it into a random string.
* "cookie_expire" defines the expiration time of the cookies.
* "bind_domain" defines the specific domain you want to bind cookies to. COMMENT IT IF YOU DON'T NEED IT.
*/
define("cookie_key","C0DG1C_HELLO_WORLD!");
define("cookie_expire",31536000);
//define("bind_domain", '.example.com');

/**
* 2. Password Encryption
* ----------------
* Currently Codgic uses an RSA public key to encrypt user password.
* Please destory the private key immediately after generating a new RSA key. 
*/
define("PUBLIC_KEY", "
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMa44v2O2oZIgXL2PtdoxjTJ4ASWfGTL
d4VZ05MzsQAbNnQ+abT4otHnK7n6Ku4WKCbDAd3FKcsnNSv0eVhehxUCAwEAAQ==
-----END PUBLIC KEY-----
");