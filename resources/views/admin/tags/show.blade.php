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
<div class="col">
    <table class="wecode_table table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
        <th>ID</th>
        <th>Text</th>
        <th>No. of Problems</th>
        <th>Tools</th>
    </tr>
    </thead>
    @foreach ($tags as $item)
    <tr>
        <td>{{ $item->id}}</td>
        <td><a href="{{ route('tags.show', $item->id) }}">{{ $item->text }}</a></td>
        <td>
            {{$item->problems->count()}}
        </td>
        <td> 
            <form method="POST"  action="{!! route('problems.pdf',$item->id) !!}">
                <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
                <button title="Download Tests and Descriptions" class="fa fa-cloud-download-alt fa-lg color11">
            </form>
        </td>
    
    </tr>
    @endforeach
    </table>
</div>
@endsection