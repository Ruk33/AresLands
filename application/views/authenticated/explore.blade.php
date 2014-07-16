@if ( Session::has("error") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>¡Alto explorador!</h4>
        <p>{{ Session::get("error") }}</p>
    </div>
</div>
@endif

<h2>Explorar</h2>
<p>
    Éstas son tierras de aventuras y tesoros ocultos. Explora el terreno a fondo
    para descubrir nuevos objetos, oro, ¡y quién sabe con que mas te podrás topar!. 
    Recuerda que cuanto mas nivel tengas, mejor serán tus exploraciones.
</p>

<div class="text-center">
    <h4>¿Cuánto tiempo quieres explorar?</h4>
    {{ Form::open(URL::to_route("post_authenticated_action_explore")) }}
        {{ Form::token() }}
        
        <div>
            {{ Form::select('time', array(5 => '5 minutos', 10 => '10 minutos', 20 => '20 minutos', 30 => '30 minutos', 60 => '1 hora', 120 => '2 horas', 180 => '3 horas', 240 => '4 horas')) }}
        </div>
        
        <div class="ui-button button">
            <i class="button-icon map"></i>
            <span class="button-content">
                {{ Form::submit('Comenzar a explorar', array('class' => 'ui-button ui-input-button')) }}
            </span>
        </div>
    {{ Form::close() }}
</div>