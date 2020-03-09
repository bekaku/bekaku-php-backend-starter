<?php
namespace application\util;
use application\util\MessageUtils as MessageUtil;
class Log {

	protected $_log_path;
	protected $_threshold	= 1;
	protected $_date_fmt	= 'Y-m-d H:i:s';
	protected $_enabled	= true;
	protected $_levels	= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

	/**
	 * Constructor
	 */
	public function __construct()
	{

		$logConfig = MessageUtil::getConfig('log');

		$this->_log_path = ($logConfig['log_path'] != '') ? $logConfig['log_path'] : __SITE_PATH.'/resources/logs/';
//		$this->_log_path = ($logConfig['log_path'] != '') ? $logConfig['log_path'] : __UPLOAD_PATH.'/logs/';

		if (!is_dir($this->_log_path) OR !is_really_writable($this->_log_path))
		{
			$this->_enabled = false;
		}
		if (is_numeric($logConfig['log_threshold']))
		{
			$this->_threshold = $logConfig['log_threshold'];
		}

		if ($logConfig['log_date_format'] != '')
		{
			$this->_date_fmt = $logConfig['log_date_format'];
		}
	}

	// --------------------------------------------------------------------

	public function write_log($level = 'error', $msg, $php_error = FALSE, $fileName="")
	{

		if ($this->_enabled === false)
		{
			return false;
		}

		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
		{
			return false;
		}
		$filepath = null;
		if(!$fileName){
			$filepath = $this->_log_path.'log-'.date('Y-m-d').'.php';
		}else{
			$filepath = $this->_log_path.$fileName.'.php';
		}
		$message  = '';

//		if ( ! file_exists($filepath))
//		{
//			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
//		}


		if ( ! $fp = @fopen($filepath, MessageUtil::getConfig('fopen_mode.fopen_write_create')))
		{
			return false;
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, MessageUtil::getConfig('chmod_mode.file_write_mode'));
		return true;
	}
	
}