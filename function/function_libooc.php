<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/15
 * Time: 下午11:09
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * @param array $params
 * @return array
 */
function queryBooks($params = [],$url)
{
    $graphql = <<<data
            {
          books(topic: "%s",page:%s,size:%s) {
            ID
            Language
            Title
            Topic
            Descr:Desc
            MD5
            Locator
            Author
            Edition
            Year
            Pages
            Filesize
            Coverurl
            Tags
            Publisher
           
          }
        }
data;
    $queryData= http_build_query(['query' => sprintf($graphql, 305, 3, 1)]);
    $url = sprintf('%s?%s', $url, $queryData);
    $res = curlhelper::get($url);



    $books = [];
    if (!$res['err']) {
        $content = json_decode($res['content'],true);
        $bookData = $content['data']['books'];
        foreach ($bookData as $bookDatum) {
            $books[] = book::loadData($bookDatum);
        }
    }
    return $books;
}

