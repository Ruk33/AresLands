<?php

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
Route::get("home", array(
	"uses" => "home@index",
	"as"   => "get_home_index"
));

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::group(array("before" => "auth|hasNoCharacter"), function()
{
	Authenticated_Controller::register_routes();
	Authenticated_Character_Controller::register_routes();
	Authenticated_Talent_Controller::register_routes();
	Authenticated_SecretShop_Controller::register_routes();
	Authenticated_Tournament_Controller::register_routes();
	Authenticated_Quest_Controller::register_routes();
	Authenticated_Orb_Controller::register_routes();
	Authenticated_Message_Controller::register_routes();
	Authenticated_Trade_Controller::register_routes();
	Authenticated_Inventory_Controller::register_routes();
	Authenticated_Npc_Controller::register_routes();
	Authenticated_Battle_Controller::register_routes();
	Authenticated_Action_Controller::register_routes();
	Authenticated_Dungeon_Controller::register_routes();
	Authenticated_Ranking_Controller::register_routes();
	
	// Los que usan Route::group abajo, ya que modifican los filtros
	Authenticated_Clan_Controller::register_routes();
});

Route::get("admin/npcs", array(
	"uses" => "admin.npc@index",
	"as" => "get_admin_npc_index",
));

Route::get("admin/npcs/create", array(
	"uses" => "admin.npc@create",
	"as" => "get_admin_npc_create",
));

Route::post("admin/npcs/create", array(
	"uses" => "admin.npc@create",
	"as" => "post_admin_npc_create",
));

Route::get("admin/npcs/(:num)", array(
	"uses" => "admin.npc@edit",
	"as" => "get_admin_npc_edit",
));

Route::post("admin/npcs/edit", array(
	"uses" => "admin.npc@edit",
	"as" => "post_admin_npc_edit",
));

Route::get("admin/npcs/(:num)/delete", array(
	"uses" => "admin.npc@delete",
	"as" => "get_admin_npc_delete",
));

Route::get('admin/dungeons', array(
	'uses' => 'admin.dungeon@index',
	'as' => 'get_admin_dungeon_index',
));

Route::get('admin/dungeons/create', array(
	'uses' => 'admin.dungeon@create',
	'as' => 'get_admin_dungeon_create',
));

Route::post('admin/dungeons/create', array(
	'uses' => 'admin.dungeon@create',
	'as' => 'post_admin_dungeon_create',
));

Route::get('admin/dungeons/(:num)', array(
	'uses' => 'admin.dungeon@edit',
	'as' => 'get_admin_dungeon_edit',
));

Route::post('admin/dungeons/edit', array(
	'uses' => 'admin.dungeon@edit',
	'as' => 'post_admin_dungeon_edit',
));

Route::get('admin/dungeons/(:num)/delete', array(
	'uses' => 'admin.dungeon@delete',
	'as' => 'get_admin_dungeon_delete',
));

Route::get('admin/generators', array(
	'uses' => 'admin.generator@index',
	'as' => 'get_admin_generator_index'
));

Route::get('admin/generators/item', array(
	'uses' => 'admin.generator@item',
	'as' => 'get_admin_generator_item'
));

Route::post('admin/generators/item', array(
	'uses' => 'admin.generator@item',
	'as' => 'post_admin_generator_item'
));

/*
|--------------------------------------------------------------------------
| Application Controllers
|--------------------------------------------------------------------------
*/

Route::controller('Game');
Route::controller('CharacterCreation');
Route::controller('Home');
Route::controller('Api');
Route::controller('Chat');
Route::controller('Cron');
Route::controller('Admin');

/*
|--------------------------------------------------------------------------
| Application Events
|--------------------------------------------------------------------------
*/
require("events.php");

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/
require("filters.php");