<div class="create">
<form method="POST"  action="{!! route('users.store') !!}" name="formthem">
<input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
<table>
	<tr>
				<td>Mã số sinh viên:</td>
				<td>	<input type="text" name="username">	</td>
	</tr>
	<tr>
				<td>Mật khẩu:</td>
				<td>	<input type="text" name="password">	</td>
	</tr>

	
</table>
<button class="btn btn-primary"   type="submit" > OK </button>
</form>
</div>