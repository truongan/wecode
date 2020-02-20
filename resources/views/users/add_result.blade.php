<div class="col-12">
	
		<div class="alert alert-success">These <ok|length> user(s) added successfully:</p>
			<ol>
				@forelse ($ok as $item)
				<li>
					<span class="text-muted">Usename: {{$item[0]}} </span> 
					<span class="text-muted">Email: {{$item[1]}}</span>   
					<span class="text-muted">Password: </span><code> {{$item[2]}} </code> 
					<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
				</li>	
				@empty
					<p> no user sucess</p>
				@endforelse
			</ol>
		</div>
</div>
<div class="col-12">
	
		<div class="alert alert-success">These <ok|length> user(s) added error:</p>
			<ol>
				@forelse ($error as $item)
				<li>
					<span class="text-muted">Usename: {{$item[0]}} </span> 
					<span class="text-muted">Email: {{$item[1]}}</span>   
					<span class="text-muted">Password: </span><code> {{$item[2]}} </code> 
					<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
					<span class="text-muted">Role:</span>  {{$item[3]}} ( {{$item[4]}} )
					@foreach ($item[5] as $message)
						{{$message}}
					@endforeach
				</li>	
				@empty
					<p> no user sucess</p>
				@endforelse
			</ol>
		</div>
</div>
