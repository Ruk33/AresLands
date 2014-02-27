<?php

class CharacterCreation_Controller extends Base_Controller
{
	public $layout = 'layouts.default';
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		/*
		 *	Solo queremos logueados
		 */
		$this->filter('before', 'auth');

		/*
		 *	Solo queremos usuarios
		 *	que no tengan un personaje
		 */
		$this->filter('before', 'hasCharacter', array('authenticated/index'));
	}

	public function get_race()
	{
		$this->layout->title = '¿Con qué raza comenzarás tu aventura?';
		$this->layout->content = View::make('charactercreation.race');
	}

	public function get_create($race = '')
	{
		/*
		 *	Evitamos que elijan cualquier raza
		 */
		if ( ! in_array($race, array('dwarf', 'human', 'drow', 'elf')) )
		{
			return Redirect::to('charactercreation/race');
		}

		$this->layout->title = 'Último paso para jugar';
		$this->layout->content = View::make('charactercreation.create', array('race' => $race));
	}

	public function post_create()
	{
		$character = new Character();
		$data = Input::json();

		$character->name = (isset($data->name)) ? $data->name : '';
		$character->race = (isset($data->race)) ? $data->race : '';
		$character->gender = (isset($data->gender)) ? $data->gender : '';

		if ($character->validate()) {
			$character->user_id = Auth::user()->id;

			switch ($character->get_attribute('race')) {
				case 'dwarf':
					$character->stat_strength = 28;
					$character->stat_dexterity = 9;
					$character->stat_resistance = 13;
					
					$character->stat_magic = 0;
					$character->stat_magic_skill = 0;
					$character->stat_magic_resistance = 0;
					//50

					$character->max_life = 500;
					$character->zone_id = 1;
					break;

				case 'human':
					$character->stat_strength = 22;
					$character->stat_dexterity = 10;
					$character->stat_resistance = 0;
					
					$character->stat_magic = 10;
					$character->stat_magic_skill = 8;
					$character->stat_magic_resistance = 0;
					//50

					$character->max_life = 400;
					$character->zone_id = 3;
					break;

				case 'drow':
					$character->stat_strength = 0;
					$character->stat_dexterity = 0;
					$character->stat_resistance = 0;
					
					$character->stat_magic = 37;
					$character->stat_magic_skill = 13;
					$character->stat_magic_resistance = 0;
					//50

					$character->max_life = 150;
					$character->zone_id = 2;
					break;

				case 'elf':
					$character->stat_strength = 12;
					$character->stat_dexterity = 8;
					$character->stat_resistance = 3;
					
					$character->stat_magic = 17;
					$character->stat_magic_skill = 8;
					$character->stat_magic_resistance = 2;
					//50

					$character->max_life = 300;
					$character->zone_id = 4;
					break;
			}

			$character->level = 1;
			$character->xp_next_level = 10;
			
			$character->current_life = $character->max_life;

			$character->save();

			$characterCoin = new CharacterItem();
			$characterCoin->owner_id = $character->id;
			$characterCoin->item_id = Config::get('game.coin_id');
			$characterCoin->count = 50;
			$characterCoin->location = 'none';

			$characterCoin->save();

			return json_encode(array('ok' => true));
		} else {
			return json_encode(array('errors' => $character->errors()->all()));
		}
	}
}