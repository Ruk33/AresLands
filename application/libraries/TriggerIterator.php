<?php namespace Libraries;

class TriggerIterator
{
	/**
	 * @var Trigger
	 */
	private $triggers;
	
	/**
	 * @var integer
	 */
	private $position;
	
	/**
	 * @param \Libraries\TriggerArray $triggers
	 */
	public function __construct(TriggerArray $triggers)
	{
		$this->triggers = $triggers->triggers;
		$this->position = 0;
	}
	
	/**
	 * @return boolean
	 */
	public function hasNext()
	{
		return $this->position < count($this->triggers);
	}
	
	/**
	 * @return \Libraries\Trigger
	 */
	public function next()
	{
		$trigger = $this->triggers[$this->position];
		$this->position++;
		
		return $trigger;
	}
}