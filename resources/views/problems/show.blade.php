@extends("layouts.app")
@php($selected ?? ($selected = "assignments"))
@php($can_edit_description = in_array(Auth::user()->role->name, ["admin", "head_instructor"]))
@if ($all_problems != null)
	@php($pdf_route = route("assignments.show_pdf", ["assignment" => $assignment, "problem" => $problem]))
@else
	@php($pdf_route = route("practices.show_pdf", $problem))
@endif
@section("head_title", "View Problem")
@section("icon", "bi bi-puzzle-fill")

@section("title")
	{{
		isset($all_problems)
			? $all_problems->find($problem->id)->pivot->problem_name
			: $problem->name
	}}
@endsection

@section("other_assets")
	<style media="screen">
		.wecode_table td {
			text-align: left;
		}
		#problem_pdf_embed {
			height: 50rem;
			border: 1rem solid rgba(0, 0, 0, 0.1);
		}
		#problem_description table tr {
			border-width: 1px;
		}
	</style>
	<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}" />
	@if ($can_edit_description)
		<link rel="stylesheet" href="{{ asset('assets/styles/tiptap_editor.css') }}" />
	@endif
@endsection
@if (!isset($error))
	@section("title_menu")
		@if ($problem->has_pdf)
			<a href="{{ $pdf_route }}" class="link-dark-subtle"
				><span class="fs-6 ms-4"><i class="bi bi-file-earmark-pdf-fill text-danger"></i> PDF</span></a
			>
		@endif
		@if ($problem->allow_input_download)
			<span class="fs-6 ms-4"
				><a
					href="{{ route('problems.download_testcases', ['problem' => $problem->id, 'assignment' => ($all_problems != NULL ? $assignment->id : 0), 'type' => 'in'] ) }}"
					class="link-dark"
					><i class="bi bi-download text-success"></i> Download testcases' input</a
				></span
			>
		@endif
		@if ($problem->allow_output_download)
			<span class="fs-6 ms-4"
				><a
					href="{{ route('problems.download_testcases', ['problem' => $problem->id, 'assignment' => ($all_problems != NULL ? $assignment->id : 0), 'type' => 'out'] ) }}"
					class="link-dark"
					><i class="bi bi-download text-primary"></i> Download testcases' output</a
				></span
			>
		@endif
		@if ($can_edit_description)
			<span class="fs-6 ms-4 ms-auto"
				><a href="#" class="btn btn-secondary save-button"><i class="bi bi-save"></i> Save</a></span
			>
		@endif

	@endsection
	@section("body_end")
		@if ($can_edit_description)
			<script src="{{ asset('assets/tiptap/tiptap.min.js') }}"></script>
			<script src="{{ asset('assets/js/tiptap_editor.js') }}"></script>
			<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function () {
					const { getCurrentHtml } = createTiptapEditor({
						element: document.querySelector("#problem_description"),
						source_element: document.querySelector("#source_editor"),
						toolbar: document.querySelector("#toolbar"),
						on_update: function () {
							$(".save-button").removeClass("btn-secondary").addClass("btn-info");
						},
					});

					$(".save-button").click(function () {
						$.ajax({
							type: "POST",
							url: "{{ route("problems.edit_description", $problem->id) }}",
							data: {
								_token: "{{ csrf_token() }}",
								content: getCurrentHtml(),
							},
							success: function (response) {
								if (response == "success") {
									notify("Change sucessfully saved", { position: "bottom right", className: "success", autoHideDelay: 3500 });
									$(".save-button").removeClass("btn-info").addClass("btn-secondary");
								}
							},
							error: function (response) {
								notify("Error while saving", { position: "bottom right", className: "error", autoHideDelay: 3500 });
							},
						});
					});
				});
			</script>
		@else
			<script src="{{ asset('assets/tiptap/katex.min.js') }}"></script>
			<script src="{{ asset('assets/tiptap/auto-render.min.js') }}"></script>
			<script type="text/javascript">
				// Give formulas saved by the Tiptap editor as
				// <span data-type="inline-math" data-latex="..."></span> the
				// \(...\) delimiters, then typeset them together with legacy
				// $...$ and \(...\) math.
				const description = document.querySelector("#problem_description");
				description.querySelectorAll('[data-type="inline-math"]').forEach(function (span) {
					span.textContent = "\\(" + span.getAttribute("data-latex") + "\\)";
				});
				renderMathInElement(description, {
					delimiters: [
						{ left: "$$", right: "$$", display: true },
						{ left: "$", right: "$", display: false },
						{ left: "\\(", right: "\\)", display: false },
						{ left: "\\[", right: "\\]", display: true },
					],
					throwOnError: false,
				});
			</script>
		@endif

	@endsection
@endif
@section("content")
	@if (isset($error))
		<div class="alert alert-danger">{{ $error }}</div>
	@else
		<div class="row">
			<div class="col-md-7 col-lg-8 col-sm-12">
				@if ($problem->has_pdf)
					<div class="problem_description" id="problem_pdf_embed">
						<object data="{{ $pdf_route }}" type="application/pdf" width="100%" height="100%">
							<p>If this browser does not support PDFs. Please download the PDF to view it: <a href="{{ $pdf_route }}">Download PDF</a>.</p>
						</object>
					</div>
				@endif
				@if ($can_edit_description)
					@include("html_editor.tiptap_toolbar")
				@endif
				<div class="problem_description" id="problem_description">
					{!! $problem->description !!}
				</div>
				@if ($can_edit_description)
					<textarea
						id="source_editor"
						class="form-control font-monospace d-none"
						spellcheck="false"
						aria-label="HTML source"
					></textarea>
				@endif
			</div>

			<div class="col-md-5 col-lg-4">
				@if ($all_problems != null)
					@php($i = 0)
					<div class="problems_widget">
						@if (in_array(Auth::user()->role->name, ["admin", "head_instructor"]))
							<a href="{{ route('assignments.edit', $assignment->id) }}"> <i class="bi bi-pencil-square color9"></i>
						@endif
						{{ $assignment->name }} <br />
						@if (in_array(Auth::user()->role->name, ["admin", "head_instructor"]))
							Lớp: {{ $assignment->lops->pluck("name")->join(",") }} </a>
						@endif

						<p class="text-muted"><span class="badge bg-secondary count_problems">{{ count($all_problems) }}</span> problems with a total score of <span class="badge bg-secondary sum_score">{{ $sum_score }}</span></p>
						<table class="wecode_table table-bordered table">
							<thead>
								<tr>
									<th>#</th>
									<th>Problem</th>
									<th>Score</th>
								</tr>
							</thead>
							@foreach ($all_problems as $one_problem)
								@php($i = $i + 1)
								<tr class=" {{ $problem->id == $one_problem->id ? "table-active":"" }} ">
									<td>{{ $i }}</td>
									<td>
										@php($t = $assignment != null ? $assignment->id : "")
										<a
											href="{{route('assignments.show', ['assignment'=>$assignment,'problem_id'=>$one_problem->id])}}"
											>{{ $one_problem->pivot->problem_name }}</a
										>
									</td>
									<td class="{{ isset($problem_status[$one_problem->id])? $problem_status[$one_problem->id] :'' }}">
										<span>{{ $one_problem->pivot->score }}</span>
									</td>
								</tr>
							@endforeach
						</table>
					</div>
				@endif

				@if ($can_submit)
					<div class="problems_widget">
						<span><i class="bi bi-upload fs-5 text-success"></i> Submit</span>

						<form
							action="{{ route('submissions.store') }}"
							method="POST"
							enctype="multipart/form-data"
							class="row g-2 align-items-end"
						>
							@csrf

							@if ($all_problems != null)
								<input type="hidden" name="assignment" value="{{ $assignment->id }}" />
							@else
								{{-- Default assignment to practice --}}
								<input type="hidden" name="assignment" value="0" />
							@endif
							<input type="hidden" name="problem" value="{{ $problem->id }}" />

							<div class="">
								<label class="text-muted"><small>upload source code</small></label>
								<input type="file" id="file" class="form-control" name="userfile" />
							</div>

							<div class="col-8">
								<div class="form-floating">
									<select id="languages" name="language" class="form-select">
										@foreach ($problem->languages as $l)
											@if ($assignment == null || in_array($l->id, explode(", ", $assignment->language_ids)))
												<option value="{{ $l->id }}">
													{{ $l->name }} ({{ $l->pivot->time_limit / 1000 }}s, {{ $l->pivot->memory_limit / 1000 }}MB )
												</option>
											@endif
										@endforeach
									</select>
									<label>Select language</label>
								</div>
							</div>
							<div class="col-4">
								<input type="submit" value="Submit" class="form-control btn btn-primary btn-lg" />
							</div>
						</form>
					</div>
					<div class="problems_widget row">
						@php($t = $assignment->id ?? 0)
						<span class=""
							><a href="{{ route("submissions.create", ['assignment' => $t, 'problem' => $problem->id]) }}" target="_blank"
								><i class="bi bi-pencil-square"></i> Code editor</a
							></span
						>
					</div>
				@endif
			</div>
		</div>
	@endif

@endsection
