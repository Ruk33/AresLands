<div class="row">
    <h2>Lista de torneos</h2>

    <table class="table table-striped brown-table">
        <thead>
            <tr>
                <th>Nombre del torneo</th>
                <th>Grupo ganador</th>
            </tr>
        </thead>

        <tbody>
        @foreach ( $tournaments as $tournament )
            <tr>
                <td><a href="{{ URL::to_route("get_authenticated_tournament_show", array($tournament->id)) }}">{{ $tournament->name }}</a></td>
                <td>
                    @if ( $winner = $tournament->get_clan_winner() )
                        {{ $winner->get_link() }}
                    @else
                        Ninguno
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>