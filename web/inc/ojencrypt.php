<?php
class mycrypt {
    public $pubkey;
    public $privkey;
    function __construct() {
				require 'inc/ojsettings.php';
                $this->pubkey = file_get_contents($pub_dir); 
                $this->privkey = file_get_contents($pri_dir);
    }
    public function encrypt($data) {
        if (openssl_private_encrypt($data, $encrypted, $this->privkey))
            $data = base64_encode($encrypted);
        else
            throw new Exception('error');

        return $data;
    }
    public function decrypt($data) {
        if (openssl_public_decrypt(base64_decode($data), $decrypted, $this->pubkey))
            $data = $decrypted;
        else
            $data = 'error';

        return $data;
    }
}
$rsa = new mycrypt();
