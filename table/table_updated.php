<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_admincp_cmenu.php 27806 2012-02-15 03:20:46Z svn_project_zhangjie $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_updated extends discuz_table
{
	public function __construct() {

		$this->_table = 'updated';
		$this->_pk    = 'ID';
		parent::__construct();
	}

    public function select_books_with_desc($where)
    {
        return DB::fetch_all("select updated.* , description.descr as Descr from  %t left join %t on (description.md5 = updated.mD5) WHERE %s", array
        ($this->_table, 'description', $where));
    }


    public function page_select_books_by_topic($topics,$offset,$size)
    {
        return DB::fetch_all("select updated.* , description.descr as Descr from  %t left join %t on (description.md5 = updated.mD5) WHERE  
language = 'english' AND ".DB::field('Topic',$topics,'in')." limit %d,%d", array($this->_table, 'description',$offset,
            $size));
    }

}

?>