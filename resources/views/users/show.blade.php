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
    <th>Username</th>
    <th>Display name</th>
    <th>Email</th>
    <th>Role</th>
  </tr>
  <tr>
    <td>{{$user->id}}</td>
    <td>{{$user->username}}</td>
    <td>{{$user->display_name}}</td>
    <td>{{$user->email}}</td>
    <td>{{$user->role_id}}</td>
  </tr>
</table>
</html>