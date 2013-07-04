<?php

class Skill extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'skills';
	public static $key = 'id';

	public function get_data()
	{
		return unserialize($this->get_attribute('data'));
	}

	public function set_data($data)
	{
		$this->set_attribute('data', serialize($data));
	}
}