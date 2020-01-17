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
  </tr>
  @foreach ($users as $user)
  <tr>
    <td>{{$user->id}}</td>
    <td>{{$user->display_name}}</td>
  </tr>
  @endforeach
</table>
</html>