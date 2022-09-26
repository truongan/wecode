@if (strtolower($submission->status) == "pending")
<div class="btn btn-secondary pending" data-type="result">PENDING</div>
@else 
@if ((count($submission->judgement->mems ?? []) > 0))
    @if (count( array( $submission->judgement->verdicts) ) > 0)
        @foreach ($submission->judgement->verdicts as $v => $c)
            <button  data-type="result"  class="btn btn-sm btn-outline-danger m-2">{{$v}}<span class="badge p bg-info">{{$c}} </span></button>
        @endforeach
    @else
        <div class="btn btn-danger" data-type="result">{{$submission->status}}</div>
    @endif
@else
    <div class="btn btn-danger" data-type="result">{{$submission->status}}</div>
@endif	
@endif