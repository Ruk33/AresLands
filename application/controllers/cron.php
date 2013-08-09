<?php

class Cron_Controller extends Base_Controller
{
	public $restful = true;

	// 8e981b1df7b205a43a353682ae118430 = -(orb|cronjob)- en md5
	public function get_8e981b1df7b205a43a353682ae118430()
	{
		Command::run(array('orb'));
	}
}