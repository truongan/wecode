<table class="wecode_table table table-striped table-bordered table-sm">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th><small>Username</small></th>
            <th>Name</th>
            @foreach ($problems as $problem)
            <th>
                <a class="small" href="#">{{ $problem->pivot->problem_name }}</a>
                <br>
                <a class="text-light" href="#">{{ $problem->pivot->score }}</a>
            </th>
            @endforeach
            <th>
                Total<br>
                <small>{{ $total_score }}</small>
            </th>
            <th>
                Total<br>accepted
            </th>
        </tr>
    </thead>
   
    @foreach ($scoreboard['username'] as $sc_username)
        <tr>
        <td>{{ $loop->index }}</td>
        <td>{{ $sc_username }}</td>
        <td><a class="text-muted small" href="#" >{{ $names[$sc_username] }}</a></td>
        @foreach ($problems as $problem)
        <td>
            @if (isset($scores[$sc_username][$problem->id]->score))
                <a href="#"
                @if ($scores[$sc_username][$problem->id]->fullmark == true)
                    class="text-success" >
                        {{ $scores[$sc_username][$problem->id]->score }}
                @else
                    class="text-danger">
                        {{ $scores[$sc_username][$problem->id]->score }}*
                @endif
                    </a><br/>
                @if ($scores[$sc_username][$problem->id]->late > 0)
                    <span class="small text-warning" title="Delay time" >{{ time_hhmm($scores[$sc_username][$problem->id]->late) }}**</span>
                @else
                    <span class="small" title="Time">{{ time_hhmm($scores[$sc_username][$problem->id]->time) }}</span>
                @endif
            @else
                -
            @endif
        </td>
        @endforeach
        <td>
            <a class="text-muted" href="#" >
                <span>{{ $scoreboard['score'][$loop->index] }}</span>
                <br>
                <span class="small" title="Total Time + Submit Penalty"> {{($scoreboard['submit_penalty'][$loop->index]) }}</span>
            </a>
        </td>
        <td class="bg-success text-light" >
        <span class="lead"><strong>{{ $scoreboard['accepted_score'][$loop->index] }}</strong></span>
        <br>
        <span class="small" title="Solved : Attack ratio">{{ $scoreboard['solved'][$loop->index]}}:{{ $scoreboard['tried_to_solve'][$loop->index]}}</span>
        </td>
        </tr>
    @endforeach
    
    </table>
    *: Not full mark
    <br/>
    **: Delay time