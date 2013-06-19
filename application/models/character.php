<?php

class Character extends Base_Model
{
	public static $softDelete = true;
	public static $timestamps = true;
	public static $table = 'characters';

	protected $rules = [
		'name' => 'required|unique:Characters|between:3,10',
		'race' => ['required', 'match:/^(dwarf|human|drow|elf)$/'],
		'gender' => ['required', 'match:/^(male|female)$/'],
	];

	protected $messages = [
		'name_required' => 'El nombre del personaje es requerido',
		'name_unique' => 'Ya existe otro personaje con ese nombre',
		'name_between' => 'El nombre del personaje debe tener entre 3 y 10 carácteres',

		'race_required' => 'La raza es requerida',
		'race_match' => 'La raza es incorrecta',

		'gender_required' => 'El género es requerido',
		'gender_match' => 'El género es incorrecto',
	];
}