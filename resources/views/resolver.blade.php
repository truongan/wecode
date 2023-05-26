@php($selected = 'resolver')
@extends('layouts.app')
@section('icon', 'fas fa-snowflake')
@section('head_title', 'Resolver')
@section('title', 'Resolver')

@section('other_assets')
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css'/>
<script>
	if(!!window.performance && window.performance.navigation.type === 2)
	{
		window.location.reload();
	}
</script>
@endsection

@section('title_menu')

@php($sl = 0)
@if (isset(Auth::user()->selected_assignment_id))
	@php($sl = 1)
@endif
@endsection


@section('content')
<div class="mx-n2">
<h1>resolver</h1>
</div>
@endsection