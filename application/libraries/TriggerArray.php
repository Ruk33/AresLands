<?php namespace Libraries;

class TriggerArray
{
	/**
	 * @var Trigger
	 */
	public $triggers;
	
	/**
	 * @param \Libraries\Trigger $trigger
	 */
	public function add(Trigger $trigger)
	{
		$this->triggers[] = $trigger;
	}
	
	/**
	 * @return \Libraries\TriggerIterator
	 */
	public function iterator()
	{
		return new TriggerIterator($this);
	}
}