@extends('layouts.app')
@php ($selected="assignments")
@section('head_title','Assignments')
@section('icon', 'fas fa-folder-open')

@section('title', 'Assignments')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}'/>
@endsection
@if (!in_array( Auth::user()->role->name, ['student', 'guest']))
    @section('title_menu')
    <small><nav class="nav nav-pills">
        <a class="nav-link" href="{{ route('assignments.create') }}"><i class="fa fa-plus color8"></i> Add</a>
        <a class="nav-link" href="{{ route('assignments.index') }}"><i class="far fa-star text-danger"></i>Assingments setting</a>
        <a class="nav-link active" href="{{ route('assignments.score_accepted') }}"><i class="far fa-star text-danger"></i>Assignments score accepted</a>
        <a class="nav-link" href="{{ route('assignments.score_sum') }}"><i class="far fa-star text-danger"></i>Assignments score olp</a>
    </nav></small>
    @endsection
@endif
@section('content')
biết chi mô
@endsection
@section('body_end')

<script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script>
$(document).ready(function () {
    $("table").DataTable({
		"pageLength": 10,
		"lengthMenu": [ [10, 20, 30, 50, -1], [10, 20, 30, 50, "All"] ]
	});
});
</script>
@endsection