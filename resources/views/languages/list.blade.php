@extends('layouts.app')
@php($selected="settings")
@section('icon', 'fas fa-laptop-code')

@section('title','Languages')

@section('title_menu')
<span class="title_menu_item"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a></span>
@endsection

@section('content')
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead class="thead-dark">
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
      <tr>
        <td>{{$item->id}}</td>
        <td>{{$item->name}}</td>
        <td>{{$item->extension}}</td>
        <td>{{$item->default_time_limit}}</td>
        <td>{{$item->default_memory_limit}}</td>
        <td>{{$item->sorting}}</td>
        <td>
          <a title="Edit" href="{{ route('languages.edit', $item->id) }}"><i class="fas fa-edit fa-lg color9"></i></a>
          <span title="Delete Language" class="delete-btn delete_language pointer"><i class="fa fa-times-circle fa-lg color1"></i></span>
        </td>
      </tr>
      @endforeach
    </table>
  </div>
@endsection