<form method="POST"  action="{!! route('users.store') !!}">
<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
<table>
	<tr>
				<td>Username:</td>
				<td>	<input type="text" name="username">	</td>
	</tr>
	<tr>
				<td>Display name:</td>
				<td>	<input type="text" name="display_name">	</td>
	</tr>
	<tr>
				<td>Password:</td>
				<td>	<input type="text" name="password">	</td>
	</tr>
	<tr>
				<td>Email:</td>
				<td>	<input type="text" name="email">	</td>
	</tr>
	<tr>
				<td>Role id:</td>
				<td>	<input type="text" name="role_id">	</td>
	</tr>

	
</table>
<button class="btn btn-primary"   type="submit" > OK </button>
</form>
