@php($selected = "notifications")
@extends("layouts.app")
@section("head_title", "Notifications")
@section("icon", "bi bi-bell-fill")

@section("title", "Notifications")

@section("other_assets")
	<style>
		#more {
			display: none;
		}
	</style>
	<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}" />
@endsection

@section("title_menu")
	@if (in_array(Auth::user()->role->name, ["admin", "head_instructor"]))
		<span class="ms-4 fs-6"
			><a href="{{ route('notifications.create') }}"><i class="bi bi-plus color10"></i> New</a></span
		>
	@endif
@endsection

@section("body_end")
	<div class="modal fade" id="notification_delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this notification?</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger confirm-notifycation-delete">yes, DELETE it</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal">NO, DON'T delete</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		/**
		 * Notifications
		 */
		document.addEventListener("DOMContentLoaded", function () {
			$(".del_n").click(function () {
				var notif = $(this).parents(".notif");
				var id = $(notif).data("id");

				$(".confirm-notifycation-delete").off();
				$(".confirm-notifycation-delete").click(function () {
					$.ajax({
						type: "DELETE",
						url: "{{ route("notifications.index") }}/" + id,
						data: {
							_token: "{{ csrf_token() }}",
						},
						error: shj.loading_error,
						success: function (response) {
							if (response.done) {
								notif.animate({ backgroundColor: "#FF7676" }, 100, function () {
									notif.remove();
								});
								notify("Notification deleted", { position: "bottom right", className: "success", autoHideDelay: 5000 });
								$("#notification_delete").modal("hide");
							} else shj.loading_failed(response.message);
						},
					});
				});
				$("#notification_delete").modal("show");
			});
		});
	</script>
	<script src="{{ asset('assets/tiptap/katex.min.js') }}"></script>
	<script src="{{ asset('assets/tiptap/auto-render.min.js') }}"></script>
	<script type="text/javascript">
		// Give formulas saved by the Tiptap editor as
		// <span data-type="inline-math" data-latex="..."></span> the
		// \(...\) delimiters, then typeset them together with legacy
		// $...$ and \(...\) math.
		document.querySelectorAll(".notif_text").forEach(function (notif_text) {
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
		});
	</script>
@endsection

@section("content")
	@if ($notifications->all() == [])
		<p></p>
	@endif
	@foreach ($notifications as $notification)
		<div class="notif border shadow rounded-5 p-2 mb-2" id="number{{ $notification->id }}" data-id="{{ $notification->id }}">
			<div class="border-bottom">
				<a class="lead" href="{{ route('notifications.show', $notification->id) }}"
					>{{ $notification->title }} - Author: {{ $notification->user->username }}</a
				>
				<div class="notif_meta text-muted">
					{{ $notification->created_at }}
					@if (in_array(Auth::user()->role->name, ["admin", "head_instructor"]))
						<a href="notifications/{{ $notification->id }}/edit">Edit</a>
						<span class="pointer del_n text-danger">Delete</span>
					@endif
				</div>
			</div>
			<div class="notif_text p-2">{!! $notification->text !!}</div>
		</div>

	@endforeach
	{!! $notifications->render() !!}
@endsection
