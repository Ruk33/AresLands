<div class="alert-center text-center" style="margin: 0 auto;">
    <div class="alert-top"></div>

    <div class="alert-content">
        <div ng-controller="CharacterCreationController" style="padding-bottom: 50px;">
            <h2>
                <img src="<?php echo URL::base(); ?>/img/icons/race/<?php echo $race; ?>_male.jpg" alt="<?php echo $race; ?>">
                ¡Último paso para jugar!
            </h2>

            <form name="form" novalidate>
                <div>
                    <label class="control-label" for="name">Nombre de tu personaje</label>
                    <input type="text" name="name" id="name" ng-model="character.name" ng-minlength="3" ng-maxlength="10" class="input-block-level" required>

                    <div ng-show="form.name.$dirty && form.name.$invalid" class="alert alert-error">
                        <strong>Oops!</strong>
                        <ul class="unstyled">
                            <li ng-show="form.name.$error.required"><small>El nombre es requerido.</small></li>
                            <li ng-show="form.name.$error.minlength"><small>El nombre debe tener al menos 3 carácteres.</small></li>
                            <li ng-show="form.name.$error.maxlength"><small>El nombre no debe tener mas de 10 carácteres.</small></li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="control-label" for="gender">Género de tu personaje</label>
                    <select name="gender" id="gender" ng-model="character.gender" class="input-block-level" required>
                        <option value="male">Masculino</option>
                        <option value="female">Femenino</option>
                    </select>
                </div>

                <input type="text" name="race" id="race" ng-model="character.race" ng-show="false" ng-init="character.race='<?php echo $race; ?>'">

                <div ng-show="errorDiv" class="alert alert-error error-icon">
                    <strong>Oops!</strong>
                    <ul class="unstyled" style="margin-left: 10px;">
                        <li ng-repeat="error in errors">
                            <small>[[ error ]]</small>
                        </li>
                    </ul>
                </div>
                
                <div class="pull-left">
                    <span class="ui-button button pull-left">
                        <i class="button-icon cross"></i>
                        <span class="button-content">
                            <a href="<?php echo URL::to('charactercreation/race'); ?>">Elegir otra raza</a>
                        </span>
                    </span>
                </div>

                <div class="pull-right">
                    <span class="ui-button button pull-left">
                        <i class="button-icon check"></i>
                        <span class="button-content">
                            <button ng-click="sendForm(character)" ng-disabled="form.$invalid" data-loading-text="Cargando..." class="ui-button ui-input-button">¡Jugar!</button>
                        </span>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="alert-bot"></div>
</div>

<script src="<?php echo URL::base(); ?>/js/controllers/CharacterCreationController.js"></script>