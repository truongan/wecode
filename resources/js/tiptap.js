// Entry point for the Tiptap browser bundle. esbuild compiles this into
// public/assets/tiptap/tiptap.min.js as an IIFE exposing everything under
// the `Tiptap` global (see scripts/sync-vendor-assets.js), mirroring how the
// other vendored libraries are consumed from Blade views.

export { Editor } from "@tiptap/core";
export { default as StarterKit } from "@tiptap/starter-kit";
export { default as Image } from "@tiptap/extension-image";
export { Mathematics } from "@tiptap/extension-mathematics";
export { default as Subscript } from "@tiptap/extension-subscript";
export { default as Superscript } from "@tiptap/extension-superscript";
export { default as TextAlign } from "@tiptap/extension-text-align";
export { TableKit } from "@tiptap/extension-table";
export { TextStyleKit } from "@tiptap/extension-text-style";
export { TaskList, TaskItem } from "@tiptap/extension-list";
