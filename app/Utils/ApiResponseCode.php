<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class ApiResponseCode
{
    //application status codes
    const SUCCESS = 1000;
    const FAILED = 99;


    //Http Response code
    const HTTP_SUCCESS = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_VALIDATION_ERROR = 422;
    const HTTP_SERVER_ERROR = 500;
    const HTTP_SERVICE_UNAVAILABLE = 503;


}
