<!DOCTYPE html>
<html>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 5px;
}
th {
  text-align: left;
}
</style>
<table style="width:50%">
  <tr>
    <th>Id</th>
    <th>Display name</th>
    <th>Tools</th>
  </tr>
  @foreach ($users as $user)
  <tr>
    <td>{{$user->id}}</td>
    <td>{{$user->display_name}}</td>
    <td>
        <a href="{{ route('users.show', $user) }}" class = "btn btn-success">Profile</a>
        <a href="{{ route('users.edit', $user) }}" class = "btn btn-success">Edit</a>
        <a href="{{ route('users.destroy', $user) }}" class = "btn btn-success">Delete</a>
    </td>
  </tr>
  @endforeach
</table>
<a href="{{ route('users.create', $user) }}" class = "btn btn-success">Create</a>
</html>