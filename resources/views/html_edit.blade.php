@extends('layouts.app')
@section('head_title','Edit by HTML')
@section('icon', 'bi bi-pencil-square')

@section('title', 'Edit by HTML')

@section('other_assets')
<link rel="stylesheet" href="{{ asset('assets/tiptap/katex.min.css') }}">
<style>
    #toolbar .btn {
        --bs-btn-padding-y: .25rem;
        --bs-btn-padding-x: .5rem;
    }
    #toolbar input[type="color"] {
        width: 2.1rem;
        height: 100%;
    }
    #editor .tiptap {
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: .375rem;
        min-height: 65vh;
        padding: 1rem 1.25rem;
    }
    #editor .tiptap:focus {
        outline: none;
        border-color: #86b7fe;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
    }
    #editor .tiptap img {
        max-width: 100%;
        height: auto;
    }
    #editor .tiptap blockquote {
        border-left: 3px solid #adb5bd;
        margin-left: 0;
        padding-left: 1rem;
    }
    #editor .tiptap pre {
        background: #212529;
        color: #f8f9fa;
        border-radius: .375rem;
        padding: .75rem 1rem;
    }
    #editor .tiptap table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1rem;
    }
    #editor .tiptap th,
    #editor .tiptap td {
        border: 1px solid #adb5bd;
        padding: .25rem .5rem;
        vertical-align: top;
    }
    #editor .tiptap th {
        background-color: #f8f9fa;
    }
    #editor .tiptap .selectedCell {
        background-color: rgba(13, 110, 253, .1);
    }
    #editor .tiptap .column-resize-handle {
        background-color: #86b7fe;
        width: 3px;
        position: absolute;
        top: 0;
        bottom: -2px;
        right: -2px;
        pointer-events: none;
    }
    #editor .tiptap.resize-cursor {
        cursor: col-resize;
    }
    #editor .tiptap ul[data-type="taskList"] {
        list-style: none;
        padding-left: .25rem;
    }
    #editor .tiptap ul[data-type="taskList"] li {
        display: flex;
        gap: .5rem;
    }
    #editor .tiptap [data-type="inline-math"],
    #editor .tiptap [data-type="block-math"] {
        cursor: pointer;
    }
    #editor .tiptap [data-type="block-math"] {
        text-align: center;
        padding: .25rem 0;
    }
    #editor .tiptap .ProseMirror-selectednode {
        outline: 2px solid #86b7fe;
        border-radius: .125rem;
    }
    #source_editor {
        min-height: 65vh;
        font-size: .875rem;
    }
</style>
@endsection

@section('body_end')
<script src="{{ asset('assets/tiptap/tiptap.min.js') }}"></script>

<script type="module">

function b64EncodeUnicode(str) {
	//this function is shamelessly copied from: https://developer.mozilla.org/en/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
}
document.addEventListener("DOMContentLoaded", function(){
	var file_name = "";

    let is_dirty = false;
    let is_saving = false;

    const editor_element = document.querySelector("#editor");
    const initial_content = editor_element.innerHTML;
    editor_element.innerHTML = "";

    // Pasted/dropped image files become inline base64 <img> tags, so the
    // downloaded document stays a single self-contained HTML file.
    function insertImagesAsBase64(dataTransfer) {
        const image_files = Array.from(dataTransfer?.files ?? [])
            .filter((file) => file.type.startsWith("image/"));
        if (image_files.length === 0) {
            return false;
        }
        image_files.forEach(function(file){
            const reader = new FileReader();
            reader.onload = function (e) {
                editor.chain().focus().setImage({ src: e.target.result }).run();
            };
            reader.readAsDataURL(file);
        });
        return true;
    }

    function editMathOnClick(update_command, delete_command) {
        return function (node, pos) {
            const latex = window.prompt("Edit LaTeX formula (leave empty to remove)", node.attrs.latex);
            if (latex === null) {
                return;
            }
            const selected = editor.chain().focus().setNodeSelection(pos);
            if (latex === "") {
                selected[delete_command]().run();
            } else {
                selected[update_command]({ latex: latex }).run();
            }
        };
    }

    const editor = new Tiptap.Editor({
        element: editor_element,
        editorProps: {
            handlePaste: (view, event) => insertImagesAsBase64(event.clipboardData),
            handleDrop: (view, event) => insertImagesAsBase64(event.dataTransfer),
        },
        extensions: [
            Tiptap.StarterKit.configure({
                link: { openOnClick: false },
            }),
            Tiptap.Image.configure({ allowBase64: true }),
            Tiptap.Mathematics.configure({
                inlineOptions: { onClick: editMathOnClick("updateInlineMath", "deleteInlineMath") },
                blockOptions: { onClick: editMathOnClick("updateBlockMath", "deleteBlockMath") },
            }),
            Tiptap.TableKit.configure({
                table: { resizable: true },
            }),
            Tiptap.Subscript,
            Tiptap.Superscript,
            Tiptap.TextAlign.configure({
                types: ["heading", "paragraph"],
            }),
            Tiptap.TextStyleKit,
            Tiptap.TaskList,
            Tiptap.TaskItem.configure({ nested: true }),
        ],
        content: initial_content,
    });

    const chain = () => editor.chain().focus();

    const commands = {
        undo: () => chain().undo().run(),
        redo: () => chain().redo().run(),
        bold: () => chain().toggleBold().run(),
        italic: () => chain().toggleItalic().run(),
        underline: () => chain().toggleUnderline().run(),
        strike: () => chain().toggleStrike().run(),
        blockquote: () => chain().toggleBlockquote().run(),
        code_block: () => chain().toggleCodeBlock().run(),
        ordered_list: () => chain().toggleOrderedList().run(),
        bullet_list: () => chain().toggleBulletList().run(),
        task_list: () => chain().toggleTaskList().run(),
        subscript: () => chain().unsetSuperscript().toggleSubscript().run(),
        superscript: () => chain().unsetSubscript().toggleSuperscript().run(),
        outdent: () => editor.can().liftListItem("taskItem")
            ? chain().liftListItem("taskItem").run()
            : chain().liftListItem("listItem").run(),
        indent: () => editor.can().sinkListItem("taskItem")
            ? chain().sinkListItem("taskItem").run()
            : chain().sinkListItem("listItem").run(),
        align_left: () => chain().setTextAlign("left").run(),
        align_center: () => chain().setTextAlign("center").run(),
        align_right: () => chain().setTextAlign("right").run(),
        align_justify: () => chain().setTextAlign("justify").run(),
        clean: () => chain().unsetAllMarks().clearNodes().run(),
        link: () => {
            if (editor.isActive("link")) {
                chain().unsetLink().run();
                return;
            }
            const url = window.prompt("Link URL");
            if (url) {
                chain().extendMarkRange("link").setLink({ href: url }).run();
            }
        },
        image: () => {
            const url = window.prompt("Image URL");
            if (url) {
                chain().setImage({ src: url }).run();
            }
        },
        inline_math: () => {
            const latex = window.prompt("LaTeX formula, e.g. e = mc^2");
            if (latex) {
                chain().insertInlineMath({ latex: latex }).run();
            }
        },
        block_math: () => {
            const latex = window.prompt("LaTeX formula (own line), e.g. \\int_a^b f(x)\\,dx");
            if (latex) {
                chain().insertBlockMath({ latex: latex }).run();
            }
        },
        source: () => toggleSourceView(),
        insert_table: () => chain().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(),
        add_row: () => chain().addRowAfter().run(),
        delete_row: () => chain().deleteRow().run(),
        add_column: () => chain().addColumnAfter().run(),
        delete_column: () => chain().deleteColumn().run(),
        delete_table: () => chain().deleteTable().run(),
    };

    // HTML source view: swaps the rich editor for a plain textarea and syncs
    // content back into Tiptap when leaving it (or when saving/downloading).
    const source_editor = document.querySelector("#source_editor");
    let is_source_view = false;

    function toggleSourceView() {
        if (is_source_view) {
            editor.commands.setContent(source_editor.value);
        } else {
            source_editor.value = editor.getHTML();
        }
        is_source_view = !is_source_view;
        editor_element.classList.toggle("d-none", is_source_view);
        source_editor.classList.toggle("d-none", !is_source_view);
        document.querySelector('[data-cmd="source"]').classList.toggle("active", is_source_view);
        document.querySelectorAll("#toolbar button, #toolbar select, #toolbar input").forEach(function(control){
            if (control.dataset.cmd !== "source") {
                control.disabled = is_source_view;
            }
        });
        if (is_source_view) {
            source_editor.focus();
        }
    }

    function getCurrentHtml() {
        if (is_source_view) {
            editor.commands.setContent(source_editor.value);
        }
        return editor.getHTML();
    }

    source_editor.addEventListener("input", function(){
        is_dirty = true;
        updateStatus();
    });

    document.querySelectorAll("[data-cmd]").forEach(function(button){
        button.addEventListener("click", function(e){
            e.preventDefault();
            commands[this.dataset.cmd]();
        });
    });

    const heading_select = document.querySelector("#heading_select");
    heading_select.addEventListener("change", function(){
        if (this.value === "p") {
            chain().setParagraph().run();
        } else {
            chain().toggleHeading({ level: parseInt(this.value, 10) }).run();
        }
    });

    document.querySelector("#text_color").addEventListener("input", function(){
        chain().setColor(this.value).run();
    });
    document.querySelector("#background_color").addEventListener("input", function(){
        chain().setBackgroundColor(this.value).run();
    });

    const active_marks = [
        ["bold", "bold"], ["italic", "italic"], ["underline", "underline"], ["strike", "strike"],
        ["blockquote", "blockquote"], ["code_block", "codeBlock"],
        ["ordered_list", "orderedList"], ["bullet_list", "bulletList"], ["task_list", "taskList"],
        ["subscript", "subscript"], ["superscript", "superscript"], ["link", "link"],
    ];
    editor.on("transaction", function(){
        active_marks.forEach(function([cmd, name]){
            document.querySelector('[data-cmd="' + cmd + '"]').classList.toggle("active", editor.isActive(name));
        });
        let heading = "p";
        for (let level = 1; level <= 6; level++) {
            if (editor.isActive("heading", { level: level })) {
                heading = String(level);
            }
        }
        heading_select.value = heading;
    });

	document.querySelector("#opendialog").addEventListener("change",function(e){
		console.log(this.files);

		file_name = this.files[0].name

		if (this.files && this.files[0]){
			var reader = new FileReader();

			reader.onload = function (e) {
				editor.commands.setContent(e.target.result);
				if (is_source_view) {
					source_editor.value = editor.getHTML();
				}
			}

			reader.readAsText(this.files[0]);
		}
	});

	document.querySelector("#open").onclick = function(){
		document.querySelector("#opendialog").click();
	};

    //FOR AUTOSAVING

    // Listen to new changes (to enable the "Save" button).
    editor.on( 'update', () => {
        is_dirty = true;

        updateStatus();
    } );

    // If the user tries to leave the page before the data is saved, ask
    // them whether they are sure they want to proceed.
    window.addEventListener( 'beforeunload', evt => {
        if ( is_saving ) {
            evt.preventDefault();
        }
    } );

    function updateStatus() {
        const saveButton = document.querySelector( '.save-button' );

        // Disables the "Save" button when the data on the server is up to date.
        if ( is_dirty ) {
            saveButton.classList.remove( 'disabled' );
        } else {
            saveButton.classList.add( 'disabled' );
        }

        // Shows the spinner animation.
        if ( is_saving ) {
            saveButton.classList.add( 'btn-lg' );
        } else {
            saveButton.classList.remove( 'btn-lg' );
        }
    }


	document.querySelector("#save").onmouseover = (function(){
		document.querySelector("#save > a").href = "data:text/html;base64," + b64EncodeUnicode( getCurrentHtml() ) ;
		document.querySelector("#save > a").download =  file_name ;
	});
    document.querySelector('.save-button').onclick = (function(){
        is_saving = true;
        updateStatus();
        const data = getCurrentHtml();
        $.ajax({
            type: 'POST',
            url: '{{ route('htmleditor.autosave') }}',
            data: {
                '_token': "{{ csrf_token() }}",
                'content' : data
            },
            success: function (response) {
                if (response == "success"){
                    notify('Change sucessfully saved'
                        , {position: 'bottom right', className: 'success', autoHideDelay: 3500});
                    $('.save-button').removeClass('btn-info').addClass('btn-secondary');
                }
                is_saving = false;
                if ( data == editor.getHTML() ) {
                    is_dirty = false;
                }
                updateStatus();

            },
            error: function(response){
                notify('Error while saving'
                    , {position: 'bottom right', className: 'error', autoHideDelay: 3500});
                is_saving = false;
                updateStatus();
            }
        });
    });

    setInterval(() => {
        document.querySelector('.save-button').click();
    }, 1000*60*3);
});

</script>
@endsection

@section('content')
<div class="d-flex flex-column">

    <div class="row justify-content-center">
        <input type="file" style="display: none;" id="opendialog">

        <div class="col-auto">
            <a class="btn btn-primary" href="#" id="open"><i class="bi bi-folder2-open" aria-hidden="true"></i> Open</a>
        </div>

        <div class="col-auto me-auto ms-auto">
            <a class="btn btn-secondary save-button">
                Save draft (will autosave every 3 minutes)
            </a>
        </div>

        <div id="save" class="col-auto">
            <a class="btn btn-primary"  download href="#"><i class="bi bi-download" aria-hidden="true"></i>Download </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 d-flex flex-wrap gap-2 mb-2" id="toolbar">
            <div class="btn-group" role="group" aria-label="History">
                <button type="button" class="btn btn-outline-secondary" data-cmd="undo" title="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="redo" title="Redo"><i class="bi bi-arrow-clockwise"></i></button>
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
                <button type="button" class="btn btn-outline-secondary" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="strike" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Blocks">
                <button type="button" class="btn btn-outline-secondary" data-cmd="blockquote" title="Blockquote"><i class="bi bi-blockquote-left"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="code_block" title="Code block"><i class="bi bi-code-square"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Insert">
                <button type="button" class="btn btn-outline-secondary" data-cmd="link" title="Link"><i class="bi bi-link-45deg"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="image" title="Image (or paste/drop an image file)"><i class="bi bi-image"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Formula">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="LaTeX formula">&sum;</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-cmd="inline_math">Inline formula</a></li>
                    <li><a class="dropdown-item" href="#" data-cmd="block_math">Block formula</a></li>
                </ul>
            </div>

            <div class="btn-group" role="group" aria-label="Table">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Table"><i class="bi bi-table"></i></button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-cmd="insert_table">Insert table</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-cmd="add_row">Add row below</a></li>
                    <li><a class="dropdown-item" href="#" data-cmd="delete_row">Delete row</a></li>
                    <li><a class="dropdown-item" href="#" data-cmd="add_column">Add column after</a></li>
                    <li><a class="dropdown-item" href="#" data-cmd="delete_column">Delete column</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-cmd="delete_table">Delete table</a></li>
                </ul>
            </div>

            <div class="btn-group" role="group" aria-label="Lists">
                <button type="button" class="btn btn-outline-secondary" data-cmd="ordered_list" title="Ordered list"><i class="bi bi-list-ol"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="bullet_list" title="Bullet list"><i class="bi bi-list-ul"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="task_list" title="Check list"><i class="bi bi-list-check"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Script">
                <button type="button" class="btn btn-outline-secondary" data-cmd="subscript" title="Subscript"><i class="bi bi-subscript"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="superscript" title="Superscript"><i class="bi bi-superscript"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Indent">
                <button type="button" class="btn btn-outline-secondary" data-cmd="outdent" title="Outdent"><i class="bi bi-text-indent-right"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="indent" title="Indent"><i class="bi bi-text-indent-left"></i></button>
            </div>

            <div class="btn-group" role="group" aria-label="Color">
                <input type="color" class="form-control form-control-color btn btn-outline-secondary" id="text_color" value="#212529" title="Text color">
                <input type="color" class="form-control form-control-color btn btn-outline-secondary" id="background_color" value="#ffff00" title="Background color">
            </div>

            <div class="btn-group" role="group" aria-label="Align">
                <button type="button" class="btn btn-outline-secondary" data-cmd="align_left" title="Align left"><i class="bi bi-text-left"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="align_center" title="Align center"><i class="bi bi-text-center"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="align_right" title="Align right"><i class="bi bi-text-right"></i></button>
                <button type="button" class="btn btn-outline-secondary" data-cmd="align_justify" title="Justify"><i class="bi bi-justify"></i></button>
            </div>

            <button type="button" class="btn btn-outline-secondary" data-cmd="clean" title="Clear formatting"><i class="bi bi-eraser"></i></button>

            <button type="button" class="btn btn-outline-secondary" data-cmd="source" title="View HTML source"><i class="bi bi-code-slash"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="edit_wrapper" id="editor" >
            {!! $content !!}
        </div>
        <textarea id="source_editor" class="form-control font-monospace d-none" spellcheck="false" aria-label="HTML source"></textarea>
    </div>
</div>
@endsection
