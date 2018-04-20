<?php



class Conf {
    // Cos php sdk version number.
    const VERSION = 'v4.2';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    // Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
    const APP_ID = '1252036340';
    const SECRET_ID = 'AKIDLGyQHjmdiEFalmqMM2uUaLZCiTWLD65A';
    const SECRET_KEY = 'nZonLUji6FtuLAiqHCyX5aNo9lU79SdB';

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent() {
        return 'cos-php-sdk-' . self::VERSION;
    }
}
