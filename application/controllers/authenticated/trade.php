<?php

class Authenticated_Trade_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var Trade
	 */
	protected $trade;
	
	/**
	 *
	 * @var TradeItem
	 */
	protected $tradeItem;
	
	public static function register_routes()
	{
		Route::get("authenticated/trade", array(
			"uses" => "authenticated.trade@index",
			"as"   => "get_authenticated_trade_index"
		));
		
		Route::get("authenticated/trade/category/(:any)", array(
			"uses" => "authenticated.trade@category",
			"as"   => "get_authenticated_trade_category"
		));
		
		Route::get("authenticated/trade/new", array(
			"uses" => "authenticated.trade@new",
			"as"   => "get_authenticated_trade_new"
		));
		
		Route::post("authenticated/trade/new", array(
			"uses" => "authenticated.trade@new",
			"as"   => "post_authenticated_trade_new"
		));
		
		Route::post("authenticated/trade/cancel", array(
			"uses" => "authenticated.trade@cancel",
			"as"   => "post_authenticated_trade_cancel"
		));
		
		Route::post("authenticated/trade/buy", array(
			"uses" => "authenticated.trade@buy",
			"as"   => "post_authenticated_trade_buy"
		));
	}
	
	public function __construct(Trade $trade, TradeItem $tradeItem, Character $character)
	{
		$this->trade = $trade;
		$this->tradeItem = $tradeItem;
		$this->character = $character;
		
		parent::__construct();
	}
	
	public function get_index()
	{
		return Laravel\Redirect::to_route("get_authenticated_trade_category", array("all"));
	}
	
	public function get_category($filter)
	{
		$availableFilters = array("self", "weapon", "armor", "consumible", "all");
		
		if ( ! in_array($filter, $availableFilters))
		{
			$filter = "all";
		}
		
		$character = $this->character->get_logged();
		
		switch ( $filter )
		{
			case "all":
				$trades = $this->trade->with(array("trade_item", "trade_item.item"))->get_valid()->get();
				break;
			
			case "self":
				$trades = $character->trades()->with(array("trade_item", "trade_item.item"))->get();
				break;
			
			default:
				$trades = $this->trade->filter_by_item_class($filter)->select(array("trades.*"))->get();
		}

		$this->layout->title = "Comercios";
		$this->layout->content = View::make('authenticated.trades', compact("trades", "character"));
	}
	
	public function get_new()
	{
		$character = $this->character->get_logged();
		
		if ( ! $character->can_trade() )
		{
			Session::flash("error", "No tienes ningun objeto para comerciar");
			return Laravel\Redirect::to_route("get_authenticated_trade_index");
		}
		
		$characterItems = $character->tradeable_items()->select(array('character_items.*'))->get();
		
		$this->layout->title = "Nuevo comercio";
		$this->layout->content = View::make("authenticated.newtrade", compact("character", "characterItems"));
	}
	
	public function post_new()
	{
		$seller = $this->character->get_logged();
		
		if ( ! $seller->can_trade() )
		{
			return Laravel\Redirect::to_route("get_authenticated_index");
		}
		
		$nonNumericReg = "/[^\d]+/";
		
		// Sacamos todos los caracteres que no sean numeros y ademas
		// completamos 0 (ceros) en caso de faltar (por ejemplo, 1 -> 01)
		$price = 
			str_pad(preg_replace($nonNumericReg, "", Input::get("gold")),   2, "0", STR_PAD_LEFT) .
			str_pad(preg_replace($nonNumericReg, "", Input::get("silver")), 2, "0", STR_PAD_LEFT) .
			str_pad(preg_replace($nonNumericReg, "", Input::get("copper")), 2, "0", STR_PAD_LEFT);
				
		$trade = $this->trade->create_instance(array_merge(
			array(
				"seller_id"    => $seller->id,
				"price_copper" => $price,
				"until"        => time() + Input::get("duration") * 60 * 60,
				"clan_id"      => Input::get("only_clan") ? $seller->clan_id : 0
			),
			Input::only("duration", "amount", "item_id")
		));

		if ( $trade->validate() )
		{
			$characterItem = $seller->items()->find(Input::get("item_id"));
			
			$trade->trade_item()->insert(
				$this->tradeItem->create_instance(array(
					"item_id" => $characterItem->item_id,
					"data"    => $characterItem->get_attribute("data")
				))
			);
			
			$trade->save();

			$characterItem->count -= Input::get("amount");
			$characterItem->save();

			Session::flash("success", "Comercio creado con éxito");
			return Laravel\Redirect::to_route("get_authenticated_trade_index");
		}
		
		Session::flash("errors", $trade->errors->all());
		return Laravel\Redirect::to_route("get_authenticated_trade_new")->with_input();
	}
	
	public function post_cancel()
	{
		$character = $this->character->get_logged();
		$trade = $character->trades()->find_or_die(Input::get("id"));
		
		if ( $trade->cancel() )
		{
			Session::flash("success", "El comercio ha sido cancelado");
		}
		else
		{
			Session::flash("error", "El comercio no pudo ser cancelado. Verifica que tengas espacio en tu inventario");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_trade_index");
	}
	
	public function post_buy()
	{
		$character = $this->character->get_logged();
		$trade = $this->trade->find_or_die(Input::get("id"));
		
		if ( $trade->buy($character) )
		{
			Session::flash("success", "Compraste el objeto exitosamente");
		}
		else
		{
			Session::flash("error", "No puedes comprar el objeto porque o no tienes espacio en tu inventario o no tienes suficientes monedas");
		}
		
		return Laravel\Redirect::to_route("get_authenticated_trade_index");
	}
}