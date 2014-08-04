Parece que tienes buen ojo para los negocios. {{ $buyer->get_link() }} se vio interesado por tu oferta de
{{ $trade->item->name }} y te ha pagado {{ Item::get_divided_coins($trade->price_copper)['text'] }}
monedas que exigias (se ha descontado el {{ $trade->get_commission_percentage() }}% de comision).