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
    <th>name</th>
    <th>extension name</th>
    <th>default_time_limit</th>
    <th>sorting</th>
  </tr>
  @foreach ($Language as $item)
    <tr>
        <th>{{$item->id}}</th>
        <th>{{$item->name}}</th>
        <th>{{$item->extension}}</th>
        <th>{{$item->default_time_limit}}</th>
        <th>{{$item->sorting}}</th>
      </tr>
  @endforeach 
 
</table>
</html>