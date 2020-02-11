@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-school')

@section('title', 'Classes')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}

    <span class="title_menu_item"><a href="{{ route('lops.create') }}"><i class="fa fa-plus color11"></i>Add class</a></span>

@endsection

@section('content')


<div class="row">
  <div class="col">
    <div style="height:15px"></div>
    <table class="wecode_table table table-striped table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>ID</th>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      @foreach ($lops as $lop)
        <tr data-id="{{$lop->id}}">
          <td> {{$loop->iteration}} </td>
          <td> {{$lop->id}} </td>
          <td>{{$lop->name}}</td>
          <td>
            <a title="Profile" href="{{ route('lops.show', $lop->id) }}" class = "fas fa-address-book fa-lg color0"></a>
            <a title="Edit" href="{{ route('lops.edit', $lop->id) }}"><i class="fas fa-lop-edit fa-lg color9"></i></a>
          </td>
        </tr>
        @endforeach
    </table>
  </div>
</div>

@endsection
