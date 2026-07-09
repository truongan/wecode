@extends('layouts.app')
@php($selected="settings")
@section('head_title','Notification')
@section('icon', 'bi bi-bell-fill')

@section('title','Notification')

@section('title_menu')
@endsection

@section('other_assets')
<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}" />
@endsection

@section('body_end')
<script src="{{ asset('assets/tiptap/katex.min.js') }}"></script>
<script src="{{ asset('assets/tiptap/auto-render.min.js') }}"></script>
<script type="text/javascript">
	// Give formulas saved by the Tiptap editor as
	// <span data-type="inline-math" data-latex="..."></span> the
	// \(...\) delimiters, then typeset them together with legacy
	// $...$ and \(...\) math.
	const notif_text = document.querySelector(".notif_text");
	notif_text.querySelectorAll('[data-type="inline-math"]').forEach(function (span) {
		span.textContent = "\\(" + span.getAttribute("data-latex") + "\\)";
	});
	renderMathInElement(notif_text, {
		delimiters: [
			{ left: "$$", right: "$$", display: true },
			{ left: "$", right: "$", display: false },
			{ left: "\\(", right: "\\)", display: false },
			{ left: "\\[", right: "\\]", display: true },
		],
		throwOnError: false,
	});
</script>
@endsection

@section('content')
<div id="number{{ $notification->id }}" data-id="{{ $notification->id }}">
	<div class="notif_title">
	<h2>{{ $notification->title }} - {{$author}}</h2>
		<div class="notif_meta">
			{{ $notification->created_at }}
			@if ( in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
				<a href="{{ $notification->id }}/edit">Edit</a>
				<p>Chỉnh sửa lần cuối bởi: {{$notification->last_user->username}}</p>
			@endif
        </div>
        <hr>
    </div>
    <div class="notif_text">
        {!! $notification->text !!}
    </div>
</div>

@endsection
