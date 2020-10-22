@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Admin panel')
@section('icon', 'fas fa-sliders-h')

@section('title', 'Admin panel')

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
      <i class="text-center card-img-top fas fa-cogs fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">SETTING</strong></small>
        <small><p  class="card-text">Chỉnh sửa và ...</p></small>
        <a href="{{ route('settings.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
    
  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-users fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">USERS</strong></small>
        <small><p  class="card-text">Quản lý người dùng</p></small>
        <a href="{{ route('users.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-laptop-code fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">LANGUAGES</strong></small>
        <small><p  class="card-text">Thiết lập ngôn ngữ lập trình</p></small>
        <a href="{{ route('languages.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card bg-dark text-light" >
      <i class="text-center card-img-top fas fa-play fa-2x p-3"></i>
      <div class="card-body bg-light text-dark">
        <small><strong class="card-title">Submission Queue</strong></small>
        <small><p  class="card-text">Những thao tác xử lý trên hàng đợi các bài đang chấm</p></small>
        <a href="{{ route('queue.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>


</div>
  
</div>


@endsection