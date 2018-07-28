<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/24
 * Time: 下午11:43
 */

class curlhelper
{
    const CURL_METHOD_GET = 'get';
    const CURL_METHOD_PUT = 'put';
    const CURL_METHOD_POST = 'post';
    const CURL_METHOD_HEAD = 'head';
    const CURL_METHOD_DELETE = 'delete';
    const CURL_UA = 'BaiShanCloud';

    public static function get($url, $options = [], $headers = [], $try = 3)
    {
        return self::request($url, self::CURL_METHOD_GET, $options, $headers, $try);
    }

    public static function head($url, $options = [], $headers = [], $try = 3)
    {
        return self::request($url, self::CURL_METHOD_HEAD, $options, $headers, $try);
    }

    public static function delete($url, $options = [], $headers = [], $try = 3)
    {
        return self::request($url, self::CURL_METHOD_DELETE, $options, $headers, $try);
    }

    public static function put($url, $postdata = null, $options = [], $headers = [], $try = 3)
    {
        if (!empty($postdata)) {
            if (is_string($postdata)) {
                $options[CURLOPT_POSTFIELDS] = $postdata;
            } else {
                $options[CURLOPT_POSTFIELDS] = http_build_query($postdata);
            }
        }
        return self::request($url, self::CURL_METHOD_PUT, $options, $headers, $try);
    }

    public static function post($url, $postdata = null, $options = [], $headers = [], $try = 3)
    {
        if (!empty($postdata)) {
            if (is_string($postdata)) {
                $options[CURLOPT_POSTFIELDS] = $postdata;
            } else {
                $options[CURLOPT_POSTFIELDS] = http_build_query($postdata);
            }
        }
        return self::request($url, self::CURL_METHOD_POST, $options, $headers, $try);
    }

    public static function request($url, $type = self::CURL_METHOD_GET, $options = [], $headers = [], $try = 3)
    {
        $res = [
            'err' => false,
            'header' => [],
            'content' => '',
        ];
        try {
            $ch = curl_init();
            $options[CURLOPT_URL] = $url;
            switch ($type) {
                case self::CURL_METHOD_POST:
                    $options[CURLOPT_POST] = 1;
                    break;
                case self::CURL_METHOD_PUT:
                    $options[CURLOPT_CUSTOMREQUEST] = strtoupper($type);
                    break;
                case self::CURL_METHOD_HEAD:
                    $options[CURLOPT_NOBODY] = 1;
                    $options[CURLOPT_HEADER] = 1;
                    break;
                case self::CURL_METHOD_DELETE:
                    $options[CURLOPT_CUSTOMREQUEST] = strtoupper($type);
                    break;
                default:
                    $options[CURLOPT_POST] = 0;
                    break;
            }
            $options_def = [
                CURLOPT_HEADER => 0,
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_USERAGENT => self::CURL_UA,
                CURLOPT_PROXYPORT => 80,
                CURLOPT_SSL_VERIFYPEER => false,
            ];
            foreach ($options_def as $k => $v) {
                if (empty($options[$k])) {
                    $options[$k] = $v;
                }
            }
            if (count($headers)) {
                if (empty($options[CURLOPT_HTTPHEADER])) {
                    $options[CURLOPT_HTTPHEADER] = [];
                }
                foreach ($headers as $k => $header) {
                    if (is_array($header)) {
                        if (is_numeric($k)) {
                            foreach ($header as $subHeader) {
                                $options[CURLOPT_HTTPHEADER][] = (string)$subHeader;
                            }
                        } else {
                            foreach ($header as $subHeader) {
                                $options[CURLOPT_HTTPHEADER][] = (string)$k . ': ' . (string)$subHeader;
                            }
                        }
                    } else {
                        if (is_numeric($k)) {
                            $options[CURLOPT_HTTPHEADER][] = (string)$header;
                        } else {
                            $options[CURLOPT_HTTPHEADER][] = (string)$k . ': ' . (string)$header;
                        }
                    }
                }
            }
            curl_setopt_array($ch, $options);
            if ($try > 10 || $try < 0) {
                $try = 3;
            }
            while ($try) {
                $try--;
                $content = curl_exec($ch);
                $curlErrno = curl_errno($ch);
                if ($curlErrno && $try) {
                    continue;
                }
                if (!$curlErrno) {
                    $res['header'] = curl_getinfo($ch);
                    if ($options[CURLOPT_HEADER]) {
                        $lines = explode("\n", $content);
                        foreach ($lines as $line) {
                            $subs = explode(":", $line, 2);
                            if (count($subs) < 2) {
                                continue;
                            }
                            $key = trim($subs[0]);
                            $val = trim($subs[1]);
                            $res['header'][$key] = $val;
                        }
                    } else {
                        $res['content'] = $content;
                    }
                    break;
                } else {
                    $res['err'] = curl_error($ch);
                }
            }
            curl_close($ch);
        } catch (\Exception $e) {
            $res['err'] = 'Err:' . $e->getMessage();
        }
        return $res;
    }

    public static function getUserIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return null;
        }
    }
}