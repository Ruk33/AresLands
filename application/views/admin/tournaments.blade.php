<ul>
	@foreach ( $tournaments as $tournament )
	<li><a href="{{ URL::to('admin/tournament/' . $tournament->id) }}">{{ $tournament->name }}</a></li>
	@endforeach
</ul>