
<div class="col-12">
	@if (count($ok)>0)
		<div class="alert alert-success">These {{count($ok)}} user(s) added successfully:
		<ol>
			@foreach ($ok as $item)
			<li>
				<span class="text-muted">Usename: {{$item[0]}} </span> 
				<span class="text-muted">Email: {{$item[1]}}</span>   
				<span class="text-muted">Password: </span><code> {{$item[2]}} </code> 
				<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
			</li>
			@endforeach
		</ol>
		</div>
	@endif
	@if(count($error)>0)
		<div class="alert alert-danger">Error adding these {{count($error)}} user(s):
		<ol>
			@foreach ($error as $item)
				<li>
					<span class="text-muted">Usename: {{$item[0]}} </span> 
					<span class="text-muted">Email: {{$item[1]}}</span>   
					<span class="text-muted">Password: </span><code> {{$item[2]}} </code> 
					<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
					<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
					@foreach ($item[5] as $message)
						<p>{{$loop->index + 1}} - {{$message}}</p>
					@endforeach
				</li>
			@endforeach
		</ol>
		</div>
	@endif
</div>