// Copies third-party libraries from node_modules into public/assets, using
// the exact paths our Blade views reference. Runs on every `npm install`
// (see "postinstall" in package.json) so `npm update` actually takes effect
// on the page instead of just bumping a version in node_modules.
//
// Libraries with no clean npm equivalent (ckeditor, ckeditor5, sbadmin,
// fullcalendar) are intentionally NOT handled here.

const { execSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "..");
const nm = path.join(root, "node_modules");
const assets = path.join(root, "public", "assets");

const sh = (cmd) => execSync(cmd, { cwd: root, shell: "/bin/bash", stdio: "inherit" });
const copy = (src, dest) => sh(`mkdir -p "${assets}/${path.dirname(dest)}" && cp -R "${nm}/${src}" "${assets}/${dest}"`);
const concat = (srcs, dest) => {
	fs.mkdirSync(path.join(assets, path.dirname(dest)), { recursive: true });
	fs.writeFileSync(path.join(assets, dest), srcs.map((s) => fs.readFileSync(path.join(nm, s), "utf8")).join("\n"));
};

// --- Simple 1:1 file copies ---------------------------------------------
copy("quill/dist/quill.js", "quill/quill.js");
copy("quill/dist/quill.snow.css", "quill/quill.snow.css");
copy("bootstrap-icons/font/bootstrap-icons.min.css", "bootstrap-icons/bootstrap-icons.min.css");
copy("bootstrap-icons/font/fonts/bootstrap-icons.woff", "bootstrap-icons/fonts/bootstrap-icons.woff");
copy("bootstrap-icons/font/fonts/bootstrap-icons.woff2", "bootstrap-icons/fonts/bootstrap-icons.woff2");
copy("frappe-charts/dist/frappe-charts.min.umd.js", "frappe/frappe-charts.min.iife.js");
copy("select2/dist/js/select2.min.js", "select2/select2.min.js");
copy("select2/dist/css/select2.min.css", "select2/select2.min.css");
copy("select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css", "select2/select2-bootstrap-5-theme.min.css");
copy("slim-select/dist/slimselect.js", "slimselect/slimselect.js");
copy("slim-select/dist/slimselect.css", "slimselect/slimselect.css");
copy("bootstrap/dist/js/bootstrap.bundle.min.js", "js/bootstrap.bundle.min.js");
copy("bootstrap/dist/js/bootstrap.bundle.min.js.map", "js/bootstrap.bundle.min.js.map");
copy("bootstrap/dist/css/bootstrap.min.css", "styles/bootstrap/default.min.css");
copy("sortablejs/Sortable.min.js", "js/Sortable.min.js");

// --- Directory copies -----------------------------------------------------
sh(`rm -rf "${assets}/ace" && mkdir -p "${assets}/ace" && cp -R "${nm}/ace-builds/src-min-noconflict/." "${assets}/ace/"`);
sh(`rm -rf "${assets}/mathjax" && mkdir -p "${assets}/mathjax" && cp -R "${nm}/mathjax/." "${assets}/mathjax/"`);
sh(`rm -rf "${assets}/mathjax/"{package.json,README.md,LICENSE,CONTRIBUTING.md,test}`);

// --- Bootswatch themes ------------------------------------------------------
for (const theme of fs.readdirSync(`${nm}/bootswatch/dist`)) {
	copy(`bootswatch/dist/${theme}/bootstrap.min.css`, `styles/bootstrap/${theme}.min.css`);
}

// --- DataTables: core + Bootstrap 5 styling, concatenated like the
//     datatables.net/download builder produces ---------------------------
concat(["datatables.net/js/dataTables.min.js", "datatables.net-bs5/js/dataTables.bootstrap5.min.js"], "DataTables/datatables.min.js");
copy("datatables.net-bs5/css/dataTables.bootstrap5.min.css", "DataTables/datatables.min.css");

// --- Prism: matches the exact recipe baked into the current theme file's
//     header comment (themes=prism-solarizedlight&languages=clike+javascript
//     +c+cpp+java+pascal+python&plugins=line-numbers+toolbar) --------------
concat(
	["core", "clike", "javascript", "c", "cpp", "java", "pascal", "python"].map((l) => `prismjs/components/prism-${l}.min.js`),
	"prismjs/prism.js",
);
concat(
	[
		"prismjs/themes/prism-solarizedlight.min.css",
		"prismjs/plugins/line-numbers/prism-line-numbers.min.css",
		"prismjs/plugins/toolbar/prism-toolbar.min.css",
	],
	"prismjs/prism.css",
);

console.log("Synced vendor assets into public/assets.");
