<table class="wecode_table table table-striped table-bordered table-sm">
    <thead class="thead-dark">
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
   
    @foreach ($scoreboard['username'] as $i => $sc_username)
    <tr>
        <td>{{ $loop->index + 1}}</td>
        <td> <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => 'all', 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}" >{{ $sc_username }}</a></td>
        <td>{{ $names[$sc_username] }}</td>
        <td>{{ $scoreboard['lops'][$sc_username] ?? 'none' }}</td>
        <td>

                <span>{{ $scoreboard['score'][$loop->index] }}</span>
                <p class="excess">
                    <span class="small" title="Total Time + Submit Penalty">{{($scoreboard['submit_penalty'][$loop->index]->cascade()->forHumans(['short' => true]) ) }}</span>
                </p>

        </td>
        <td class="bg-success text-white" >
        <span class="lead"><strong>{{ $scoreboard['accepted_score'][$loop->index] }}</strong></span>
        <p class="excess">
            <span class="small" title="Solved : Attack ratio">{{ $scoreboard['solved'][$loop->index]}}:{{ $scoreboard['tried_to_solve'][$loop->index]}}</span>
        </p>
        </td>
        @foreach ($problems as $problem)
        <td>
            @if (isset($scores[$sc_username][$problem->id]['score']))
                <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}"
                    class = "lead 
                    @if ($scores[$sc_username][$problem->id]['fullmark'] == true)
                        text-success" >
                            {{ $scores[$sc_username][$problem->id]['score'] }}
                    @else
                        text-danger">
                            {{ $scores[$sc_username][$problem->id]['score'] }}
                    @endif
                </a>
                <p class="excess">
                    <span class="small text-info" title="Total tries and time to final submit">
                    {{$number_of_submissions[$sc_username][$problem->id]}}
                        - </span>

                    @if ($scores[$sc_username][$problem->id]['late']->totalSeconds > 0)
                        <span class="text-warning">{{ $scores[$sc_username][$problem->id]['late']->forHumans(['short' => true]) }}</span>
                    @else
                        <span class="small text-info">{{ $scores[$sc_username][$problem->id]['time']->forHumans(['short' => true]) }}</span>
                    @endif
                </p>
            @else
                -
            @endif
        </td>
        @endforeach
        
    </tr>
    @endforeach

    <tr class="bg-dark">
        <th colspan="6"></th>
        @foreach ($problems as $problem)
        <th>
            <a class="small" href="{{ route('assignments.show', ['assignment'=>$assignment_id, 'problem_id'=> $problem->id]) }}">{{ $problem->pivot->problem_name }}</a>
            {{-- <a class="small" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => 'all' , 'choose' => 'final']) }}">{{ $problem->pivot->problem_name }}</a> --}}
            <br>
            <a class="text-light" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' =>'all' , 'choose' => 'final']) }}">{{ $problem->pivot->score }}</a>
        </th>
        @endforeach

    </tr>
    <tr class="bg-dark text-light">
        <td colspan="6">Solved/tries</td>
        @foreach ($problems as $p)
        <td>
            {{$statistics[$p->id]->solved}} / {{$statistics[$p->id]->tries}}  ( {{ round($statistics[$p->id]->solved*100/ $statistics[$p->id]->tries, 1) }}% )
        </td>
        @endforeach
    </tr>
    <tr class="bg-dark text-light">
        <td colspan="6">Solved users/tries users</td>
        @foreach ($problems as $p)
        <td>
            {{$statistics[$p->id]->solved_user}} / {{$statistics[$p->id]->tries_user}}  ( {{ round($statistics[$p->id]->solved_user*100/ $statistics[$p->id]->tries_user, 1) }}% )
        </td>
        @endforeach
    </tr>
    <tr class="bg-dark text-light">
        <td colspan="6">Average tries per users</td>
        @foreach ($problems as $p)
        <td>
            {{ round($statistics[$p->id]->tries / $statistics[$p->id]->tries_user, 2) }}
        </td>
        @endforeach
    </tr>
    <tr class="bg-dark text-light">
        <td colspan="6">Average tries to solve</td>
        @foreach ($problems as $p)
        <td>
            {{ round($statistics[$p->id]->tries/ $statistics[$p->id]->solved, 2) }}
        </td>
        @endforeach
    </tr>
    </table>