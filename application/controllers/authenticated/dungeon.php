<?php

class Authenticated_Dungeon_Controller extends Authenticated_Base
{
	public function post_dungeon()
	{
		$dungeon = Dungeon::find(Input::get('id'));
		$redirection = Redirect::to('authenticated');

		if ( $dungeon )
		{
			$character = Character::get_character_of_logged_user();

			if ( $dungeon->can_character_do_dungeon($character) )
			{
				$dungeonBattle = new DungeonBattle($character, $dungeon, $dungeon->get_level($character));

				if ( $dungeonBattle->get_completed() )
				{
					return $redirection->with('message', '¡Haz completado la mazmorra!');
				}
				else
				{
					return $redirection->with('error', 'Uno de los monstruos de la mazmorra te ha derrotado');
				}
			}
		}

		return $redirection;
	}

	public function get_dungeon()
	{
		$character = Character::get_character_of_logged_user(array('id', 'zone_id', 'level'));
		$dungeons = Dungeon::available_for($character)->get();

		$this->layout->title = 'Mazmorras';
		$this->layout->content = View::make('authenticated.dungeon', compact('character', 'dungeons'));
	}	
}