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
    
    /**
	 * 
	 * @param Clan $clan
	 * @param Skill $skill
	 * @param Laravel\Auth\Drivers\Driver $auth
	 * @param Laravel\Session\Payload $session
	 */
    public function __construct(Clan $clan, Skill $skill)
    {
        parent::__construct();
        
        $this->clan = $clan;
		$this->skill = $skill;
    }
    
	/**
	 * Intentamos aprender habilidad de grupo
	 * @param integer $skillId
	 * @param integer $level
	 * @return Redirect
	 */
    public function get_learnSkill($skillId, $level)
	{
		$character = $this->auth->user()->character;
		$clan = $this->clan->find_or_die($character->clan_id);
		
		if ( $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
		{
			$skill = $this->skill
						  ->where_id($skillId)
						  ->where_level($level)
						  ->first_or_die();

			if ( $clan->can_learn_skill($skill) )
			{
				$clan->learn_skill($skill);
				return Redirect::to('authenticated/clan/' . $clan->id);
			}
		}

		return Redirect::to('authenticated/index/');
	}
    
	/**
	 * Intentamos modificar mensaje del grupo
	 */
    public function post_editMessage()
	{
		$character = $this->auth->user()->character;
		$clan = $character->clan()->first_or_die();
		
		if ( $character->has_permission(Clan::PERMISSION_EDIT_MESSAGE) )
		{
			$clan->message = Input::get('message');
			$clan->save();
		}
	}

	/**
	 * Intentamos crear grupo
	 * @return Redirect
	 */
	public function post_create()
	{
		$character = $this->auth->user()->character;
		
		if ( ! $character->clan_id )
		{
			$clan = IoC::resolve('Clan');

			$clan->name = Input::get('name');
			$clan->message = Input::get('message');
			$clan->leader_id = $character->id;

			if ( $clan->validate() )
			{
				$clan->save();
				$clan->join($character);

				return Redirect::to('authenticated/clan/' . $clan->id);
			}
			else
			{
				$this->session->flash('errorMessages', $clan->errors()->all());
				return Redirect::to('authenticated/createClan');
			}
		}
		
		return Redirect::to("authenticated/index/");
	}

	/**
	 * 
	 * @return Redirect
	 */
	public function get_create()
	{
		$character = $this->auth->user()->character;

		if ( $character->clan_id )
		{
			return Redirect::to('authenticated/clan/' . $character->clan_id);
		}

		$this->layout->title = 'Crear grupo';
		$this->layout->content = View::make('authenticated.createclan');
	}
	
	/**
	 * Intentamos salir del grupo
	 * @return Redirect
	 */
	public function post_leave()
	{
		$character = $this->auth->user()->character;

		if ( ! $character->can_leave_clan() )
		{
			$this->session->flash('error', 'En este momento puedes salir del grupo.');
			return Redirect::to('authenticated/index/');
		}

		$character->leave_clan();

		return Redirect::to('authenticated/clan/');
	}

	/**
	 * Intentamos borrar el grupo
	 * @return Redirect
	 */
	public function post_delete()
	{
		$character = $this->auth->user()->character;
		
		if ( $character->can_delete_clan() )
		{
			$character->delete_clan();
		}
		else
		{
			$this->session->flash('error', 'No puedes borrar el grupo cuando el torneo está activo.');
		}

		return Redirect::to('authenticated/index/');
	}
	
	/**
	 * Intentamos expulsar a miembro del grupo
	 * @param string $name Nombre del personaje a expulsar
	 * @return Redirect
	 */
	public function post_kickMember($name)
	{
		$character = $this->auth->user()->character;
		$clan = $character->clan()->first_or_die();
		$member = $clan->members()->where_name($name)->first_or_die();
		
		if ( $clan->can_kick_member($character, $member) )
		{
			$clan->kick_member($character, $member);
			
			$this->session->flash('successMessage', "El miembro {$member->name} ha sido expulsado del grupo");
			return Redirect::to('authenticated/clan/' . $clan->id);
		}

		$this->session->flash('error', "No puedes expulsar a {$member->name} del grupo");
		return Redirect::to('authenticated/index/');
	}
	
	/**
	 * Intentamos rechazar peticion
	 * @param integer $id
	 * @return Redirect
	 */
	public function post_rejectPetition($id)
	{
		$character = $this->auth->user()->character;
		$clan = $character->clan()->first_or_die();
		$petition = $clan->petitions()->where_id($id)->first_or_die();
		
		if ( $clan->can_reject_petitions($character) )
		{
			$clan->reject_petition($character, $petition);
			
			$this->session->flash('successMessage', 'La petición ha sido rechazada');
			return Redirect::to('authenticated/clan/' . $clan->id);
		}

		return Redirect::to('authenticated/index/');
	}
	
	/**
	 * Intentamos aceptar peticion
	 * @param integer $id
	 * @return Redirect
	 */
	public function post_acceptPetition($id)
	{
		$character = $this->auth->user()->character;
		$clan = $character->clan()->first_or_die();
		$petition = $clan->petitions()->where_id($id)->first_or_die();
		
		if ( $clan->can_accept_petitions($character) )
		{
			if ( $clan->accept_petition($character, $petition) )
			{
				$this->session->flash('successMessage', 'La petición ha sido aceptada y el miembro incluido.');
			}
			else
			{
				$this->session->flash('error', 'El jugador no puede ser aceptado en el grupo, posiblemente ya esté en otro grupo.');
			}
			
			return Redirect::to('authenticated/clan/' . $clan->id);
		}

		return Redirect::to('authenticated/index/');
	}

	/**
	 * Intentamos enviar nueva peticion de ingreso al grupo 
	 * @param integer $id Id del clan
	 * @return Redirect
	 */
	public function post_newPetition($id)
	{
		$character = $this->auth->user()->character;
		
		if ( $character->can_enter_in_clan() )
		{
			$clan = $this->clan->find_or_die($id);
			
			if ( $clan->can_send_petition($character) )
			{
				$clan->send_petition($character);
				
				$this->session->flash('successMessage', 'Haz enviado exitosamente la petición para la inclusión en este grupo');
			}
			else
			{
				$this->session->flash('errorMessage', 'Ya tienes una petición pendiente con este grupo, debes esperar a que sea respondida');
			}
			
			return Redirect::to('authenticated/clan/' . $clan->id);
		}

		$this->session->flash('errorMessage', 'No puedes ingresar a un grupo');
		
		return Redirect::to('authenticated/index/');
	}
	
	/**
	 * Intenamos modificar los permisos de miembro
	 * @return Redirect
	 */
	public function post_modifyMemberPermissions()
	{
		$character = $this->auth->user()->character;
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
		}
		
		return Redirect::to('authenticated/clan/' . $clan->id);
	}

    /**
     * Mostramos grupo
     * @param integer $id
     */
	public function get_show($id)
	{		
		$clan = $this->clan->find_or_die($id);
		$character = $this->auth->user()->character;
		$members = $clan->members()->get();
		$skills = $this->clan->get_skills()->where_level(1)->get();
		$petitions = array();

		if ( $clan->can_accept_petitions($character) || $clan->can_reject_petitions($character) )
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
	 * @return Redirect
	 */
	public function post_giveLeaderShip()
	{
		$character = $this->auth->user()->character;
		$clan = $character->clan()->first_or_die();
		$newLider = $clan->members()->find_or_die(Input::get('id'));
		
		if ( $character->can_give_leadership_to($newLider) )
		{
			$character->give_leadership_to($newLider);
			
			$this->session->flash('message', "Le haz dado el liderazgo del grupo a {$newLider->name}");
			
			return Redirect::to('authenticated/index');
		}
		
		return Redirect::back();
	}
}