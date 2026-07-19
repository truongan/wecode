{{-- Toolbar for the shared Tiptap editor (assets/js/tiptap_editor.js). --}}
<div class="{{ $toolbar_class ?? '' }} d-flex flex-wrap gap-1 mb-1 tiptap-toolbar" id="toolbar">
	<div class="btn-group" role="group" aria-label="History">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="undo" title="Undo">
			<i class="bi bi-arrow-counterclockwise"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="redo" title="Redo">
			<i class="bi bi-arrow-clockwise"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Paragraph format">
		<button
			type="button"
			class="btn btn-sm btn-dark dropdown-toggle"
			data-bs-toggle="dropdown"
			id="heading_dropdown"
			title="Paragraph format"
		>
			<i class="bi bi-paragraph"></i>
		</button>
		<ul class="dropdown-menu">
			<li>
				<a class="dropdown-item" href="#" data-heading="p"><i class="bi bi-paragraph"></i> Normal</a>
			</li>
			<li>
				<a class="dropdown-item h1 mb-0" href="#" data-heading="1"><i class="bi bi-type-h1"></i> Heading 1</a>
			</li>
			<li>
				<a class="dropdown-item h2 mb-0" href="#" data-heading="2"><i class="bi bi-type-h2"></i> Heading 2</a>
			</li>
			<li>
				<a class="dropdown-item h3 mb-0" href="#" data-heading="3"><i class="bi bi-type-h3"></i> Heading 3</a>
			</li>
			<li>
				<a class="dropdown-item h4 mb-0" href="#" data-heading="4"><i class="bi bi-type-h4"></i> Heading 4</a>
			</li>
			<li>
				<a class="dropdown-item h5 mb-0" href="#" data-heading="5"><i class="bi bi-type-h5"></i> Heading 5</a>
			</li>
			<li>
				<a class="dropdown-item h6 mb-0" href="#" data-heading="6"><i class="bi bi-type-h6"></i> Heading 6</a>
			</li>
		</ul>
	</div>

	<div class="btn-group" role="group" aria-label="Text style">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="bold" title="Bold">
			<i class="bi bi-type-bold"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="italic" title="Italic">
			<i class="bi bi-type-italic"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="underline" title="Underline">
			<i class="bi bi-type-underline"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="strike" title="Strikethrough">
			<i class="bi bi-type-strikethrough"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Blocks">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="blockquote" title="Blockquote">
			<i class="bi bi-blockquote-left"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="code_block" title="Code block">
			<i class="bi bi-code-square"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Insert">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="link" title="Link">
			<i class="bi bi-link-45deg"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="image" title="Image (or paste/drop an image file)">
			<i class="bi bi-image"></i>
		</button>
	</div>

	<button type="button" class="btn btn-sm btn-dark" data-cmd="inline_math" title="LaTeX formula">&sum;</button>

	<div class="btn-group" role="group" aria-label="Table">
		<button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-bs-toggle="dropdown" title="Table">
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
		<button type="button" class="btn btn-sm btn-dark" data-cmd="ordered_list" title="Ordered list">
			<i class="bi bi-list-ol"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="bullet_list" title="Bullet list">
			<i class="bi bi-list-ul"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="task_list" title="Check list">
			<i class="bi bi-list-check"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Script">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="subscript" title="Subscript">
			<i class="bi bi-subscript"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="superscript" title="Superscript">
			<i class="bi bi-superscript"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Indent">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="outdent" title="Outdent">
			<i class="bi bi-text-indent-right"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="indent" title="Indent">
			<i class="bi bi-text-indent-left"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Color">
		<input
			type="color"
			class="form-control form-control-color btn btn-sm btn-dark"
			id="text_color"
			value="#212529"
			title="Text color"
		/>
		<input
			type="color"
			class="form-control form-control-color btn btn-sm btn-dark"
			id="background_color"
			value="#ffff00"
			title="Background color"
		/>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="unset_color" title="Remove text and background color">
			<i class="bi bi-droplet"></i>
		</button>
	</div>

	<div class="btn-group" role="group" aria-label="Align">
		<button type="button" class="btn btn-sm btn-dark" data-cmd="align_left" title="Align left">
			<i class="bi bi-text-left"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="align_center" title="Align center">
			<i class="bi bi-text-center"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="align_right" title="Align right">
			<i class="bi bi-text-right"></i>
		</button>
		<button type="button" class="btn btn-sm btn-dark" data-cmd="align_justify" title="Justify">
			<i class="bi bi-justify"></i>
		</button>
	</div>

	<button type="button" class="btn btn-sm btn-dark" data-cmd="clean" title="Clear formatting">
		<i class="bi bi-eraser"></i>
	</button>

	<button type="button" class="btn btn-sm btn-dark" data-cmd="source" title="View HTML source">
		<i class="bi bi-code-slash"></i>
	</button>
</div>
