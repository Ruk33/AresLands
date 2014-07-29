<?php

class Authenticated_Talent_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var Skill
	 */
	protected $skill;
	
	public static function register_routes()
	{
		Route::get("authenticated/talent", array(
			"uses" => "authenticated.talent@index",
			"as"   => "get_authenticated_talent_index"
		));
		
		Route::post("authenticated/talent/learn", array(
			"uses" => "authenticated.talent@learn",
			"as"   => "post_authenticated_talent_learn"
		));
		
		Route::post("authenticated/talent/cast", array(
			"uses" => "authenticated.talent@cast",
			"as"   => "post_authenticated_talent_cast"
		));
	}
	
	public function __construct(Skill $skill, Character $character)
	{
		$this->skill = $skill;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{
		$character = $this->character->get_logged();
		$racials = $this->skill->get_racials($character->race);
		$talents = $this->skill->get_talents($character->characteristics);
		
		$this->layout->title = 'Talentos';
		$this->layout->content = View::make(
            'authenticated.talents', compact('character', 'racials', 'talents')
        );
	}
	
	public function post_learn()
	{
		$skill = $this->skill->find_or_die(Input::get("id"));
		$character = $this->character->get_logged();
		
		if ( $character->can_learn_talent($skill) )
		{
			if ( $character->learn_talent($skill) )
			{
				Session::flash("message", "Aprendiste el talento {$skill->name}");
			}
		}
		
		return Redirect::to_route("get_authenticated_talent_index");
	}
	
	public function post_cast()
	{
		$character = $this->character->get_logged();
		$talent = $character->talents()->where_skill_id(Input::get('skill_id'))->first_or_die();
		
		if ( $character->can_use_talent($talent) )
		{
			$target = $this->character->find_or_die(Input::get('id'));
			$hasReflect = $target->has_skill(Config::get('game.reflect_skill'));

			if ( $character->use_talent($talent, $target) )
			{
				$skill = $talent->skill;
				
				if ( $hasReflect && $skill->type == 'debuff' )
				{
					Session::flash("error", "¡Oh no!, {$target->name} te ha reflejado el hechizo {$skill->name}");
				}
				else
				{
					Session::flash("message", "Lanzaste la habilidad {$skill->name} a {$target->name}");
				}
			}
		}
		
		return Redirect::to_route("get_authenticated_index");
	}
}