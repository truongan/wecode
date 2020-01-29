@extends('layouts.app')

@section('icon', 'fas fa-users')

@section('title', 'Users')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}
  <span class="title_menu_item"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a></span>
  <span class="title_menu_item"><a href="{{ route('users.add') }}"><i class="fa fa-user-plus color11"></i> Add Users</a></span>
	<span class="title_menu_item"><a href="{{ url('users/list_excel') }}"><i class="fa fa-download color9"></i> Excel</a></span>
@endsection

@section('content')
<div class="row">
  <div class="col">
    {{-- {% if deleted_user %}
      <p class="shj_ok">User deleted successfully.</p>
    {% endif %}
    {% if deleted_submissions %}
      <p class="shj_ok">Submissions of selected user deleted successfully.</p>
    {% endif %} --}}
    <div style="height:15px"></div>
    <table class="wecode_table table table-striped table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>User ID</th>
          <th>Username</th>
          <th>Display Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>First Login</th>
          <th>Last Login</th>
          <th>Actions</th>
        </tr>
      </thead>
      @foreach ($users as $user)
        <tr data-id="{{$user->id}}">
          <td> loop.index </td>
          <td> {{$user->id}} </td>
          <td id="un"> {{$user->username}} </td>
          <td>{{$user->display_name}}</td>
          <td>{{$user->email}}</td>
          <td>user.role</td>
          <td>user.first_login_time ? user.first_login_time : 'Never'</td>
          <td>user.last_login_time ? user.last_login_time : 'Never'</td>
          <td>
            <a title="Profile" href="{{ route('users.show', $user) }}" class = "fas fa-address-book fa-lg color0"></a>
            <a title="Edit" href="{{ route('users.edit', $user) }}"><i class="fas fa-user-edit fa-lg color9"></i></a>
            <a title="Delete User" href="{{ route('users.destroy', $user) }}"><i title="Delete User" class="fa fa-user-times fa-lg color2"></i></a>
            {{-- <a title="Submissions" href="{{ site_url('submissions/all/user/'~user.username) }}"><i class="fa fa-bars fa-lg color12"></i></a> --}}
            {{-- <span title="Delete User" class="delete-btn delete_user pointer"><i title="Delete User" class="fa fa-user-times fa-lg color2"></i></span> --}}
            <span title="Delete Submissions" class="delete-btn delete_submissions pointer"><i class="fa fa-times-circle fa-lg color1"></i></span>
          </td>
        </tr>
        @endforeach
    </table>
  </div>
</div>
  <span><a href="{{ route('users.create', $user) }}"><i class="fa fa-user-plus color11"></i> Add 1 User</a></span>

@endsection