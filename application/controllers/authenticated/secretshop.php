<?php

class Authenticated_SecretShop_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var VipFactory
	 */
	protected $vipFactory;
	
	public function __construct(VipFactory $vipFactory)
	{
		parent::__construct();
		
		$this->vipFactory = $vipFactory;
	}
	
	public function get_index()
	{
		$vipObjects = $this->vipFactory->get_all();
		
		$this->layout->title = 'Mercado secreto';
		$this->layout->content = View::make('authenticated.secretshop', compact('vipObjects'));
	}
	
	public function post_buy()
	{
		$vipObject = $this->vipFactory->get(Input::get('id'));
		
		if ( ! $vipObject )
		{
			return Response::error('404');
		}
		
		$character = $this->auth->user()->character;
		$attributes = Input::all();
		$validator = $vipObject->get_validator($attributes);
		
		if ( $validator->fails() )
		{
			$this->session->flash('errors', $validator->errors->all());
			return Redirect::to('authenticated/secretShop');
		}
		
		if ( ! $this->auth->user()->consume_coins($vipObject->get_price()) )
		{
			$this->session->flash('errors', array('No tienes suficientes IronCoins para comprar este objeto'));
			return Redirect::to('authenticated/secretShop');
		}
		
		$vipObject->execute($character, $attributes);
		
		$this->layout->title = '¡Compra exitosa!';
		$this->layout->content = View::make('authenticated.buyfromsecretshop', compact('vipObject'));
	}
}