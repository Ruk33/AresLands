<?php

class Trade extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'trades';
	public static $key = 'id';

	protected $rules = array(
		//'seller_id' => 'required|exists:characters,id|different:buyer_id',
		'buyer_id' => 'required|exists:characters,id|different:seller_id',
		'item_id' => 'required',
		'amount' => 'required|numeric|min:1',
		'price_copper' => 'required|numeric',
	);

	protected $messages = array(
		//'seller_id_required' => 'El vendedor es requerido',
		//'seller_id_exists' => 'El vendedor debe exister',
		//'seller_id_different' => 'El vendedor y el comprador no pueden ser el mismo',

		'buyer_id_required' => 'El comprador es requerido',
		'buyer_id_exists' => 'El comprador no existe',
		'buyer_id_different' => 'El vendedor y el comprador no pueden ser el mismo',

		'item_id_required' => 'El objeto es requerido',

		'amount_required' => 'El monto es requerido',
		'amount_numeric' => 'El monto es incorrecto (solo números)',
		'amount_min' => 'El mínimo debe ser 1',

		'price_copper_required' => 'El precio es requerido',
		'price_copper_numeric' => 'El precio es incorrecto (solo números)',
	);

	public function seller()
	{
		return $this->belongs_to('Character', 'seller_id');
	}

	public function buyer()
	{
		return $this->belongs_to('Character', 'buyer_id');
	}

	public function item()
	{
		return $this->belongs_to('Item', 'item_id');
	}
}