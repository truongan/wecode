@php($selected = 'notifications')
@extends('layouts.app')
@section('head_title','Edit Notification')
@section('icon', 'bi bi-plus')

@section('title', 'Edit Notification')

@section('other_assets')
<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/styles/tiptap_editor.css') }}" />
@endsection

@section('body_end')
<script src="{{ asset('assets/tiptap/tiptap.min.js') }}"></script>
<script src="{{ asset('assets/js/tiptap_editor.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
	const notif_text = document.querySelector("#notif_text");
	const { getCurrentHtml } = createTiptapEditor({
		element: document.querySelector("#editor"),
		source_element: document.querySelector("#source_editor"),
		toolbar: document.querySelector("#toolbar"),
	});
	notif_text.form.addEventListener("submit", function(){
		notif_text.value = getCurrentHtml();
	});
});
</script>
@endsection

@section('content')
<form action="/notifications/{{ $notification->id }}" method="POST">
    @method('PUT')
    <input type="hidden"  name ="_token" value="{!! csrf_token() !!}"/>
    <input type="hidden" name="id" value="{{ $notification->id }}"/>

    <p class="input_p">
        <label for="form_title" class="tiny">Title:</label>
        <input id="form_title" name="title" type="text" class="sharif_input" value=" {{$notification->title}} "/>
    </p>
    <div class="input_p">
        <label class="tiny">Text:</label>
        @include("html_editor.tiptap_toolbar")
        <div class="edit_wrapper" id="editor">{!! $notification->text !!}</div>
        <textarea
            id="source_editor"
            class="form-control font-monospace d-none"
            spellcheck="false"
            aria-label="HTML source"
        ></textarea>
        <textarea id="notif_text" name="text" class="d-none" aria-hidden="true"></textarea>
    </div>
    <p class="input_p">
        <input type="submit" value="Save" class="sharif_input"/>
    </p>
</form>
@endsection
