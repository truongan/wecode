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
        <td>{{ $sc_username }}</td>
        <td><a class="text-muted small" href="#" >{{ $names[$sc_username] }}</a></td>
        @foreach ($problems as $problem)
        <td>
            @if (isset($scores[$sc_username][$problem->id]['score']))
                <a href="{{ route('submissions.index', ['assignment_id' => $assignment_id, 'problem_id' => $problem->id, 'user_id' => $scoreboard['user_id'][$i] , 'choose' => 'all']) }}"
                @if ($scores[$sc_username][$problem->id]['fullmark'] == true)
                    class="text-success" >
                        {{ $scores[$sc_username][$problem->id]['score'] }}
                @else
                    class="text-danger">
                        {{ $scores[$sc_username][$problem->id]['score'] }}*
                @endif
                <br/>
                Tried: {{$number_of_submissions[$sc_username][$problem->id]}}
                    </a>
                    <br/>
                    {{-- {{ dd($scores[$sc_username][$problem->id]['late']->forHumans() )}} --}}
                @if ($scores[$sc_username][$problem->id]['late']->seconds > 0)
                    <span class="small text-warning" title="Delay time" >{{ $scores[$sc_username][$problem->id]['late']->forHumans(['short' => true]) }}**</span>
                @else
                    <span class="small" title="Time">{{ $scores[$sc_username][$problem->id]['time']->forHumans(['short' => true]) }}</span>
                @endif
            @else
                -
            @endif
        </td>
        @endforeach
        <td>

                <span>{{ $scoreboard['score'][$loop->index] }}</span>
                <br>
                <span class="small" title="Total Time + Submit Penalty"> Penalty time: {{($scoreboard['submit_penalty'][$loop->index]) }}</span>

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