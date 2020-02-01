<div class="col-12">
	{{-- {% if ok %} --}} Nếu add thành công thì hiển thị
		<div class="alert alert-success">These <ok|length> user(s) added successfully:</p>
		<ol>
			{{-- {% for item in ok %} --}}
			<li>
				<span class="text-muted">Usename: </span> item[0]  
				<span class="text-muted">Email: </span> item[1]  
				<span class="text-muted">Password: </span><code> item[2] </code> 
				<span class="text-muted">Role:</span>  item[3] ( item[4] )
			</li>
			{{-- {% endfor %} --}}
		</ol>
		{{-- {% endif %} --}}
		{{-- {% if error %} --}} Nếu lỗi thì hiển thị
		<div class="alert alert-danger">Error adding these error|length user(s):</div>
		<ol>
			{{-- {% for item in error %} --}}
				<li>
					<span class="text-muted">Usename: </span> item[0]  
					<span class="text-muted">Email: </span> item[1]  
					<span class="text-muted">Password: </span><code> item[2] </code> 
					<span class="text-muted">Role:</span> item[3] ( item[4] )
				</li>
			{{-- {% endfor %} --}}
		</ol>
	{{-- {% endif %} --}}
</div>