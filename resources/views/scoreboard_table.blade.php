<table class="wecode_table table table-striped table-bordered table-sm">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th><small>Username</small></th>
            <th>Name</th>
            @foreach ($problems as $problem)
            <th>
                <a class="small" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => 'all' , 'choose' => 'final']) }}">{{ $problem->pivot->problem_name }}</a>
                <br>
                <a class="text-light" href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' =>'all' , 'choose' => 'all']) }}">{{ $problem->pivot->score }}</a>
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
   
    @foreach ($scoreboard['username'] as $i => $sc_username)
        <tr>
        <td>{{ $loop->index + 1}}</td>
        <td> <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => 'all', 'user_id' => $scores[$sc_username]['id'] , 'choose' => 'all']) }}" >{{ $sc_username }}</a></td>
        <td>{{ $names[$sc_username] }}</td>
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
                            {{ $scores[$sc_username][$problem->id]['score'] }}*
                    @endif
                </a>
                <br/>
                <span class="small" title="Total tries and time to final submit">
                {{$number_of_submissions[$sc_username][$problem->id]}}
                    - 

                @if ($scores[$sc_username][$problem->id]['late']->totalSeconds > 0)
                    <span class="text-warning">{{ $scores[$sc_username][$problem->id]['late']->forHumans(['short' => true]) }}**</span>
                @else
                    <span class="small">{{ $scores[$sc_username][$problem->id]['time']->forHumans(['short' => true]) }}</span>
                @endif
                </span>
            @else
                -
            @endif
        </td>
        @endforeach
        <td>

                <span>{{ $scoreboard['score'][$loop->index] }}</span>
                <br>
                <span class="small" title="Total Time + Submit Penalty">{{($scoreboard['submit_penalty'][$loop->index]->cascade()->forHumans(['short' => true]) ) }}</span>

        </td>
        <td class="bg-success text-light" >
        <span class="lead"><strong>{{ $scoreboard['accepted_score'][$loop->index] }}</strong></span>
        <br>
        <span class="small" title="Solved : Attack ratio">{{ $scoreboard['solved'][$loop->index]}}:{{ $scoreboard['tried_to_solve'][$loop->index]}}</span>
        </td>
        </tr>
    @endforeach
    
    </table>