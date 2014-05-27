<div class="row orb-content">
	<div class="dialog-box">
		<h2>Orbes</h2>
		<p>
			<i>
				Orbes... antiguas y poderosas piedras cuyo núcleo reciéntemente ha despertado nuevamente.
				Se dice que quien posea estos raros artilugios obtendrá regalos de extrañas formas.
			</i>
		</p>
		<p>Si quieres hacerte con uno de estos extraños objetos, ¡deberás atacar a su poseedor y vencerlo!.</p>
	</div>
    <table class="table table-striped brown-table">
        <thead>
            <tr class="text-left">
                <th></th>
                <th>Nombre</th>
                <th>Poseedor</th>
                <th>Lo tiene desde</th>
                <th>Ultimo atacante</th>
            </tr>
        </thead>

        <tbody>
            @foreach ( $orbs as $orb )
            <tr>
                <td class="span1"><img src="{{ URL::base() }}/img/icons/orbs/{{ $orb->id }}.png" width="32" height="32" /></td>
                <td data-toggle="tooltip" data-original-title="{{ $orb->get_tooltip() }}">{{ $orb->name }}</td>
                <td>
                    @if ( $orb->owner_character )
                        {{ $orb->owner()->select(array('name'))->first()->get_link() }}
                    @else
                        Nadie
                        
                        @if ( $orb->can_be_stolen_by($character) )
							<div><a href="{{ URL::to('authenticated/claimOrb/' . $orb->id) }}">¡Reclamar orbe!</a></div>
						@endif
                    @endif
                </td>
                <td>
                    @if ( $orb->owner_character )
                        {{ date('H:i:s d/m/y', $orb->acquisition_time) }}
                    @else
                        --:--:-- --/--/--
                    @endif
                </td>
                <td>
                    @if ( $orb->last_attack_time && $orb->last_attacker )
                        {{ date('H:i:s d/m/y', $orb->last_attack_time) }} por 
                        {{ $orb->attacker()->select(array('name'))->first()->get_link() }}
                    @else
                        Nadie
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>