// Copies third-party libraries from node_modules into public/assets, using
// the exact paths our Blade views reference. Runs on every `npm install`
// (see "postinstall" in package.json) so `npm update` actually takes effect
// on the page instead of just bumping a version in node_modules.
//
// Libraries with no clean npm equivalent (ckeditor, ckeditor5, sbadmin,
// fullcalendar) are intentionally NOT handled here.

const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "..");
const nodeModules = path.join(root, "node_modules");
const assets = path.join(root, "public", "assets");

function copyFile(src, dest) {
	fs.mkdirSync(path.dirname(dest), { recursive: true });
	fs.copyFileSync(src, dest);
}

function copyDir(src, dest, { exclude = [] } = {}) {
	fs.rmSync(dest, { recursive: true, force: true });
	fs.mkdirSync(dest, { recursive: true });
	for (const entry of fs.readdirSync(src, { withFileTypes: true })) {
		if (exclude.includes(entry.name)) {
			continue;
		}
		const srcPath = path.join(src, entry.name);
		const destPath = path.join(dest, entry.name);
		if (entry.isDirectory()) {
			copyDir(srcPath, destPath);
		} else {
			fs.copyFileSync(srcPath, destPath);
		}
	}
}

function concatFiles(srcPaths, dest) {
	const content = srcPaths.map((p) => fs.readFileSync(p, "utf8")).join("\n");
	fs.mkdirSync(path.dirname(dest), { recursive: true });
	fs.writeFileSync(dest, content);
}

const nm = (...parts) => path.join(nodeModules, ...parts);
const out = (...parts) => path.join(assets, ...parts);

// --- Simple 1:1 file copies ---------------------------------------------

const copies = [
	[nm("quill", "dist", "quill.js"), out("quill", "quill.js")],
	[nm("quill", "dist", "quill.snow.css"), out("quill", "quill.snow.css")],

	[nm("bootstrap-icons", "font", "bootstrap-icons.min.css"), out("bootstrap-icons", "bootstrap-icons.min.css")],
	[nm("bootstrap-icons", "font", "fonts", "bootstrap-icons.woff"), out("bootstrap-icons", "fonts", "bootstrap-icons.woff")],
	[nm("bootstrap-icons", "font", "fonts", "bootstrap-icons.woff2"), out("bootstrap-icons", "fonts", "bootstrap-icons.woff2")],

	[nm("frappe-charts", "dist", "frappe-charts.min.umd.js"), out("frappe", "frappe-charts.min.iife.js")],

	[nm("select2", "dist", "js", "select2.min.js"), out("select2", "select2.min.js")],
	[nm("select2", "dist", "css", "select2.min.css"), out("select2", "select2.min.css")],
	[nm("select2-bootstrap-5-theme", "dist", "select2-bootstrap-5-theme.min.css"), out("select2", "select2-bootstrap-5-theme.min.css")],

	[nm("slim-select", "dist", "slimselect.js"), out("slimselect", "slimselect.js")],
	[nm("slim-select", "dist", "slimselect.css"), out("slimselect", "slimselect.css")],

	[nm("bootstrap", "dist", "js", "bootstrap.bundle.min.js"), out("js", "bootstrap.bundle.min.js")],
	[nm("bootstrap", "dist", "js", "bootstrap.bundle.min.js.map"), out("js", "bootstrap.bundle.min.js.map")],
	[nm("bootstrap", "dist", "css", "bootstrap.min.css"), out("styles", "bootstrap", "default.min.css")],

	[nm("sortablejs", "Sortable.min.js"), out("js", "Sortable.min.js")],
];

for (const [src, dest] of copies) {
	copyFile(src, dest);
}

// --- Directory copies -----------------------------------------------------

copyDir(nm("ace-builds", "src-min-noconflict"), out("ace"));

copyDir(nm("mathjax"), out("mathjax"), {
	exclude: ["package.json", "README.md", "LICENSE", "CONTRIBUTING.md", "test"],
});

// --- Bootswatch themes ------------------------------------------------------

for (const theme of fs.readdirSync(nm("bootswatch", "dist"))) {
	copyFile(nm("bootswatch", "dist", theme, "bootstrap.min.css"), out("styles", "bootstrap", `${theme}.min.css`));
}

// --- DataTables: core + Bootstrap 5 styling, concatenated like the
//     datatables.net/download builder produces ---------------------------

concatFiles(
	[nm("datatables.net", "js", "dataTables.js"), nm("datatables.net-bs5", "js", "dataTables.bootstrap5.js")],
	out("DataTables", "datatables.min.js"),
);
concatFiles([nm("datatables.net-bs5", "css", "dataTables.bootstrap5.css")], out("DataTables", "datatables.min.css"));

// --- Prism: matches the exact recipe baked into the current theme file's
//     header comment (themes=prism-solarizedlight&languages=clike+javascript
//     +c+cpp+java+pascal+python&plugins=line-numbers+toolbar) --------------

concatFiles(
	["core", "clike", "javascript", "c", "cpp", "java", "pascal", "python"].map((lang) =>
		nm("prismjs", "components", `prism-${lang}.js`),
	),
	out("prismjs", "prism.js"),
);
concatFiles(
	[
		nm("prismjs", "themes", "prism-solarizedlight.css"),
		nm("prismjs", "plugins", "line-numbers", "prism-line-numbers.css"),
		nm("prismjs", "plugins", "toolbar", "prism-toolbar.css"),
	],
	out("prismjs", "prism.css"),
);

console.log("Synced vendor assets into public/assets.");
