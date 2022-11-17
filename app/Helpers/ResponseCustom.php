<?php

namespace App\Helpers;

class ResponseCustom
{
    protected static $response = [];

    public static function success($data = null, $message = null)
    {
        self::$response = $data;
        return response()->json(self::$response, 200);
    }

    public static function error($error = null,$code = 500)
    {
        self::$response = $error;
        return response()->json(self::$response, $code);
    }
}
