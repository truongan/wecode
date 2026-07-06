// Reusable Tiptap editor wiring for pages embedding the rich editor. Load
// assets/tiptap/tiptap.min.js (the `Tiptap` global) and this file, include
// assets/styles/tiptap_editor.css, then call:
//
//   const { editor, getCurrentHtml, setContent } = createTiptapEditor({
//       element: ...,        // div whose innerHTML is the initial content
//       source_element: ..., // <textarea> for the HTML source view
//       toolbar: ...,        // container of [data-cmd] buttons; the heading
//                            // select and color inputs are optional
//       on_update: ...,      // called whenever the content changes
//   });
//
// getCurrentHtml() returns the content of whichever view is active, and
// setContent(html) replaces it in both views.
function createTiptapEditor(config) {
	const editor_element = config.element;
	const source_editor = config.source_element;
	const toolbar = config.toolbar;
	const on_update = config.on_update || function () {};

	const initial_content = editor_element.innerHTML;
	editor_element.innerHTML = "";

	// Pasted/dropped image files become inline base64 <img> tags, so the
	// document stays a single self-contained HTML file.
	function insertImagesAsBase64(dataTransfer) {
		const image_files = Array.from(dataTransfer?.files ?? []).filter((file) => file.type.startsWith("image/"));
		if (image_files.length === 0) {
			return false;
		}
		image_files.forEach(function (file) {
			const reader = new FileReader();
			reader.onload = function (e) {
				editor.chain().focus().setImage({ src: e.target.result }).run();
			};
			reader.readAsDataURL(file);
		});
		return true;
	}

	function editMathOnClick(node, pos) {
		const latex = window.prompt("Edit LaTeX formula (leave empty to remove)", node.attrs.latex);
		if (latex === null) {
			return;
		}
		const selected = editor.chain().focus().setNodeSelection(pos);
		if (latex === "") {
			selected.deleteInlineMath().run();
		} else {
			selected.updateInlineMath({ latex: latex }).run();
		}
	}

	// Also accept CKEditor's legacy MathJax widget markup, e.g.
	// <span class="math-tex">\(s_1, \dots, s_K\)</span>, so old documents load
	// as math nodes. Saving re-serializes them in the new
	// <span data-type="inline-math" data-latex="..."> format.
	const InlineMathWithLegacy = Tiptap.InlineMath.extend({
		parseHTML() {
			return [
				...this.parent(),
				{
					tag: "span.math-tex",
					getAttrs: (element) => ({
						latex: element.textContent
							.replace(/^\s*(\\\(|\\\[|\$)/, "")
							.replace(/(\\\)|\\\]|\$)\s*$/, "")
							.trim(),
					}),
				},
			];
		},
	});

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
			InlineMathWithLegacy.configure({ onClick: editMathOnClick }),
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
		outdent: () =>
			editor.can().liftListItem("taskItem") ? chain().liftListItem("taskItem").run() : chain().liftListItem("listItem").run(),
		indent: () =>
			editor.can().sinkListItem("taskItem") ? chain().sinkListItem("taskItem").run() : chain().sinkListItem("listItem").run(),
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
		source: () => toggleSourceView(),
		insert_table: () => chain().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(),
		add_row: () => chain().addRowAfter().run(),
		delete_row: () => chain().deleteRow().run(),
		add_column: () => chain().addColumnAfter().run(),
		delete_column: () => chain().deleteColumn().run(),
		delete_table: () => chain().deleteTable().run(),
	};

	// HTML source view: swaps the rich editor for a plain textarea and syncs
	// content back into Tiptap when leaving it (or when reading the HTML).
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
		toolbar.querySelector('[data-cmd="source"]').classList.toggle("active", is_source_view);
		toolbar.querySelectorAll("button, select, input").forEach(function (control) {
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

	function setContent(html) {
		editor.commands.setContent(html);
		if (is_source_view) {
			source_editor.value = editor.getHTML();
		}
	}

	source_editor.addEventListener("input", on_update);
	editor.on("update", on_update);

	toolbar.querySelectorAll("[data-cmd]").forEach(function (button) {
		button.addEventListener("click", function (e) {
			e.preventDefault();
			commands[this.dataset.cmd]();
		});
	});

	const heading_select = toolbar.querySelector("#heading_select");
	if (heading_select) {
		heading_select.addEventListener("change", function () {
			if (this.value === "p") {
				chain().setParagraph().run();
			} else {
				chain()
					.toggleHeading({ level: parseInt(this.value, 10) })
					.run();
			}
		});
	}

	const text_color = toolbar.querySelector("#text_color");
	if (text_color) {
		// Seed the picker with the page's actual text color so the default
		// makes sense in dark themes too.
		const body_rgb = getComputedStyle(document.body).color.match(/\d+/g);
		if (body_rgb) {
			text_color.value =
				"#" +
				body_rgb
					.slice(0, 3)
					.map((channel) => Number(channel).toString(16).padStart(2, "0"))
					.join("");
		}
		text_color.addEventListener("input", function () {
			chain().setColor(this.value).run();
		});
	}

	const background_color = toolbar.querySelector("#background_color");
	if (background_color) {
		background_color.addEventListener("input", function () {
			chain().setBackgroundColor(this.value).run();
		});
	}

	const active_marks = [
		["bold", "bold"],
		["italic", "italic"],
		["underline", "underline"],
		["strike", "strike"],
		["blockquote", "blockquote"],
		["code_block", "codeBlock"],
		["ordered_list", "orderedList"],
		["bullet_list", "bulletList"],
		["task_list", "taskList"],
		["subscript", "subscript"],
		["superscript", "superscript"],
		["link", "link"],
	];
	editor.on("transaction", function () {
		active_marks.forEach(function ([cmd, name]) {
			const button = toolbar.querySelector('[data-cmd="' + cmd + '"]');
			if (button) {
				button.classList.toggle("active", editor.isActive(name));
			}
		});
		if (heading_select) {
			let heading = "p";
			for (let level = 1; level <= 6; level++) {
				if (editor.isActive("heading", { level: level })) {
					heading = String(level);
				}
			}
			heading_select.value = heading;
		}
	});

	return { editor: editor, getCurrentHtml: getCurrentHtml, setContent: setContent };
}
