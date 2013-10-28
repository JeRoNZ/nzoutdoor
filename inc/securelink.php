<?php

class SecureLink {
    const algorithm = "tripledes";
    const mode = "ecb";
    const passphrase = "Sh00t2K!ll";

    static function setToken($array,$encode=true){
        $td = mcrypt_module_open(self::algorithm, "", self::mode, "");
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, self::passphrase, $iv);
        $encryptedString = mcrypt_generic($td, serialize($array));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $token = base64_encode($encryptedString);
        if ($encode)
            return urlencode($token);
        return $token;
    }

    static function getToken($token) {
        $td = mcrypt_module_open(self::algorithm, "", self::mode, "");
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, self::passphrase, $iv);
        $decryptedString = mcrypt_decrypt(self::algorithm, self::passphrase, base64_decode($token), self::mode, $iv);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        if (($array = unserialize($decryptedString)) === false)
            return false;
        return $array;
    }
}

?>
