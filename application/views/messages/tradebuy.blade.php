Parece que tienes buen ojo para los negocios. {{ $buyer->get_link() }} se a visto interesado por tu oferta de
{{ $trade->trade_item->item->name }} y te a pagado {{ Item::get_divided_coins($trade->price_copper)['text'] }}
monedas que exigias (se ha descontado el {{ $trade->get_commission_percentage() }}% la comision).