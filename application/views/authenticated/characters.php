<h2>Personajes</h2>

<div class="span11" ng-controller="CharactersController">

	<ul class="thumbnails">
	<li class="span6">
		<div class="thumbnail">
			<div class="caption">
				Buscar por nombre
				<input type="text" class="input-block-level" ng-model="search.name">
			</div>
		</div>
	</li>

	<li class="span6">
		<div class="thumbnail">
			<div class="caption">
				Buscar por nombre de grupo
				<input type="text" class="input-block-level" ng-model="search.clan_name">
			</div>
		</div>
	</li>
	</ul>

	<table class="table table-hover">
		<thead>
			<tr>
				<th>Raza</th>
				<th><a href="" ng-click="predicate='name'; reverse=!reverse;">Nombre</a></th>
				<th><a href="" ng-click="predicate='clan_name'; reverse=!reverse;">Grupo</a></th>
				<th><a href="" ng-click="predicate='pvp_points'; reverse=!reverse;">Puntos de PVP</a></th>
			</tr>
		</thead>

		<tbody>
			<td colspan="4" ng-show="!characters">
				<div class="text-center" style="color: white;" ng-show="!error">
					<img src="<?php echo URL::base(); ?>/img/icons/ajax-loader.gif" alt="">
					Obteniendo datos del servidor
				</div>

				<div class="alert alert-error text-center" ng-show="error">
					[[ error ]]
				</div>
			</td>

			<tr ng-repeat="character in characters | filter:search | orderBy:predicate:reverse">
				<td>
					<div class="icon-race-30 icon-race-30-[[ character.race ]]_[[ character.gender ]]"></div>
				</td>

				<td>
					[[ character.name ]]
				</td>

				<td>
					<span ng-show="character.clan_name">[[ character.clan_name ]]</span>
					<span ng-show="!character.clan_name">Sin grupo</span>
				</td>

				<td>
					[[ character.pvp_points ]]
				</td>
			</tr>
		</tbody>
	</table>
</div>

<script src="<?php echo URL::base(); ?>/js/controllers/CharactersController.js"></script>