<form method="POST"  action="{!! route('tags.store') !!}">
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
	<p class="input_p">
		<label for="form_title" class="tiny">text:</label>
		<input id="form_title" name="text" type="text" class="sharif_input"/>
	</p>
</form>	
