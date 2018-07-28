<?php

/**
 *  (C)2013
 */

/*
 * 存放在插件目录的计划任务模块
 * 文档: http://open.discuz.net/?ac=document&page=plugin_other_module
 */

//cronname:mycron
//minute:1,1,2,3
require_once libfile('function/forum');
require_once libfile('function/post');
require_once libfile('function/libooc', 'plugin/ccc');
require_once libfile('class/book', 'plugin/ccc');
require_once libfile('class/curlhelper', 'plugin/ccc');
loadcache('plugin');
$_LIBOOC_CONFIG = $_G['cache']['plugin']['ccc'];
$topic = $_LIBOOC_CONFIG['topic'];
$size = $_LIBOOC_CONFIG['size'];
$offset = $_LIBOOC_CONFIG['offset'];
$language = 'english';


$mapTopicToForumId = [

];
function bookThreadIsDeploy($book)
{
    return C::t('#ccc#thread_book')->exist_md5($book->mD5);
}

function tag($tag = 'div', $content, $options)
{
    $html = '<' . $tag;
    $attrs = '';
    foreach ($options as $key => $item) {
        if (is_array($item)) {
            $item = implode(';', $item);
        }
        $attrFormat = ' %s = "%s"';
        $attrs .= sprintf($attrFormat, $key, $item);
    }
    $html .= $attrs . '>';
    $html .= $content;
    $html .= '</' . $tag . '>';
    return $html;
}

function genBookForumTitle($book)
{
    return sprintf('%s-%s', $book->title, '资源下载');//标题
}

function genBookForumContent($book)
{
    /** @var book $book */
    $contentLines = [];
    $imgUrl = genBookCoverImageLink($book);
    $img = sprintf('[img]%s[/img]', $imgUrl);
    $contentLines[] = $img;
    $bookDetail = tag('strong', tag('font', '书籍信息', ['color' => 'red']), []);
    $contentLines[] = $bookDetail;
    //language
    $bookTitle = tag('strong', '标题：' . $book->title, []);
    $contentLines[] = $bookTitle;
    $language = tag('strong', '语言：' . $book->language, []);
    $contentLines[] = $language;

    $size = tag('strong', '大小：' . $book->getSize('m'), []);
    $contentLines[] = $size;
    $page = tag('strong', '页数：' . $book->page, []);
    $contentLines[] = $page;
    $year = tag('strong', '日期：' . $book->year, []);
    $contentLines[] = $year;
    $author = tag('strong', '作者：' . $book->author, []);
    $contentLines[] = $author;
    $edition = tag('strong', '版本：' . $book->edition, []);
    $contentLines[] = $edition;
    $publisher = tag('strong', '出版社：' . $book->publisher, []);
    $contentLines[] = $publisher;
    $subDes = tag('strong', tag('font', '简介', ['color' => 'red']), []);
    $des = tag('blockquote', $subDes . '<br>' . $book->descr, ['class' => 'quote']);
    $description = tag('div', $des, ['class' => 'quote']);
    $contentLines[] = $description;
    $sourceDes = tag('strong', tag('font', '电子书下载地址回复可见:', ['color' => 'red']), []);
    $contentLines[] = $sourceDes;
    $hiddenLink = sprintf('[hide]%s[/hide]', genBookDownLink($book));
    $contentLines[] = $hiddenLink;
    return implode('<br>', $contentLines);
}

function genBookDownLink($book)
{
    /** @var  book $book */
    return $book->genLiboocSourceUrl();
}

function genBookCoverImageLink($book)
{
    /** @var  book $book */
    return $book->genLiboocCoverUrl();
}

try {

    $mapTopic = explode('=>', $topic);
    $topics = json_decode($mapTopic[1],true);
    $topics=array_column($topics, 'id');
    //
    // $books = queryBooks([],$_GRAPHQL_URL);
    while ($results = c::t('#ccc#updated')->page_select_books_by_topic($topics, $offset, $size)) {
        $offset += $size;
        $books = [];
        foreach ($results as $item) {
            $books[] = book::loadData($item);
        }
        foreach ($books as $book) {
            $thread = <<<json
		{
			"fid": 2,
			"posttableid": 0,
			"typeid": 0,
			"sortid": 0,
			"readperm": 0,
			"price": 0,
			"author": "admin",
			"authorid": 1,
			"subject": "abc",
			"dateline": 1531652229,
			"lastpost": 1531652229,
			"lastposter": "admin",
			"views": 1,
			"replies": 0,
			"displayorder": 0,
			"highlight": 0,
			"digest": 0,
			"rate": 0,
			"special": 0,
			"attachment": 0,
			"moderated": 0,
			"closed": 0,
			"stickreply": 0,
			"recommends": 0,
			"recommend_add": 0,
			"recommend_sub": 0,
			"heats": 0,
			"status": 32,
			"isgroup": 0,
			"favtimes": 0,
			"sharetimes": 0,
			"stamp": -1,
			"icon": 20,
			"pushedaid": 0,
			"cover": 0,
			"replycredit": 0,
			"relatebytag": "0",
			"maxposition": 1,
			"bgcolor": "",
			"comments": 0,
			"hidden": 0
		}
json;
            $post = <<<json
		{
			"first": 1,
			"author": "admin",
			"authorid": 1,
			"dateline": 1531661875,
			"useip": "127.0.0.1",
			"port": 53522,
			"invisible": 0,
			"anonymous": 0,
			"usesig": 1,
			"htmlon": 0,
			"bbcodeoff": 0,
			"smileyoff": -1,
			"parseurloff": 0,
			"attachment": 0,
			"rate": 0,
			"ratetimes": 0,
			"status": 0,
			"comment": 0,
			"replycredit": 0,
			"position": 1
		}
json;
            /**@var Book $book */
            if (bookThreadIsDeploy($book)) {
                continue;
            }
            $fid = $mapTopic[0];//什么主题
            $subject = genBookForumTitle($book);
            $message = genBookForumContent($book);
            $dateLine = time() + rand(10, 60);//创建时间
            $lastPost = $dateLine;//最后发布时间
            $thread = json_decode($thread, true);
            $thread['fid'] = $fid;
            $thread['icon'] = 10;//帖子的类型 20 新人帖  10热帖 -1什么都没有
            $thread['subject'] = $subject;
            $thread['dateline'] = $dateLine;
            $thread['lastpost'] = $lastPost;

            $tid = C::t('forum_thread')->insert($thread, true);
            // C::t('forum_newthread')->insert(['tid'=>$tid,'fid'=>$fid,'dateLine'=>time()]);
            $post = json_decode($post, true);

            $post['fid'] = $fid;
            $post['tid'] = $tid;
            $post['message'] = $message;
            $post['subject'] = $subject;
            $post['dateline'] = $dateLine;
            $post['htmlon'] = 1;
            $maxPid = C::t('forum_post_tableid')->fetch_max_id() + 1;
            $post['pid'] = $maxPid;
            C::t('forum_post_tableid')->insert(['pid' => $maxPid]);
            $pid = C::t('forum_post')->insert('forum_post', $post, true);
            C::t('#ccc#thread_book')->insert(['tid' => $tid, 'md5' => $book->mD5]);

        }
    }
} catch (Exception $exception) {
    throw  $exception;
}



//您的计划任务脚本内容

