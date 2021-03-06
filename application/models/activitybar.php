<?php

class ActivityBar extends Base_Model
{
	public static $softDelete = true;
	public static $timestamps = false;
	public static $table = "activity_bars";
    
    public static $factory = array(
        "character_id" => "factory|Character",
        "filled_amount" => "0"
    );

	public function character()
	{
		return $this->belongs_to("Character", "character_id");
	}

	/**
	 * @param Character $character
     * @deprecated
	 */ 
	public static function get_bar_of_character(Character $character)
	{
		return ActivityBar::where_character_id($character->id)->first();
	}

    public function is_full()
    {
        return $this->filled_amount >= Config::get('game.activity_bar_max'); 
	}

	public function reset()
	{
		$this->filled_amount = 0;
		$this->save();
	}
    
    public function give_full_reward()
    {
        $this->character->give_full_activity_bar_reward();
        Event::fire('fullActivityBar', array($this->character));
    }
    
//    public function add($value)
//    {
//        $this->filled_amount += $value;
//        
//        if ($this->is_full()) {
//            $this->give_full_reward();
//            $this->reset();
//        }
//        
//        $this->save();
//    }

	public static function add(Character $character, $amount)
	{
		$bar = self::get_bar_of_character($character);

		if ( ! $bar )
		{
			$bar = new ActivityBar();

			$bar->character_id = $character->id;
			$bar->filled_amount = 0;
		}

		$bar->filled_amount += $amount;

		if ( $bar->is_full() )
		{
			$bar->reset();

			$character->give_full_activity_bar_reward();
			Event::fire('fullActivityBar', array($character));
		}
		else
		{
			$bar->save();
		}
	}
}