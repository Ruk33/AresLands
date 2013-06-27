<?php

class Clan extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'clans';

	protected $rules = [
		'name' => 'required|between:3,20|unique:clans',
	];

	protected $messages = [
		'name_required' => 'El nombre es requerido',
		'name_between' => 'El nombre debe tener entre 3 y 20 carácteres',
		'name_unique' => 'Ya hay un grupo con ese nombre',
	];

	public function get_members()
	{
		return Character::where('clan_id', '=', $this->id)->get();
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