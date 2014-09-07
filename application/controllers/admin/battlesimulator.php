<?php

class Admin_BattleSimulator_Controller extends Base_Controller
{
    public $layout = 'layouts.game';
	public $restful = true;
    
    public static function register_routes()
    {
        Route::get("admin/battle/simulator", array(
			"uses" => "admin.battlesimulator@index",
			"as"   => "get_admin_battle_simulator_index"
		));
        
        Route::post("admin/battle/simulator/execute", array(
			"uses" => "admin.battlesimulator@execute",
			"as"   => "post_admin_battle_simulator_execute"
		));
    }
    
    public function get_index()
    {        
        $battleSimulator = new BattleSimulator();
        
        $this->layout->title = "Simulador de batallas";
        $this->layout->content = View::make(
            "admin.battlesimulator.index",
            compact("battleSimulator")
        );
    }
    
    public function post_execute()
    {
        $battleSimulator = new BattleSimulator();
        
        $battleSimulator->loadDataFromInput(Input::all());
        $battleSimulator->startSimulations();
        
        $this->layout->title = "Simulador de batallas";
        $this->layout->content = View::make(
            "admin.battlesimulator.executed",
            compact("battleSimulator")
        );
    }
}
