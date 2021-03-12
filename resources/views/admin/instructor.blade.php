@php($selected = 'instructor_panel')
@extends('layouts.app')
@section('head_title','Instructor panel')
@section('icon', 'fas fa-sliders-h')

@section('title', 'Instructor panel')

@section('other_assets')
<style>
.card{
  width:9rem;
  height: 100%;
}
</style>
@endsection

@section('title_menu')
	{{-- Nếu là admin thì hiển thị --}}
@endsection

@section('content')
<div class="card-group ">
<div class="row">
  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-clipboard-list fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">PROBLEMS</strong></small>
        <small><p  class="card-text">Danh sách bài tập</p></small>
        <a href="{{ route('problems.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-tags fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">TAGS</strong></small>
        <small><p  class="card-text">Nhãn dán cho các problems</p></small>
        <a href="{{ route('tags.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-edit fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">EDIT BY HTML</strong></small>
        <small><p  class="card-text">Trình soạn thảo đề bài (problem description) trên web</p></small>
        <a href="{{ route('htmleditor') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>


  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-user-secret fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">Detect Similar Codes</strong></small>
        <small><p  class="card-text">Kiểm tra code trùng nhau</p></small>
        <a href="{{ route('moss.index' , Auth::user()->selected_assignment_id) }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
</div>
  
</div>


@endsection