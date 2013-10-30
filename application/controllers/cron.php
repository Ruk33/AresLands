<?php

class Cron_Controller extends Base_Controller
{
	public $restful = true;

	// 8e981b1df7b205a43a353682ae118430 = -(orb|cronjob)- en md5
	public function get_8e981b1df7b205a43a353682ae118430()
	{
		Command::run(array('orb'));
	}
	
	// 63bc2e05a572acaacb1342a1573b8eab = /-*/periodic//skill|cc
	public function get_63bc2e05a572acaacb1342a1573b8eab()
	{
		Command::run(array('skill'));
	}
}