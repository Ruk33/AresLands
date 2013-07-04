<?php

class Clan extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clans';
	public static $key = 'id';

	protected $rules = array(
		'name' => 'required|between:3,35|unique:clans',
	);

	protected $messages = array(
		'name_required' => 'El nombre es requerido',
		'name_between' => 'El nombre debe tener entre 3 y 35 carácteres',
		'name_unique' => 'Ya hay un grupo con ese nombre',
	);

	public function get_members()
	{
		return Character::select(array('name', 'race', 'gender', 'level'))->where('clan_id', '=', $this->id)->get();
	}

	public function get_link()
	{
		return '<a href="' . URL::to('authenticated/clan/' . $this->id ) . '">' . $this->name . '</a>';
	}

	public function lider()
	{
		return $this->belongs_to('Character', 'leader_id');
	}

	public function petitions()
	{
		return $this->has_many('ClanPetition', 'clan_id');
	}
}