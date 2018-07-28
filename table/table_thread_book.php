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

class table_thread_book extends discuz_table
{
	public function __construct() {

		$this->_table = 'thread_book';
		$this->_pk    = 'id';

		parent::__construct();
	}
    public function exist_md5($md5) {
        return  DB::fetch_all('SELECT *FROM %t WHERE md5=%s ',array($this->_table, $md5)) != null;
    }
}

?>