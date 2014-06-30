<?php

class Authenticated_SecretShop_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var VipFactory
	 */
	protected $vipFactory;
	
	public static function register_routes()
	{
		Route::get("authenticated/secretShop", array(
			"uses" => "authenticated.secretshop@index",
			"as"   => "get_authenticated_secret_shop_index"
		));
		
		Route::post("authenticated/secretShop/buy", array(
			"uses" => "authenticated.secretshop@buy",
			"as"   => "post_authenticated_secret_shop_buy"
		));
	}
	
	public function __construct(VipFactory $vipFactory, Character $character)
	{
		$this->vipFactory = $vipFactory;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{
		$vipObjects = $this->vipFactory->get_all();
		
		$this->layout->title = 'Mercado secreto';
		$this->layout->content = View::make('authenticated.secretshop', compact('vipObjects'));
	}
	
	public function post_buy()
	{
		$character = $this->character->get_logged();
		$vipObject = $this->vipFactory->get(Input::get("id"));
		
		$vipObject->set_attributes($character, Input::all());
		
		$validator = $vipObject->get_validator();
		
		if ( $validator->fails() )
		{
			Session::flash("errors", $validator->errors->all());
			return \Laravel\Redirect::to_route("get_authenticated_secret_shop_index");
		}
		
		if ( ! $vipObject->execute() )
		{
			Session::flash("errors", array("No tienes suficientes IronCoins o hubo un error al procesar la peticion"));
			return \Laravel\Redirect::to_route("get_authenticated_secret_shop_index");
		}
		
		$this->layout->title = "¡Compra exitosa!";
		$this->layout->content = View::make('authenticated.buyfromsecretshop', compact("vipObject"));
	}
}