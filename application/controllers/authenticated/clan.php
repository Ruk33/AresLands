<?php

class Authenticated_Clan_Controller extends Authenticated_Base
{
    /**
     *
     * @var Clan
     */
    protected $clan;
	
	/**
	 *
	 * @var Skill
	 */
	protected $skill;
    
	public static function register_routes()
	{
		Route::get("authenticated/clan/show/(:num)", array(
			"uses" => "authenticated.clan@show",
			"as"   => "get_authenticated_clan_show"
		));
		
		Route::get("authenticated/clan/create", array(
			"uses" => "authenticated.clan@create",
			"as"   => "get_authenticated_clan_create"
		));
		
		Route::post("authenticated/clan/create", array(
			"uses" => "authenticated.clan@create",
			"as"   => "post_authenticated_clan_create"
		));
		
		Route::post("authenticated/clan/petitions/new", array(
			"uses" => "authenticated.clan@newPetition",
			"as"   => "post_authenticated_clan_new_petition"
		));
		
		Route::group(array("before" => "auth|hasNoCharacter|hasClan"), function()
		{
			Route::post("authenticated/clan/learnSkill", array(
				"uses" => "authenticated.clan@learnSkill",
				"as"   => "post_authenticated_clan_learn_skill"
			));
			
			Route::post("authenticated/clan/editMessage", array(
				"uses" => "authenticated.clan@editMessage",
				"as"   => "post_authenticated_clan_edit_message"
			));
			
			Route::post("authenticated/clan/leave", array(
				"uses" => "authenticated.clan@leave",
				"as"   => "post_authenticated_clan_leave"
			));

			Route::post("authenticated/clan/delete", array(
				"uses" => "authenticated.clan@delete",
				"as"   => "post_authenticated_clan_delete"
			));
			
			Route::post("authenticated/clan/kick", array(
				"uses" => "authenticated.clan@kick",
				"as"   => "post_authenticated_clan_kick"
			));
			
			Route::post("authenticated/clan/petitions/reject", array(
				"uses" => "authenticated.clan@rejectPetition",
				"as"   => "post_authenticated_clan_reject_petition"
			));
			
			Route::post("authenticated/clan/petitions/accept", array(
				"uses" => "authenticated.clan@acceptPetition",
				"as"   => "post_authenticated_clan_accept_petition"
			));
			
			Route::post("authenticated/clan/permissions", array(
				"uses" => "authenticated.clan@setMemberPermissions",
				"as"   => "post_authenticated_clan_permissions"
			));
			
			Route::post("authenticated/clan/leader", array(
				"uses" => "authenticated.clan@giveLeaderShip",
				"as"   => "post_authenticated_clan_give_leader"
			));
		});
	}
	
    /**
	 * 
	 * @param Character $character
	 * @param Clan $clan
	 * @param Skill $skill
	 */
    public function __construct(Character $character, Clan $clan, Skill $skill)
    {
		$this->character = $character;
        $this->clan = $clan;
		$this->skill = $skill;
		
        parent::__construct();
    }
    
	/**
	 * Intentamos aprender habilidad de grupo
	 * 
	 * @return Redirect
	 */
    public function post_learnSkill()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		
		if ( $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
		{
			$skill = $this->skill->where_id(Input::get("skill_id"))->where_level(Input::get("skill_level"))->first_or_die();
			
			if ( $clan->can_learn_skill($skill) )
			{
				$clan->learn_skill($skill);
				Session::flash("message", "Has aprendido la habilidad {$skill->name} para tu grupo");
			}
		}
		
		return Laravel\Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}
    
	/**
	 * Intentamos modificar mensaje del grupo
	 */
    public function post_editMessage()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		
		if ( $clan->has_permission($character, Clan::PERMISSION_EDIT_MESSAGE) )
		{
            $json = Input::json(true);
            
			$clan->message = Input::get("message", $json["message"]);
			$clan->save();
		}
	}

	/**
	 * Intentamos crear grupo
	 * @return Redirect
	 */
	public function post_create()
	{
		$character = $this->character->get_logged();
		
		if ( ! $character->clan_id )
		{
			$clan = $this->clan->create_instance(
                array_merge(
                    array(
                        "leader_id" => $character->id,
                        "server_id" => $character->server_id
                    ),
                    Input::only(array("name", "message"))
                )
            );
			
			if ( $clan->validate() )
			{
				$clan->save();
                $clan->join($character);
                
				return Laravel\Redirect::to_route(
                    "get_authenticated_clan_show", 
                    array($clan->id)
                );
			}
			else
			{
				Session::flash("error", $clan->errors()->all());
				return Laravel\Redirect::to_route("get_authenticated_clan_create");
			}
		}
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}

	/**
	 * 
	 * @return Redirect
	 */
	public function get_create()
	{
		$character = $this->character->get_logged();
		
		if ( $character->clan_id )
		{
			return Laravel\Redirect::to_route("get_authenticated_clan_show", array($character->clan_id));
		}
		
		$this->layout->title = "Crear grupo";
		$this->layout->content = View::make("authenticated.createclan", array("clan" => $this->clan->create_instance()));
	}
	
	/**
	 * Intentamos salir del grupo
	 * @return Redirect
	 */
	public function post_leave()
	{
		$character = $this->character->get_logged();
		
		if ( $character->can_leave_clan() )
		{
			$character->leave_clan();
			Session::flash("message", "Haz salido del grupo");
		}
		else
		{
			Session::flash("error", "En este momento no puedes salir del grupo");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}

	/**
	 * Intentamos borrar el grupo
	 * @return Redirect
	 */
	public function post_delete()
	{
		$character = $this->character->get_logged();
		
		if ( $character->can_delete_clan() )
		{
			$character->delete_clan();
			Session::flash("message", "Haz borrado el grupo con exito");
		}
		else
		{
			Session::flash("error", "No puedes borrar el grupo en este momento");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}
	
	/**
	 * Intentamos expulsar a miembro del grupo
	 * @param string $name Nombre del personaje a expulsar
	 * @return Redirect
	 */
	public function post_kick()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		$member = $clan->members()->where_name(Input::get("name"))->first_or_die();
		
		if ( $clan->can_kick_member($character, $member) )
		{
			$clan->kick_member($character, $member);
			Session::flash("success", "Expulsaste a {$member->name} del grupo exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes expulsar miembros en este momento");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}
	
	/**
	 * Intentamos rechazar peticion
	 * @return Redirect
	 */
	public function post_rejectPetition()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		
		if ( $clan->can_reject_petitions($character) )
		{
			$petition = $clan->petitions()->find_or_die(Input::get("id"));
			$clan->reject_petition($character, $petition);
			
			Session::flash("success", "Rechazaste la peticion exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes rechazar peticiones");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}
	
	/**
	 * Intentamos aceptar peticion
	 * 
	 * @return Redirect
	 */
	public function post_acceptPetition()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		
		if ( $clan->can_accept_petitions($character) )
		{
			$petition = $clan->petitions()->find_or_die(Input::get("id"));
			$clan->accept_petition($character, $petition);
			
			Session::flash("success", "Aceptaste la peticion exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes aceptar peticiones");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}

	/**
	 * Intentamos enviar nueva peticion de ingreso al grupo 
	 * 
	 * @return Redirect
	 */
	public function post_newPetition()
	{
		$character = $this->character->get_logged();
		
		if ( $character->can_enter_in_clan() )
		{
			$clan = $this->clan->find_or_die(Input::get("id"));
			
			if ( $clan->can_send_petition($character) )
			{
				$clan->send_petition($character);
				Session::flash("success", "Haz enviado exitosamente la peticion para la inclusion en este grupo");
			}
			else
			{
				Session::flash("error", "Ya tienes una peticion pendiente con este grupo, debes esperar a que sea respondida");
			}
		}
		else
		{
			Session::flash("error", "No puedes entrar en un grupo en este momento");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_index");
	}
	
	/**
	 * Intenamos modificar los permisos de miembro
	 * @return Redirect
	 */
	public function post_setMemberPermissions()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		
		if ( $clan->can_modify_permissions($character) )
		{
			$input = Input::all();
			$member = $clan->members()->find_or_die($input['id']);
			
			$member->set_permission(Clan::PERMISSION_ACCEPT_PETITION, isset($input['can_accept_petition']), false);
			$member->set_permission(Clan::PERMISSION_DECLINE_PETITION, isset($input['can_decline_petition']), false);
			$member->set_permission(Clan::PERMISSION_KICK_MEMBER, isset($input['can_kick_member']), false);
			$member->set_permission(Clan::PERMISSION_LEARN_SPELL, isset($input['can_learn_spell']), false);
			$member->set_permission(Clan::PERMISSION_EDIT_MESSAGE, isset($input['can_edit_message']), false);
			$member->set_permission(Clan::PERMISSION_REGISTER_TOURNAMENT, isset($input['can_register_tournament']), false);

			$member->save();
			
			Session::flash("success", "Permisos modificados correctamente");
		}
		
		return Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}

    /**
     * Mostramos grupo
     * @param integer $id
     */
	public function get_show($id)
	{		
		$clan = $this->clan->same_server()->find_or_die($id);
		$character = $this->character->get_logged();
		$members = $clan->members()->get();
		$skills = $this->clan->get_skills()->where_level(1)->get();
		$petitions = array();

		if ( $clan->can_see_petitions($character) )
		{
			$petitions = $clan->petitions()->get();
		}

		$this->layout->title = $clan->name;
		$this->layout->content = View::make('authenticated.viewclan', compact(
			'clan',	'members', 'character', 'skills', 'petitions'
		));
	}
	
	/**
	 * Intenamos dar liderazgo a personaje
	 * 
	 * @return Redirect
	 */
	public function post_giveLeaderShip()
	{
		$character = $this->character->get_logged();
		$clan = $character->clan()->first_or_die();
		$newLider = $clan->members()->find_or_die(Input::get("id"));
		
		if ( $character->can_give_leadership_to($newLider) )
		{
			$character->give_leadership_to($newLider);
			Session::flash("success", "Le haz dado el liderazgo del grupo a {$newLider->name}");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_clan_show", array($clan->id));
	}
}