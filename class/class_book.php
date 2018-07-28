<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/15
 * Time: 下午11:55
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class book implements ArrayAccess
{

    public $id = null;
    public $language = 'english';
    public $author = '作者';
    public $publisher = '未知';
    public $year = '9999';
    public $extension = 'pdf';
    public $coverurl = '0/null.png';
    public $descr = 'test';
    public $title = 'test_title';
    public $mD5 = 'aa';
    public $filesize = 1024 * 1024 * 33;
    public $page = 10;
    public $edition = '0';
    public $locator = '';

    public function getSize($format = 'm')
    {
        $size = $this->filesize;
        switch ($format) {
            case 'm':
                $size = number_format(($size / 1024 / 1024), 2) . 'm';
                break;
        }
        return $size;
    }

    public function isDeplyed()
    {
        return false;
    }

    public function genLiboocCoverUrl()
    {
        $host = 'http://source.libooc.com';
        $queryData = [];
        $queryData['models'] = 'covers';
        $queryData['location'] = $this->coverurl;
        $queryData = http_build_query($queryData);
        return sprintf('%s?%s', $host, $queryData);
    }

    public function genLiboocSourceUrl()
    {
        $locators = explode('/', $this->locator);
        $locator = array_slice($locators, sizeof($locators) - 1, 1);
        $locator = empty($locator) ? 'null.pdf' : $this->locator;
        $host = 'http://source.libooc.com';
        $queryData = [];
        $queryData['models'] = 'books';
        $queryData['md5'] = $this->mD5;
        $queryData['locator'] = $locator;
        $queryData = http_build_query($queryData);
        return sprintf('%s?%s', $host, $queryData);
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public static function loadData($data)
    {
        $book = new self();
        foreach ($data as $key => $value) {
            $lkey = lcfirst($key);
            if (isset($book->$lkey)) {
                $book->$lkey = $value;
            }
        }
        return $book;
    }

}