<h2>Orbes</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

<ul class="inline text-center">
	@foreach ( $orbs as $orb )
		<li>
			<h2>{{ $orb->name }}</h2>
			<img src="{{ URL::base() }}/img/icons/orbs/{{ $orb->id }}.png" width="150px" height="150px" data-toggle="tooltip" data-title="{{ $orb->description }}">
			<p><strong>Poseedor:</strong> @if ( $orb->owner_character ) {{ $orb->owner->name }} @else Nadie @endif</p>
		</li>
	@endforeach
</ul>