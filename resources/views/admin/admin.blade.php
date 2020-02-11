@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-sliders-h')

@section('title', 'Admin panel')

@section('title_menu')
	{{-- Nếu là admin thì hiển thị --}}
	<span class="title_menu_item"><a href="{{ url('assignments/add') }}"><i class="fa fa-plus color8"></i> Add</a></span>
@endsection

@section('content')
<div class="card-group">
  <div class="m-3">
    <div class="card" style="width: 18rem;">
      <i class="text-center card-img-top fas fa-cogs fa-8x p-4"></i>
      <div class="card-body bg-light">
        <h5 class="card-title">SETTING</h5>
        <p  class="card-text">Chỉnh sửa và ...</p>
        <a href="{{ route('settings.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
    
  <div class="m-3">
    <div class="card" style="width: 18rem;">
      <i class="text-center card-img-top fas fa-users fa-8x p-4"></i>
      <div class="card-body bg-light">
        <h5 class="card-title">USERS</h5>
        <p  class="card-text">Quản lý người dùng</p>
        <a href="{{ route('users.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" style="width: 18rem;">
      <i class="text-center card-img-top fas fa-school fa-8x p-4"></i>
      <div class="card-body bg-light">
        <h5 class="card-title">CLASSES</h5>
        <p  class="card-text">Quản lý lớp học</p>
        <a href="{{ route('lops.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" style="width: 18rem;">
      <i class="text-center card-img-top fas fa-language fa-8x p-4"></i>
      <div class="card-body bg-light">
        <h5 class="card-title">Language</h5>
        <p  class="card-text">Thiết lập ngôn ngữ lập trình</p>
        <a href="#" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" style="width: 18rem;">
      <i class="text-center card-img-top fas fa-list fa-8x p-4"></i>
      <div class="card-body bg-light">
        <h5 class="card-title">PROBLEM LIST</h5>
        <p  class="card-text">Chỉnh sửa và ...</p>
        <a href="{{ route('users.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
</div>


@endsection