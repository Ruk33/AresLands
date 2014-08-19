<?php

/**
 * En el controlador Action, implementamos todas aquellas acciones
 * menores que no tienen mucha complejidad y/o extension
 */
class Authenticated_Action_Controller extends Authenticated_Base
{
    /**
	 *
	 * @var Zone
	 */
    protected $zone;

    public static function register_routes()
    {
        Route::get("authenticated/action/explore", array(
            "uses" => "authenticated.action@explore",
            "as"   => "get_authenticated_action_explore"
        ));

        Route::post("authenticated/action/explore", array(
            "uses" => "authenticated.action@explore",
            "as"   => "post_authenticated_action_explore"
        ));

        Route::get("authenticated/action/travel", array(
            "uses" => "authenticated.action@travel",
            "as"   => "get_authenticated_action_travel"
        ));

        Route::post("authenticated/action/travel", array(
            "uses" => "authenticated.action@travel",
            "as"   => "post_authenticated_action_travel"
        ));
    }

    /**
	 *
	 * @param Zone $zone
	 * @param Character $character
	 */
    public function __construct(Zone $zone, Character $character)
    {
        $this->zone = $zone;
        $this->character = $character;

        parent::__construct();
    }

    public function get_explore()
    {
        $this->layout->title = "Explorar";
        $this->layout->content = View::make("authenticated.explore");
    }

    public function post_explore()
    {
        $character = $this->character->get_logged();
        $time = Input::get('time');

        if ( ! $character->can_explore() ) {
            Session::flash("error", "Aun no puedes explorar");
        } else {
            $min = Config::get('game.min_explore_time');
            $max = Config::get('game.max_explore_time');

            if ($time >= $min && $time <= $max) {
                $character->explore($time * 60);
            }
        }

        return \Laravel\Redirect::to_route("get_authenticated_index");
    }

    public function get_travel()
    {
        $character = $this->character->get_logged();
        $zones = $character->get_travel_zones();
        $exploringTime = $character->exploring_times()->lists("time", "zone_id");

        $this->layout->title = "Viajar";
        $this->layout->content = View::make("authenticated.travel", compact(
            "character", "zones", "exploringTime"
        ));
    }

    public function post_travel()
    {
        $zone = $this->zone->find_or_die(Input::get("id"));
        $character = $this->character->get_logged();
        $canTravel = $character->can_travel($zone);

        if ($canTravel === true) {
            $character->travel_to($zone);
            Session::flash("success", "Haz comenzado tu viaje a {$zone->name}");
        } else {
            Session::flash("error", $canTravel);
        }

        return Laravel\Redirect::to_route("get_authenticated_index");
    }
}
