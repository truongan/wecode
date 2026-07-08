{{-- Toolbar for the shared Tiptap editor (assets/js/tiptap_editor.js). --}}
<div class="{{ $toolbar_class ?? '' }} d-flex flex-wrap gap-2 mb-2 tiptap-toolbar" id="toolbar">
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
