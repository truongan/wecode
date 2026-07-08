@extends("layouts.app")
@section("head_title", "Edit by HTML")
@section("icon", "bi bi-pencil-square")

@section("title", "Edit by HTML")

@section("other_assets")
	<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/styles/tiptap_editor.css') }}" />
@endsection

@section("body_end")
	<script src="{{ asset('assets/tiptap/tiptap.min.js') }}"></script>
	<script src="{{ asset('assets/js/tiptap_editor.js') }}"></script>
	<script type="module">
		function b64EncodeUnicode(str) {
			//this function is shamelessly copied from: https://developer.mozilla.org/en/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
			return btoa(
				encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
					return String.fromCharCode("0x" + p1);
				}),
			);
		}
		document.addEventListener("DOMContentLoaded", function () {
			var file_name = "";

			let is_dirty = false;
			let is_saving = false;

			const { editor, getCurrentHtml, setContent } = createTiptapEditor({
				element: document.querySelector("#editor"),
				source_element: document.querySelector("#source_editor"),
				toolbar: document.querySelector("#toolbar"),
				on_update: function () {
					is_dirty = true;
					updateStatus();
				},
			});

			document.querySelector("#opendialog").addEventListener("change", function (e) {
				console.log(this.files);

				file_name = this.files[0].name;

				if (this.files && this.files[0]) {
					var reader = new FileReader();

					reader.onload = function (e) {
						setContent(e.target.result);
					};

					reader.readAsText(this.files[0]);
				}
			});

			document.querySelector("#open").onclick = function () {
				document.querySelector("#opendialog").click();
			};

			//FOR AUTOSAVING

			// If the user tries to leave the page before the data is saved, ask
			// them whether they are sure they want to proceed.
			window.addEventListener("beforeunload", (evt) => {
				if (is_saving) {
					evt.preventDefault();
				}
			});

			function updateStatus() {
				const saveButton = document.querySelector(".save-button");

				// Disables the "Save" button when the data on the server is up to date.
				if (is_dirty) {
					saveButton.classList.remove("disabled");
				} else {
					saveButton.classList.add("disabled");
				}

				// Shows the spinner animation.
				if (is_saving) {
					saveButton.classList.add("btn-lg");
				} else {
					saveButton.classList.remove("btn-lg");
				}
			}

			document.querySelector("#save").onmouseover = function () {
				document.querySelector("#save > a").href = "data:text/html;base64," + b64EncodeUnicode(getCurrentHtml());
				document.querySelector("#save > a").download = file_name;
			};
			document.querySelector(".save-button").onclick = function () {
				is_saving = true;
				updateStatus();
				const data = getCurrentHtml();
				$.ajax({
					type: "POST",
					url: "{{ route("htmleditor.autosave") }}",
					data: {
						_token: "{{ csrf_token() }}",
						content: data,
					},
					success: function (response) {
						if (response == "success") {
							notify("Change sucessfully saved", { position: "bottom right", className: "success", autoHideDelay: 3500 });
							$(".save-button").removeClass("btn-info").addClass("btn-secondary");
						}
						is_saving = false;
						if (data == editor.getHTML()) {
							is_dirty = false;
						}
						updateStatus();
					},
					error: function (response) {
						notify("Error while saving", { position: "bottom right", className: "error", autoHideDelay: 3500 });
						is_saving = false;
						updateStatus();
					},
				});
			};

			setInterval(
				() => {
					document.querySelector(".save-button").click();
				},
				1000 * 60 * 3,
			);
		});
	</script>
@endsection

@section("content")
	<div class="d-flex flex-column">
		<div class="row justify-content-center">
			<input type="file" style="display: none" id="opendialog" />

			<div class="col-auto">
				<a class="btn btn-primary" href="#" id="open"><i class="bi bi-folder2-open" aria-hidden="true"></i> Open</a>
			</div>

			<div class="col-auto me-auto ms-auto">
				<a class="btn btn-secondary save-button"> Save draft (will autosave every 3 minutes) </a>
			</div>

			<div id="save" class="col-auto">
				<a class="btn btn-primary" download href="#"><i class="bi bi-download" aria-hidden="true"></i>Download </a>
			</div>
		</div>

		<div class="row mt-3">
			@include("html_editor.tiptap_toolbar", ["toolbar_class" => "col-12"])
			<div class="edit_wrapper" id="editor">{!! $content !!}</div>
			<textarea
				id="source_editor"
				class="form-control font-monospace d-none"
				spellcheck="false"
				aria-label="HTML source"
			></textarea>
		</div>
	</div>
@endsection
