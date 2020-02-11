<form method="POST"  action="{!! route('languages.store') !!}">
	<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
	<p class="input_p">
		<label for="form_title" class="tiny">name:</label>
		<input id="form_title" name="name" type="text" class="sharif_input"/>
	</p>
	<p class="input_p">
		<label for="notif_text" class="tiny">extension:</label><br><br>
		<textarea id="notif_text" name="extension"></textarea>
    </p>
    <p class="input_p">
		<label for="notif_text" class="tiny">sorting:</label><br><br>
		<textarea id="notif_text" name="sorting"></textarea>
    </p>
    <p class="input_p">
		<label for="notif_text" class="tiny">default_time_limit:</label><br><br>
		<textarea id="notif_text" name="default_time_limit"></textarea>
    </p>
    <p class="input_p">
		<label for="notif_text" class="tiny">default_memory_limit:</label><br><br>
		<textarea id="notif_text" name="default_memory_limit"></textarea>
	</p>
	<p class="input_p">
		<input type="submit" value="Add" class="sharif_input"/>
	</p>
</form>	
