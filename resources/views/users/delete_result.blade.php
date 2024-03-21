<div class="col-12">
	@if (count($ok)>0)
		<div class="alert alert-success">These {{count($ok)}} user(s) was deleted successfully:
		<ol>
			@foreach ($ok as $item)
			<li>
				<span class="text-muted">Username: {{$item}}</span> 
			</li>
			@endforeach
		</ol>
		</div>
	@endif
	@if(count($error)>0)
		<div class="alert alert-danger">Error deleting these {{count($error)}} user(s):
		<ol>
			@foreach ($error as $item)
				<li>
					<span class="text-muted">Username: {{$item[0]}}</span> 
					@foreach ($item[1] as $message)
						<p>{{$loop->index + 1}} - {{$message}}</p>
					@endforeach
				</li>
			@endforeach
		</ol>
		</div>
	@endif
	<div class="alert alert-secondary">A total of {{count($ok)  + count($error) }} list processed</div>

</div>