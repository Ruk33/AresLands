<?php

class Authenticated_SecretShop_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var \Models\Vip\VipRepository
	 */
	protected $vipRepository;
	
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
	
	public function __construct(\Models\Vip\VipRepository $vipRepository, 
                                Character $character)
	{
		$this->vipRepository = $vipRepository;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{
        $character = $this->character->get_logged();
		$vipObjects = $this->vipRepository->where_enabled(true)->get();
		
		$this->layout->title = 'Mercado secreto';
		$this->layout->content = View::make('authenticated.secretshop', compact('vipObjects', 'character'));
	}
	
	public function post_buy()
	{
        $character = $this->character->get_logged();
        $vip = $this->vipRepository->find(Input::get("id"));
        $shop = new \Models\Vip\VipShop($vip);
        
        if (! $shop->buy(Auth::user(), $character, Input::all())) {
            Session::flash("errors", $shop->getErrors());
			return Redirect::to_route("get_authenticated_secret_shop_index");
        }
        
        $this->layout->title = "¡Compra exitosa!";
		$this->layout->content = View::make(
            "authenticated.buyfromsecretshop", 
            array("vipObject" => $vip)
        );
	}
}