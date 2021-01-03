@php($selected = 'problem_list')
@extends('layouts.app')
@section('head_title','Classes')
@section('icon', 'fas fa-school')

@section('title', 'Classes')

@section('title_menu')
@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
    <span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>
@endif
@endsection

@section('content')
<div class="row">
  <table class="wecode_table table table-striped table-bordered">
    <thead class="thead-dark">
      <tr>
        <th>#</th>
        <th>ID</th>
        <th>Name</th>
        <th>Open</th>
        <th><small>Users count</small></th>
        <th><small>Assignments count</small></th>
        <th>Actions</th>
      </tr>
    </thead>
    @foreach ($lops as $lop)
      <tr data-id="{{$lop->id}}">
        <td>{{$loop->iteration}} </td>
        <td>{{$lop->id}} </td>
        <td>{{$lop->name}}</td>
        <td><i  class=" far {{ $lop->open ? 'fa-check-square color6' : 'fa-square' }} fa-2x"></i></td>
        <td>{{$lop->users()->count() }}</td>
        <td>{{$lop->assignments()->count() }}</td>
        <td>
          <a title="student list" href="{{ route('lops.show', $lop->id) }}" class = "fas fa-list fa-lg color8"></a>
          <a title="scores" href="{{ route('lop.scoreboard', $lop->id) }}" class = "fas fa-clipboard-list fa-lg color8"></a>
          @if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
          <a title="Edit" href="{{ route('lops.edit', $lop->id) }}"><i class="fas fa-edit fa-lg color9"></i></a>
          <span title="Delete lop" class="delete-btn del_n delete_lop pointer" href="{{ route('lops.destroy', $lop->id) }}"><i class="fa fa-times-circle fa-lg color1"></i></span>
          @endif
        </td>
      </tr>
      @endforeach
  </table>
</div>
<div class="modal fade" id="lop_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this class?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger confirm-lop-delete">YES</button>
    <button type="button" class="btn btn-primary" data-dismiss="modal">NO</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('body_end')
<script>
/**
* Notifications
*/
$(document).ready(function () {
$('.del_n').click(function () {
  var row = $(this).parents('tr');
  var id = row.data('id');
  $(".confirm-lop-delete").off();
  $(".confirm-lop-delete").click(function(){
    $("#lop_delete").modal("hide");
    $.ajax({
      type: 'DELETE',
      url: '{{ route('lops.index') }}/'+id,
      data: {
                  '_token': "{{ csrf_token() }}",
      },
      error: shj.loading_error,
      success: function (response) {
        if (response.done) {
          row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
          $.notify('lop deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
          $("#lop_delete").modal("hide");
        }
        else
          shj.loading_failed(response.message);
      }
    });
  });
  $("#lop_delete").modal("show");
});

});
</script>
@endsection