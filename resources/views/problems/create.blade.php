<form method="POST"  action="{!! route('problems.store') !!}">
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
	<p class="input_p">
		<label for="form_title" class="tiny">name:</label>
		<input id="form_title" name="name" type="text" class="sharif_input"/>
	</p>
</form>	
