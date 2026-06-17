@extends('layouts.app')
@section('head_title','Users')
@section('icon', 'bi bi-people-fill')

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
  {{-- <span class="ms-4 fs-6"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="bi bi-question-circle-fill color6"></i> Help</a></span> --}}

  <a class="ms-4 fs-6 link-dark-subtle" href="{{ url('users/add_multiple') }}"><i class="bi bi-person-plus-fill text-success"></i> Add Users</a>
  <a class="ms-4 fs-6 link-dark-subtle" href="{{ 'mailto:' .  App\Models\User::pluck('email')->join(',') }}"><i class="bi bi-envelope-fill    "></i> Email all users</a>
  <a class="ms-4 fs-6 link-dark-subtle" href="{{route('users.set_trial') }}"><i class="bi bi-highlighter    "></i>Update multiple users trial time</a>
	{{-- <span class="ms-4 fs-6"><a href="{{ url('users/list_excel') }}"><i class="bi bi-download color9"></i> Excel</a></span> --}}
@endsection

@section('content')
<a name="" id="copy_user_list" class="btn btn-primary my-2" href="#" role="button"><i class="bi bi-copy    "></i> copy user name list</a>
<div class="row">
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="thead-old table-dark">
        <tr>
          <th>id</th>
          <th>Username</th>
          <th>Display Name</th>
          <th>Email</th>
          <th>Trial end</th>
          <th>First Login</th>
          <th>Last Login</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

{{-- <span><a href="{{ route('users.create') }}"><i class="bi bi-person-plus-fill text-success"></i> Add 1 User</a></span> --}}

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

function escapeHtml(str){
	return $('<div>').text(str ?? '').html();
}

function renderActions(row){
	var showUrl = "{{ route('users.show', ['user' => '__ID__']) }}".replace('__ID__', row.id);
	var editUrl = "{{ route('users.edit', ['user' => '__ID__']) }}".replace('__ID__', row.id);
	var subsUrl = "{{ url('submissions/all/user/__USERNAME__') }}".replace('__USERNAME__', encodeURIComponent(row.username));

	return '<a title="Profile" href="'+showUrl+'" class="bi bi-person-vcard fs-5 color0"></a> '
		+ '<a title="Edit" href="'+editUrl+'"><i class="bi bi-person-fill-gear fs-5 color9"></i></a> '
		+ '<a title="Submissions" href="'+subsUrl+'"><i class="bi bi-list fs-5 color12"></i></a> '
		+ '<span title="Delete User" class="delete-btn delete_user pointer"><i title="Delete User" class="bi bi-person-x-fill fs-5 color2"></i></span> '
		+ '<span title="Delete Submissions" class="delete-btn delete_submissions pointer"><i class="bi bi-trash3 fs-5 text-danger"></i></span>';
}

/**
 * "Users" page
 */
document.addEventListener("DOMContentLoaded", function(){
	var table = $("table").DataTable({
		"serverSide": true,
		"ajax": "{{ route('users.data') }}",
		"pageLength": 50,
		"lengthMenu": [ [20, 50, 100, 200, -1], [20, 50, 100, 200, "All"] ],
		"order": [[0, 'asc']],
		"columns": [
			{
				"data": "id", "name": "id", "orderable": true, "searchable": false,
				"render":  $.fn.dataTable.render.number(),
			},
			{
				"data": "username", "name": "username",
				"render": $.fn.dataTable.render.text(),
				"createdCell": function(cell){ cell.id = 'un'; }
			},
			{ "data": "display_name", "name": "display_name", "render": $.fn.dataTable.render.text() },
			{
				"data": null, "name": "email",
				"render": function(data, type, row){ return escapeHtml(row.email) + '<br>' + escapeHtml(row.role_name); }
			},
			{ "data": "trial_end", "orderable": false, "searchable": false, "render": $.fn.dataTable.render.text() },
			{
				"data": "first_login", "name": "first_login_time",
				"render": function(data){ return '<small>' + (data ? escapeHtml(data) : 'Never') + '</small>'; }
			},
			{
				"data": "last_login", "name": "last_login_time",
				"render": function(data){ return '<small>' + (data ? escapeHtml(data) : 'Never') + '</small>'; }
			},
			{
				"data": null, "orderable": false, "searchable": false,
				"render": function(data, type, row){ return renderActions(row); }
			},
		],
	});

	$("table").on('click', '.delete-btn', function(){
		var row_data = table.row($(this).parents('tr')).data();
		var user_id = row_data.id;
		var username = row_data.username;
    var token = $("meta[name='csrf-token']").attr("content");
		var del_submssion = $(this).hasClass('delete_submissions');
		if (del_submssion) $(".modal-title").html("Are you sure you want to delete this user's SUBMISSIONS?");
		else $(".modal-title").html("Are you sure you want to delete this user?");

		$(".modal-body").html('User ID: '+user_id+'<br>Username: '+escapeHtml(username)+'<br><i class="splashy-warning_triangle"></i> All submissions of this user will be deleted.');
		$(".confirm-user-delete").off();
		$(".confirm-user-delete").click(function(){
      console.log(del_submssion);
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
                notify('User '+username+' deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
                table.ajax.reload(null, false);
              } else {
                notify('All ' + parseInt( response.count) +' submission(s) ' + 'of User '+username +' has been deleted.', {position: 'bottom right', className: 'success', autoHideDelay: 5000});
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
});

</script>
@endsection
