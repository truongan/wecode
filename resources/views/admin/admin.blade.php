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
	<span class="title_menu_item"><a href="{{ url('assignments/add') }}"><i class="fa fa-plus color8"></i> Add</a></span>
@endsection

@section('content')
<div class="card-group">
<div class="row">
<div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-cogs fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">SETTING</strong></small>
        <small><p  class="card-text">Chỉnh sửa và ...</p></small>
        <a href="{{ route('settings.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
    
  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-users fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">USERS</strong></small>
        <small><p  class="card-text">Quản lý người dùng</p></small>
        <a href="{{ route('users.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  {{-- <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-school fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">CLASSES</strong></small>
        <small><p  class="card-text">Quản lý lớp học</p></small>
        <a href="{{ route('lops.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div> --}}

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-laptop-code fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">LANGUAGES</strong></small>
        <small><p  class="card-text">Thiết lập ngôn ngữ lập trình</p></small>
        <a href="{{ route('languages.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-clipboard-list fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">PROBLEMS</strong></small>
        <small><p  class="card-text">Danh sách bài tập</p></small>
        <a href="{{ route('problems.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-tags fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">TAGS</strong></small>
        <small><p  class="card-text">Đánh đầu các dạng thuật toán</p></small>
        <a href="{{ route('tags.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-edit fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">EDIT BY HTML</strong></small>
        <small><p  class="card-text">Soạn văn bản trên web</p></small>
        <a href="{{ url('htmleditor') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-redo fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">Rejudge</strong></small>
        <small><p  class="card-text">Chấm lại bài trong assignment đang chọn</p></small>
        <a href="{{ url('rejudge') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-play fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">Submission Queue</strong></small>
        <small><p  class="card-text">Những thao tác xử lý trên hàng đợi các bài đang chấm</p></small>
        <a href="{{ route('queue.index') }}" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="m-3">
    <div class="card" >
      <i class="text-center card-img-top fas fa-user-secret fa-2x p-3"></i>
      <div class="card-body bg-light">
        <small><strong class="card-title">Detect Similar Codes</strong></small>
        <small><p  class="card-text">Kiểm tra code trùng nhau</p></small>
        <a href="{{ route('moss.index' , Auth::user()->selected_assignment_id) }}" class="stretched-link"></a>
      </div>
    </div>
  </div>
</div>
  
</div>


@endsection