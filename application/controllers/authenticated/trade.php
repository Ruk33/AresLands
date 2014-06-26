<?php

class Authenticated_Trade_Controller extends Authenticated_Base
{
	/**
	 *
	 * @var Trade
	 */
	protected $trade;
	
	public static function register_routes()
	{
		
	}
	
	public function __construct(Trade $trade, Character $character)
	{
		parent::__construct();
		
		$this->trade = $trade;
		$this->character = $character;
	}
	
	public function get_index($filter = "all")
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
				$trades = $character->trades()->with(array('trade_item', 'trade_item.item'))->get();
				break;
			
			default:
				$trades = $this->trade->filter_by_item_class($filter)->select(array('trades.*'))->get();
		}

		$this->layout->title = "Comercios";
		$this->layout->content = View::make('authenticated.trades', compact("trades", "character"));
	}
	
	public function get_newTrade()
	{
		$character = Character::get_character_of_logged_user();

		if ( $character->can_trade() )
		{
			$characterItems = $character->tradeable_items()->select(array('character_items.*'))->get();

			$this->layout->title = 'Nuevo comercio';
			$this->layout->content = View::make('authenticated.newtrade')
			->with('character', $character)
			->with('characterItems', $characterItems);
		}
		else
		{
			return Redirect::to('authenticated/trades')
			->with('errorMessages', array('No tienes ningún objeto para comerciar.'));
		}
	}
	
	public function post_cancelTrade()
	{
		$character = Character::get_character_of_logged_user();
		$trade = $character->trades()->where('id', '=', Input::get('id'))->first();

		if ( $trade )
		{
			if ( ! $trade->cancel() )
			{
				return Redirect::to('authenticated/trades')->with('error', array('El comercio no se pudo cancelar. Verifica que tengas espacio en tu inventario.'));
			}
			else
			{
				return Redirect::to('authenticated/trades')->with('success', 'El comercio ha sido cancelado.');
			}
		}
		
		return Redirect::to('authenticated/trades');
	}
	
	public function post_newTrade()
	{
		$sellerCharacter = Character::get_character_of_logged_user(array('id', 'clan_id'));
		
		if ( ! $sellerCharacter->can_trade() )
		{
			return Redirect::to('authenticated/index');
		}
		
		$amount = Input::get('amount');
		
		if ( ! isset($amount[Input::get('item')]) )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		$amount = $amount[Input::get('item')];
		
		$time = Input::get('time');
		
		if ( ! in_array($time, array(8, 16, 24)) )
		{
			return Redirect::to('authenticated/newTrade/');
		}

		$sellerCharacterItem = $sellerCharacter->items()
											   ->where('id', '=', Input::get('item'))
											   ->where('count', '>=', $amount)
											   ->where('location', '=', 'inventory')
											   ->first();

		if ( ! $sellerCharacterItem )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		$item = $sellerCharacterItem->item()
									->select(array('id', 'stackable', 'selleable'))
									->first();
		
		if ( ! $item )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		if ( ! $item->selleable )
		{
			return Redirect::to('authenticated/newTrade/');
		}
		
		/*
		 *	Evitamos que intenten comerciar
		 *	cantidad en un objeto que no puede ser acumulado
		 */
		if ( ! $item->stackable )
		{
			$amount = 1;
		}
		
		$price = '';
		
		foreach ( array(Input::get('gold', '00'), Input::get('silver', '00'), Input::get('copper', '00')) as $coin )
		{
			if ( ! is_numeric($coin) )
			{
				$coin = '00';
			}
			
			if ( strlen($coin) == 1 )
			{
				$coin = '0' . $coin;
			}
			
			$price .= $coin;
		}

		$trade = new Trade();

		$trade->seller_id = $sellerCharacter->id;
		$trade->amount = $amount;
		$trade->price_copper = $price;
		$trade->until = time() + $time * 60 * 60;
		$trade->duration = $time;

		if ( $sellerCharacter->clan_id > 0 && Input::get('only_clan') != 0 )
		{
			$trade->clan_id = $sellerCharacter->clan_id;
		}

		if ( $trade->validate() )
		{
			$tradeItem = new TradeItem();
			
			$tradeItem->item_id = $sellerCharacterItem->item_id;
			$tradeItem->data = $sellerCharacterItem->get_attribute('data');
			
			$tradeItem->save();
			
			$trade->trade_item_id = $tradeItem->id;
			
			$trade->save();

			/*
			 *	Sacamos el objeto al personaje
			 *	vendedor y/o únicamente restamos cantidad
			 */
			$sellerCharacterItem->count -= $amount;

			if ( $sellerCharacterItem->count > 0 )
			{
				$sellerCharacterItem->save();
			}
			else
			{
				/*
				 *	La cantidad es menor a 0
				 *	así que borramos
				 */
				$sellerCharacterItem->delete();
			}

			Session::flash('successMessage', 'Comercio creado con éxito');
			return Redirect::to('authenticated/newTrade/');
		}
		else
		{
			Session::flash('errorMessages', $trade->errors()->all());
			return Redirect::to('authenticated/newTrade/')->with_input();
		}
	}
	
	public function post_buyTrade()
	{
		$character = Character::get_character_of_logged_user();
		$trade = Trade::where('id', '=', Input::get('id'))->first();

		if ( $trade )
		{
			if ( $trade->buy($character) )
			{
				return Redirect::to('authenticated/trades')->with('success', array(
					'Compraste el objeto exitosamente.'
				));
			}
			else
			{
				return Redirect::to('authenticated/trades')->with('error', array(
					'No puedes comprar el objeto porque o no tienes espacio en tu inventario o no tienes suficientes monedas.'
				));
			}
		}
		
		return Redirect::to('authenticated/trades');
	}
}