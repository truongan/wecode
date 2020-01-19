<form action="{{ route('users.update', $user)}}" method="POST">
	@csrf
	@method('PATCH')
	<div class="form-group">
		<label for="username">User name</label>
		<input name="username" type="text" value="{{$user->username}}">
		<label for="display_name">Display name</label>
		<input name="display_name" type="text" value="{{$user->display_name}}">
		<label for="password">Password</label>
		<input name="password" type="text">
	</div>
	<input type="submit" value="Edit">
</form>