@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-clipboard-list')

@section('title','Problems List')

@section('title_menu')
{{-- {% if user.level >= 2 %} --}}
<span class="title_menu_item"><a href="{{ url('problems/create') }}"><i class="fas fa-plus fa-lg color8"></i> Add</a></span>
<span class="title_menu_item"><a href="{{ url('submissions/all/assignments/0') }}"><i class="fas fa-list-ul fa-lg color8"></i>Review test submissions for problems</a></span>
<span class="title_menu_item"><a href="{{ url('problems/download_all') }}"><i class="fas fa-download fa-lg color8"></i>Download all problem's test and description</a></span>
@endsection

@section('content')
<div class="row">
  <div class="col">
    <div class="table-responsive">
    {{-- {% set msgclasses = {'success': 'text-success', 'notice': 'text-info', 'error': 'text-danger'} %}
    {% for message in messages %}
        <p class="{{ msgclasses[message.type] }} {{message.type}}">{{ message.text }}</p>
    {% endfor %}
     --}}
    {{-- {% if all_problems|length == 0 %}
        <p style="text-align: center;">Nothing to show...</p>
    {% else %} --}}
    <table class="table table-striped table-bordered">
      <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th style="width: 20%">Name</th>
            <th style="width: 20%">Note</th>
            <th>Languages</th>
            <th>Used in assignmnets</th>
            <th>diff<br/>command</th>
            <th>diff<br/>argument</th>
            <th>Tools</th>
        </tr>
      </thead>
      @foreach ($problems as $item)
        <tr data-id="{{$item->id}}">
            <td>{{ $item->id}}</td>
            <td><a href="{{ url("problems/show/$item->id") }}">{{ $item->name }}</a></td>
            <td>{{$item->admin_note}}</td>
            <td>
              @foreach ($item->languages as $language_name)
                  {{$language_name->name}}
              @endforeach
            </td>
            <td>
                {{-- {% for ass_id in item.assignments %}
                    <a href="{{ site_url("assignments/edit/#{ass_id}") }}" class="badge badge-primary">asgmt {{ ass_id}}</a>
                {% endfor %} --}}
            </td>
            <td>{{ $item->diff_cmd }}</td>
            <td>{{ $item->diff_arg }}</td>
            
            <td>
                <a href="{{ route('problems.pdf',$item->id) }}">
                    <i title="Download Tests and Descriptions" class="fa fa-cloud-download-alt fa-lg color11"></i>
                </a>
                <a href="{{ route('problems.edit', $item) }}"> <i title="Edit" class="far fa-edit fa-lg color3"> </i> </a>
                <span title="Delete problem" class="del_n delete_tag pointer">
                  <i title="Delete problem" class="far fa-trash-alt fa-lg color1"></i>
                </span>
              
            </td>
        
        </tr>
      @endforeach
    </table>
    </div>
  </div>
</div>

  <div class="modal fade" id="problem_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this tag?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-danger confirm-tag-delete">YES</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">NO</button>
          </div>
        </div>
      </div>
  </div>
@endsection


@section('body_end')
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type='text/javascript' src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
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
          $("#problem_delete").modal("hide");
            $.ajax({
              type: 'DELETE',
              url: '/problems/'+id,
              data: {
                          '_token': "{{ csrf_token() }}",
              },
              error: shj.loading_error,
              success: function (response) {
                if (response.done) {
                  row.animate({backgroundColor: '#FF7676'},100, function(){row.remove();});
                  $.notify('problem deleted'	, {position: 'bottom right', className: 'success', autoHideDelay: 5000});
                  $("#problem_delete").modal("hide");
                }
                else
                  shj.loading_failed(response.message);
              }
            });
        });
      $("#problem_delete").modal("show");
    });
  });
</script>
@endsection

