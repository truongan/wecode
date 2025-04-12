@extends('layouts.app')
@php($selected="settings")
@section('head_title','Languages')
@section('icon', 'fas fa-laptop-code')

@section('title','Languages')

@section('title_menu')
{{-- <a class="ms-4 fs-6 link-dark" href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a> --}}
<a class="ms-4 fs-6 link-dark" href="{{ url('languages/create') }}"><i class="fa fa-plus color10"></i> Add</a>
@endsection

@section('content')
  <div class="row">
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="thead-old table-dark">
        <tr>
          <th>Id</th>
          <th>Name</th>
          <th>Extension name</th>
          <th>Default_time_limit</th>
          <th>Default_memory_limit</th>
          <th>Sorting</th>
          <th>Action</th>
        </tr>
      </thead>
      @foreach ($Language as $item)
      <tr data-id="{{$item->id}}">
        <td>{{$item->id}}</td>
        <td>{{$item->name}}</td>
        <td>{{$item->extension}}</td>
        <td>{{$item->default_time_limit}}</td>
        <td>{{$item->default_memory_limit}}</td>
        <td>{{$item->sorting}}</td>
        <td>
          <a title="Edit" href="{{ route('languages.edit', $item->id) }}"><i class="fas fa-edit fa-lg color9"></i></a>
          <span title="Delete Language" class="delete-btn del_n delete_language pointer" href="{{ route('languages.destroy', $item->id) }}"><i class="far fa-trash-alt fa-lg text-danger"></i></span>
        </td>
      </tr>
      @endforeach
    </table>
  </div>

  </div>
  <div class="modal fade" id="language_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this language?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger confirm-language-delete">YES</button>
      <button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('body_end')
{{-- <script type="text/javascript" async Language listing ? What for ? 
src="{{ asset('assets/MathJax-2.7.9') }}/MathJax.js?config=TeX-MML-AM_CHTML">
</script> --}}
<script>
/**
* Notifications
*/
document.addEventListener("DOMContentLoaded", function () {
$('.del_n').click(function () {
  var row = $(this).parents('tr');
	var id = row.data('id');
  $(".confirm-language-delete").off();
  $(".confirm-language-delete").click(function(){
    $("#language_delete").modal("hide");
    $.ajax({
      type: 'DELETE',
      url: '{{ route('languages.index') }}/'+id,
      data: {
                  '_token': "{{ csrf_token() }}",
      },
      error: shj.loading_error,
      success: function (response) {
        if (response.done) {
          row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
          $.notify('Language deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
          $("#language_delete").modal("hide");
        }
        else
          shj.loading_failed(response.message);
      }
    });
  });
  $("#language_delete").modal("show");
});

});
</script>
@endsection