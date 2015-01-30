<h2>Logros</h2>

<div class="row">
    <table class="table table-striped brown-table">
        <thead>
            <tr>
                <th class="span11">Logro</th>
                <th class="span1">Completado</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($achievements as $achievement)
            <tr>
                <td>
                    <div class="span1 text-center">
                        <div class="achievement-icon-box">
                            <img src="{{ $achievement->getIcon() }}" alt=""/>
                            <del></del>
                        </div>
                    </div>

                    @if ($rewards = $achievement->getRewards())
                    <div class="span7">
                    @else
                    <div class="span11">
                    @endif
                        <b>{{ $achievement->getName() }}</b>
                        <p>{{ $achievement->getDescription() }}</p>
                        <div style="position: relative; width: 340px;">
                            <div class="bar-empty-fill">
                                <div id="activityBar" style="width: {{ $progressHelper[$achievement->getId()]->getPercentage() }}%;"></div>
                            </div>
                            <div class="bar-border"></div>
                        </div>
                    </div>

                    @if ($rewards)
                    <div class="span4 text-center">
                        <small>Recompensas</small>
                        <ul class="inline">
                            @foreach ($rewards as $reward)
                            <li>
                                <div class="quest-reward-item" data-toggle="popover" data-placement="top" data-original-title="{{ $reward->get_text_for_tooltip() }}">
                                    <img src="{{ $reward->get_image_path() }}" alt="" />
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
                <td>
                    <div class="text-center">
                        @if (isset($characterAchievements[$achievement->getId()]) && $characterAchievements[$achievement->getId()])
                        <i class="icon-ok icon-white"></i>
                        @else
                        <i class="icon-remove icon-white"></i>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>