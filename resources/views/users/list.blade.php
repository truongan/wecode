@extends('layouts.app')
@section('head_title','Users')
@section('icon', 'fas fa-users')

@section('title', 'Users')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}'/>
  {{-- <style>

tbody{
	/* turn tbody to block so it can have separate scroll */
	display: block;
	height: calc(100vh - 187px);
	overflow: scroll;
}
tr{
	/* turn every row to a flex row to distribute column evenly */
	display: flex;
}
td, th{
	/* distribute column evenly */
	flex: 1 auto;
	width: 1px;
	word-wrap: break-word;
}
thead tr:after {
	/* add one character after thead to align it with tbody */
	content: '';
	overflow-y: scroll;
	visibility: hidden;
	height: 0;
  }
  
</style> --}}
@endsection

@section('title_menu')
  {{-- <span class="ms-4 fs-6"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a></span> --}}
  
  <a class="ms-4 fs-6 link-dark" href="{{ url('users/add_multiple') }}"><i class="fa fa-user-plus text-success"></i> Add Users</a>
  <a class="ms-4 fs-6 link-dark" href="{{ 'mailto:' .  App\User::pluck('email')->join(',') }}"><i class="fas fa-mail-bulk    "></i> Email all users</a>
  <a class="ms-4 fs-6 link-dark" href="{{route('users.set_trial') }}"><i class="fas fa-highlighter    "></i>Update multiple users trial time</a>

	{{-- <span class="ms-4 fs-6"><a href="{{ url('users/list_excel') }}"><i class="fa fa-download color9"></i> Excel</a></span> --}}
@endsection

@section('content')
<a name="" id="copy_user_list" class="btn btn-primary my-2" href="#" role="button"><i class="fas fa-copy    "></i> copy user name list</a>
@if (Auth::user()->role->name == 'admin')
<button id="select_all" class="btn btn-secondary my-2"><i class="fas fa-check"></i> Select all users</button>
<button id="delete_selected" class="btn btn-danger my-2"><i class="fas fa-trash"></i> Delete selected users</button>
@endif
<div class="row">
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="thead-old table-dark">
        <tr>
          <th></th>
          <th>#</th>
          {{-- <th>User ID</th> --}}
          <th>Username</th>
          <th>Display Name</th>
          <th>Email</th>
          <th>Trial end</th>
          <th>First Login</th>
          <th>Last Login</th>
          <th>Actions</th>
        </tr>
      </thead>
      @foreach ($users as $user)
      <tr data-id="{{$user->id}}">
        <td>
          <i class="checkbox pointer far fa-circle fa-2x"></i>
        </td>
        <td> {{$loop->iteration}} </td>
        {{-- <td> {{$user->id}} </td> --}}
        <td id="un"> {{$user->username}} </td>
        <td>{{$user->display_name}}</td>
        <td>{{$user->email}}<br/>{{$user->role->name}}</td>
        <td>{{ $user->trial_time ? ($user->created_at->addHours($user->trial_time)->diffForHumans()) : "Permanent user" }}</td>
        <td>
          <small>{{ $user->first_login_time ? $user->first_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll') : 'Never'}}</small>
        </td>
        <td>
          <small>{{ $user->last_login_time ? $user->last_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll') : 'Never'}} </small>
        </td>
        <td>
          <a title="Profile" href="{{ route('users.show', $user) }}" class = "fas fa-address-book fa-lg color0"></a>
          <a title="Edit" href="{{ route('users.edit', $user) }}"><i class="fas fa-user-edit fa-lg color9"></i></a>
          <a title="Submissions" href="{{ url('submissions/all/user/'.$user->username) }}"><i class="fa fa-bars fa-lg color12"></i></a>
          <span title="Delete User" class="delete-btn delete_user pointer"><i title="Delete User" class="fa fa-user-times fa-lg color2"></i></span>
          <span title="Delete Submissions" class="delete-btn delete_submissions pointer"><i class="far fa-trash-alt fa-lg text-danger"></i></span>
        </td>
      </tr>
      @endforeach
    </table>
  </div>
</div>

{{-- <span><a href="{{ route('users.create') }}"><i class="fa fa-user-plus text-success"></i> Add 1 User</a></span> --}}

@endsection

@section('body_end')
<div class="modal fade" id="user_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this user?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger confirm-user-delete">YES</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO</button>
      </div>
    </div>
  </div>
</div>
</div>

<script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script>
document.getElementById('copy_user_list').addEventListener('click', function(){
  cells = document.querySelectorAll('tr > #un');
  window.getSelection().removeAllRanges();
  for(cell of cells){
    x = new Range();
    x.setStart(cell, 0);
    x.setEnd(cell, 1);
    window.getSelection().addRange(x);
  }
  document.execCommand("copy");
});
/**
 * "Users" page
 */
$(document).ready(function(){
	$('.delete-btn').click(function(){
		var row = $(this).parents('tr');
		var user_id = row.data('id');
		var username = row.children('#un').html();
    var token = $("meta[name='csrf-token']").attr("content");
		var del_submssion = $(this).hasClass('delete_submissions');
		if (del_submssion) $(".modal-title").html("Are you sure you want to delete this user's SUBMISSIONS?");
		else $(".modal-title").html("Are you sure you want to delete this user?");

		$(".modal-body").html('User ID: '+user_id+'<br>Username: '+username+'<br><i class="splashy-warning_triangle"></i> All submissions of this user will be deleted.');
		$(".confirm-user-delete").off();
		$(".confirm-user-delete").click(function(){
			$.ajax({
				url: (del_submssion ? ('users/delete_submissions/'+user_id) : ('{{ route('users.index') }}/'+user_id)),
        type: (del_submssion ? 'POST' : 'DELETE'),
				data: {
					user_id: user_id,
					// wcj_csrf_name: shj.csrf_token,
          "_token": "{{ csrf_token() }}",
				},
				error: shj.loading_error,
				success: function (response){
            if (response.done)
            {
              if (!del_submssion){
                row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
                $.notify('User '+username+' deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
              } else {
                $.notify('All ' + parseInt( response.count) +' submission(s) ' + 'of User '+username +' has been deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
              }
            }
            else{
              shj.loading_failed(response.message);
            }
            $("#user_delete").modal("hide");
        }
			});
		});
		$("#user_delete").modal("show");
	});

  let selected_users = [];

  $('.checkbox').click(function() {
    let username = $(this).parents('tr').find('#un').text().trim();
    let id = $(this).parents('tr').data('id');

    if ($(this).hasClass('fa-check-circle')) {
        $(this).removeClass('fa-check-circle').addClass('fa-circle');
        this.classList.add('fa-circle');
        selected_users = selected_users.filter(user => user.id !== id && user.username !== username);
    } else {
        $(this).removeClass('fa-circle').addClass('fa-check-circle');
        selected_users.push({
          id: id,
          username: username,
        });
    }
  });

  $("#delete_selected").click(function(e) {
    e.preventDefault();

    if (selected_users.length === 0) return;

    // Show popup
    let row = $(this).parents('tr');
    let user_ids = row.data('id');
    let usernames = row.children('#un').html();

    $(".modal-title").html("Are you sure you want to DELETE these users and their submissions?");
    $(".modal-body").html('')
    selected_users.forEach(user => {
      $(".modal-body").append('User ID: <code>'+user.id+'</code><br>Username: <code>'+user.username+'</code><br><br>');
    });
    $("#user_delete").modal("show");

    $(".confirm-user-delete").click(function() {
      $("#user_delete").modal("hide");
      selected_users.forEach(async user => {

        // Delete submissions
        await $.ajax({
          url: 'users/delete_submissions/'+user.id,
          type: 'POST',
          data: {
            user_id: user.id,
            "_token": "{{ csrf_token() }}",
          },
          error: shj.loading_error,
          success: function (response){
              if (response.done) {
                $.notify('All ' + parseInt(response.count) +' submission(s) ' + 'of User '+user.username +' has been deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
              }
              else {
                shj.loading_failed(response.message);
              }
          }
        });
        
        // Delete user
        await $.ajax({
          url: '{{ route('users.index') }}/'+user.id,
          type: 'DELETE',
          data: {
            user_id: user.id,
            "_token": "{{ csrf_token() }}",
          },
          error: shj.loading_error,
          success: function (response){
              if (response.done) {
                row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
                $.notify('User '+user.username+' deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
              }
              else {
                shj.loading_failed(response.message);
              }
          }
        });

      });
    });
  });

  $("table").DataTable({
    "pageLength": 50,
    "lengthMenu": [ [20, 50, 100, 200, -1], [20, 50, 100, 200, "All"] ]
  });

  $("#select_all").click(function(e) {
    e.preventDefault();
    const table = $('table').DataTable();
    // Show all rows
    table.page.len(-1).draw();
    
    $('.checkbox').each(function() {
      let username = $(this).parents('tr').find('#un').text().trim();
      let id = $(this).parents('tr').data('id');

      if (!$(this).hasClass('fa-check-circle')) {
        $(this).removeClass('fa-circle').addClass('fa-check-circle');
        selected_users.push({
          id: id,
          username: username,
        });
      }
    });
  });

});

</script>
@endsection