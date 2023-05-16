@if ($is_freeze)
<button id="magic-btn" class="btn btn-secondary">Click me! 😢</button>
<table class="wecode_table table table-striped table-bordered table-sm">
    <thead class="thead-old table-dark">
        <tr>
            <th>#</th>
            <th><small>Username</small></th>
            <th>Name</th>
            <th>Class</th>
                        <th>
                Total<br>
                <small>{{ $total_score }}</small>
            </th>
            <th>
                Total<br>accepted
            </th>
            @foreach ($problems as $problem)
            <th>
                <a class="small" href="{{ route('assignments.show', ['assignment'=>$assignment_id, 'problem_id'=> $problem->id]) }}">{{ $problem->pivot->problem_name }}</a>
                {{-- <a class="small" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => 'all' , 'choose' => 'final']) }}">{{ $problem->pivot->problem_name }}</a> --}}
                <br>
                <a class="text-light" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' =>'all' , 'choose' => 'final']) }}">{{ $problem->pivot->score }}</a>
            </th>
            @endforeach

        </tr>
    </thead>
   
    @foreach ($scoreboard_freeze['username'] as $i => $sc_username)
    <tr>
        <td>{{ $loop->index + 1}}</td>
        <td><a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => 'all', 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}" >{{ $sc_username }}</a></td>
        <td>{{ $names[$sc_username] }}</td>
        <td>{{ $scoreboard_freeze['lops'][$sc_username] ?? 'none' }}</td>
        <td>

                <span>{{ $scoreboard_freeze['score'][$loop->index] }}</span>
                <p class="excess">
                    <span class="small" title="Total Time + Submit Penalty">{{($scoreboard_freeze['submit_penalty'][$loop->index]->cascade()->forHumans(['short' => true]) ) }}</span>
                </p>

        </td>
        <td class="bg-success text-white" >
        <span class="lead"><strong>{{ $scoreboard_freeze['accepted_score'][$loop->index] }}</strong></span>
        <p class="excess">
            <span class="small" title="Solved : Attack ratio">{{ $scoreboard_freeze['solved'][$loop->index]}}:{{ $scoreboard_freeze['tried_to_solve'][$loop->index]}}</span>
        </p>
        </td>
        @foreach ($problems as $problem)
        @if (isset($scores[$sc_username][$problem->id]['score']))
            @if ($scores[$sc_username][$problem->id]['is_freeze'] == 1)
                <td class="bg-warning">
                <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}"
                    class = "lead text-white">
                    {{(int)$number_of_submissions[$sc_username][$problem->id] - (int)$number_of_submissions_during_freeze[$sc_username][$problem->id]}} + {{$number_of_submissions_during_freeze[$sc_username][$problem->id]}} 
                
                    @if ((int)$number_of_submissions[$sc_username][$problem->id] == 1)
                        try
                    @else
                        tries
                    @endif
                </a>
            @else
                @if ($scores[$sc_username][$problem->id]['score'] == 100)
                    <td class="bg-success">
                @else
                    <td class="bg-danger">
                @endif
                    <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}" class="lead text-white">
                        {{ $scores[$sc_username][$problem->id]['score'] }}
                    </a>
                    <p class="excess">
                        <span class="small text-white" title="Total tries and time to final submit">
                            {{$number_of_submissions[$sc_username][$problem->id]}}
                            - 
                        </span>

                        @if ($scores[$sc_username][$problem->id]['late']->totalSeconds > 0)
                            <span class="text-white">{{ $scores[$sc_username][$problem->id]['late']->forHumans(['short' => true]) }}</span>
                        @else
                            <span class="small text-white">{{ $scores[$sc_username][$problem->id]['time']->forHumans(['short' => true]) }}</span>
                        @endif
                    </p>
            @endif
            </td>
        @else
            <td>-</td>
        @endif
        @endforeach
        
    </tr>
    @endforeach

    <tfoot class="bg-dark text-light">
        <th colspan="6">Sumarry</th>
        @foreach ($problems as $problem)
        <th>
            <a class="small" href="{{ route('assignments.show', ['assignment'=>$assignment_id, 'problem_id'=> $problem->id]) }}">{{ $problem->pivot->problem_name }}</a>
            {{-- <a class="small" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => 'all' , 'choose' => 'final']) }}">{{ $problem->pivot->problem_name }}</a> --}}
            <br>
            <a class="text-light" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' =>'all' , 'choose' => 'final']) }}">{{ $problem->pivot->score }}</a>
        </th>
        @endforeach
        <tr class="bg-dark text-light">
            <td colspan="6">Solved/tries</td>
            @foreach ($problems as $p)
            <td>
                {{$stat_print[$p->id]->solved_tries}}
            </td>
            @endforeach
        </tr>
        <tr class="bg-dark text-light">
            <td colspan="6">Solved users/tries users/Total users</td>
            @foreach ($problems as $p)
            <td>
            {{$stat_print[$p->id]->solved_tries_users}}
            </td>
            @endforeach
        </tr>
        <tr class="bg-dark text-light">
            <td colspan="6">Average tries per users</td>
            @foreach ($problems as $p)
            <td>
                {{$stat_print[$p->id]->average_tries}}
            </td>
            @endforeach
        </tr>
        <tr class="bg-dark text-light">
            <td colspan="6">Average tries to solve</td>
            @foreach ($problems as $p)
            <td>
                {{$stat_print[$p->id]->average_tries_2_solve}}
            </td>
            @endforeach
        </tr>
    </tfoot>
</table>
@else
    <h1>Freeze time is not occurred.</h1>
@endif