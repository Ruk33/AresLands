<h2>Explorar</h2>
<p>Estas son tierras de aventuras y de tesoros ocultos. Explora para encontrar tesoros... y quién sabe qué más puedes encontrar. Recuerda que cuanto mayor nivel tengas, tus exploraciones serán más efectivas.</p>

<div class="text-center" style="width: 250px; margin: 50px auto;">
<strong>¿Cuánto tiempo quieres explorar?</strong>
{{ Form::open() }}
	{{ Form::token() }}
	{{ Form::select('time', array(300 => '5 minutos', 600 => '10 minutos', 1200 => '20 minutos', 1800 => '30 minutos', 3600 => '1 hora', 7200 => '2 horas', 10800 => '3 horas', 14400 => '4 horas')) }}
	
	<span class="ui-button button">
		<i class="button-icon map"></i>
		<span class="button-content">
			{{ Form::submit('Comenzar a explorar', array('class' => 'ui-button ui-input-button')) }}
		</span>
	</span>
{{ Form::close() }}
</div>