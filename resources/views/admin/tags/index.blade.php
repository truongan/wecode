@extends('layouts.app')
@php($selected="instructor_panel")
@section('head_title','Tags')
@section('icon', 'fas fa-tags')

@section('title','Tags')

@section('other_assets')
  <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css'/>
@endsection

@section('title_menu')
{{-- {% if user.level >= 2 %} --}}
<div class="ms-4">
    <form method="POST"  action="{!! route('tags.store') !!}">
        <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
        <input placeholder="Tag's name here" type="text" name="text" required>
        <button type="submit" class="bg-transparent border-0 text-primary"><i class="fas fa-plus fa-lg color8"></i> Add</button>
    </form>    
</div>
	
@endsection

@section('content')
<div class="row">
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-old table-dark">
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Text</th>
            <th>No. of Problems</th>
            <th>Tools</th>
        </tr>
        </thead>
        @foreach ($tags as $item)
        <tr data-id="{{$item->id}}">
            <td> {{$loop->iteration}} </td>
            <td>{{ $item->id}}</td>
            <td><a href="{{ route('tags.show', $item->id) }}">{{ $item->text }}</a></td>
            <td>
                {{$item->problems->count()}}
            </td>
            <td> 
                <a title="Edit" href="{{ route('tags.edit', $item) }}"><i class="fas fa-edit fa-lg color9"></i></a>
                <span title="Delete Tag" class="del_n delete_tag pointer"><i title="Delete Tag" class="far fa-trash-alt fa-lg color1"></i></span>
            </td>
        
        </tr>
        @endforeach
    </table>
  </div>  
</div>


<div class="modal fade" id="tag_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this tag?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger confirm-tag-delete">YES</button>
      <button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO</button>
        </div>
      </div>
    </div>
</div>
@endsection

@section('body_end')

<script type='text/javascript' src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
/**
* Notifications
*/
$(document).ready(function () {
$('.del_n').click(function () {
  var row = $(this).parents('tr');
	var id = row.data('id');
  $(".confirm-tag-delete").off();
  $(".confirm-tag-delete").click(function(){
    $("#tag_delete").modal("hide");
    $.ajax({
      type: 'DELETE',
      url: '{{ route('tags.index') }}/'+id,
      data: {
                  '_token': "{{ csrf_token() }}",
      },
      error: shj.loading_error,
      success: function (response) {
        if (response.done) {
          row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
          $.notify('tag deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
          $("#tag_delete").modal("hide");
        }
        else
          shj.loading_failed(response.message);
      }
    });
  });
  $("#tag_delete").modal("show");
});

    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection