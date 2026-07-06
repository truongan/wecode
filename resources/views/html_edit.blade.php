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
			<div class="col-12 d-flex flex-wrap gap-2 mb-2 tiptap-toolbar" id="toolbar">
				<div class="btn-group" role="group" aria-label="History">
					<button type="button" class="btn btn-outline-secondary" data-cmd="undo" title="Undo">
						<i class="bi bi-arrow-counterclockwise"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="redo" title="Redo">
						<i class="bi bi-arrow-clockwise"></i>
					</button>
				</div>

				<select class="form-select form-select-sm w-auto" id="heading_select" title="Paragraph format">
					<option value="p">Normal</option>
					<option value="1">Heading 1</option>
					<option value="2">Heading 2</option>
					<option value="3">Heading 3</option>
					<option value="4">Heading 4</option>
					<option value="5">Heading 5</option>
					<option value="6">Heading 6</option>
				</select>

				<div class="btn-group" role="group" aria-label="Text style">
					<button type="button" class="btn btn-outline-secondary" data-cmd="bold" title="Bold">
						<i class="bi bi-type-bold"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="italic" title="Italic">
						<i class="bi bi-type-italic"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="underline" title="Underline">
						<i class="bi bi-type-underline"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="strike" title="Strikethrough">
						<i class="bi bi-type-strikethrough"></i>
					</button>
				</div>

				<div class="btn-group" role="group" aria-label="Blocks">
					<button type="button" class="btn btn-outline-secondary" data-cmd="blockquote" title="Blockquote">
						<i class="bi bi-blockquote-left"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="code_block" title="Code block">
						<i class="bi bi-code-square"></i>
					</button>
				</div>

				<div class="btn-group" role="group" aria-label="Insert">
					<button type="button" class="btn btn-outline-secondary" data-cmd="link" title="Link">
						<i class="bi bi-link-45deg"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="image" title="Image (or paste/drop an image file)">
						<i class="bi bi-image"></i>
					</button>
				</div>

				<button type="button" class="btn btn-outline-secondary" data-cmd="inline_math" title="LaTeX formula">&sum;</button>

				<div class="btn-group" role="group" aria-label="Table">
					<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Table">
						<i class="bi bi-table"></i>
					</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="#" data-cmd="insert_table">Insert table</a></li>
						<li><hr class="dropdown-divider" /></li>
						<li><a class="dropdown-item" href="#" data-cmd="add_row">Add row below</a></li>
						<li><a class="dropdown-item" href="#" data-cmd="delete_row">Delete row</a></li>
						<li><a class="dropdown-item" href="#" data-cmd="add_column">Add column after</a></li>
						<li><a class="dropdown-item" href="#" data-cmd="delete_column">Delete column</a></li>
						<li><hr class="dropdown-divider" /></li>
						<li><a class="dropdown-item" href="#" data-cmd="delete_table">Delete table</a></li>
					</ul>
				</div>

				<div class="btn-group" role="group" aria-label="Lists">
					<button type="button" class="btn btn-outline-secondary" data-cmd="ordered_list" title="Ordered list">
						<i class="bi bi-list-ol"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="bullet_list" title="Bullet list">
						<i class="bi bi-list-ul"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="task_list" title="Check list">
						<i class="bi bi-list-check"></i>
					</button>
				</div>

				<div class="btn-group" role="group" aria-label="Script">
					<button type="button" class="btn btn-outline-secondary" data-cmd="subscript" title="Subscript">
						<i class="bi bi-subscript"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="superscript" title="Superscript">
						<i class="bi bi-superscript"></i>
					</button>
				</div>

				<div class="btn-group" role="group" aria-label="Indent">
					<button type="button" class="btn btn-outline-secondary" data-cmd="outdent" title="Outdent">
						<i class="bi bi-text-indent-right"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="indent" title="Indent">
						<i class="bi bi-text-indent-left"></i>
					</button>
				</div>

				<div class="btn-group" role="group" aria-label="Color">
					<input
						type="color"
						class="form-control form-control-color btn btn-outline-secondary"
						id="text_color"
						value="#212529"
						title="Text color"
					/>
					<input
						type="color"
						class="form-control form-control-color btn btn-outline-secondary"
						id="background_color"
						value="#ffff00"
						title="Background color"
					/>
				</div>

				<div class="btn-group" role="group" aria-label="Align">
					<button type="button" class="btn btn-outline-secondary" data-cmd="align_left" title="Align left">
						<i class="bi bi-text-left"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="align_center" title="Align center">
						<i class="bi bi-text-center"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="align_right" title="Align right">
						<i class="bi bi-text-right"></i>
					</button>
					<button type="button" class="btn btn-outline-secondary" data-cmd="align_justify" title="Justify">
						<i class="bi bi-justify"></i>
					</button>
				</div>

				<button type="button" class="btn btn-outline-secondary" data-cmd="clean" title="Clear formatting">
					<i class="bi bi-eraser"></i>
				</button>

				<button type="button" class="btn btn-outline-secondary" data-cmd="source" title="View HTML source">
					<i class="bi bi-code-slash"></i>
				</button>
			</div>
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
