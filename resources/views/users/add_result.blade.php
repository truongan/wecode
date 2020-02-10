
<div class="col-12">
	{{-- {% if ok %} --}} Nếu add thành công thì hiển thị
		<div class="alert alert-success">These <ok|length> user(s) added successfully:</p>
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