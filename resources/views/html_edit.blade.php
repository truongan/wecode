@extends('layouts.app')
@section('head_title','Edit by HTML')
@section('icon', 'fas fa-edit')

@section('title', 'Edit by HTML')

@section('other_assets')
{{-- <link rel="stylesheet"  href= {{ asset('assets/ckeditor5-42.0.0/ckeditor5/ckeditor5.css') }} /> --}}
<style>
    .ck-editor{
        width: 100% !important;
    }
</style>
@endsection

@section('body_end')
<script type="text/javascript">
    mathjax_path = "{{ asset('assets/MathJax-2.7.9') }}/MathJax.js?config=TeX-MML-AM_CHTML"
</script>

<script src = "{{ asset('assets/quill/quill2.0.3.js')}}" ></script>
<link href="{{ asset('assets/quill/quill2.0.3.snow.css')}}" rel="stylesheet">

<script type="module">

function b64EncodeUnicode(str) {
	//this function is shamelessly copied from: https://developer.mozilla.org/en/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
}
$(document).ready(function(){
	var file_name ="";

    let is_dirty = false;


    const quill = new Quill('#editor', {
        modules: {
            // syntax: true,
            table: true,
            toolbar: [
  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
  ['blockquote', 'code-block'],
  ['link', 'image', 'video', 'formula'],
  ['table'],
  [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
  [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
//   [{ 'direction': 'rtl' }],                         // text direction

  [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
  [{ 'font': [] }],
  [{ 'align': [] }],

  ['clean']                                         // remove formatting button
],
        },
        theme: 'snow'
    });

    // document.querySelector
	document.querySelector("#opendialog").addEventListener("change",function(e){
		console.log(this.files);

		file_name = this.files[0].name
		
		if (this.files && this.files[0]){
			var reader = new FileReader();

			reader.onload = function (e) {
                console.log(e.target);
				ckeditor.setData(e.target.result);
			}
			
			reader.readAsText(this.files[0]);
		}
	});
	
	document.querySelector("#open").onclick = function(){
		document.querySelector("#opendialog").click();
	};

    //FOR AUTOSAVING

    const pendingActions = ckeditor.plugins.get( 'PendingActions' );


    // Listen to new changes (to enable the "Save" button) and to
    // pending actions (to show the spinner animation when the editor is busy).
    // function handleStatusChanges( editor ) {
    ckeditor.plugins.get( 'PendingActions' ).on( 'change:hasAny', () => updateStatus( ckeditor ) );

    ckeditor.model.document.on( 'change:data', () => {
        is_dirty = true;

        updateStatus( ckeditor );
    } );
    // }

    // If the user tries to leave the page before the data is saved, ask
    // them whether they are sure they want to proceed.
    // function handleBeforeunload( editor ) {
    //     const pendingActions = editor.plugins.get( 'PendingActions' );

    window.addEventListener( 'beforeunload', evt => {
        if ( pendingActions.hasAny ) {
            evt.preventDefault();
        }
    } );
    // }

    function updateStatus( editor ) {
        const saveButton = document.querySelector( '.save-button' );

        // Disables the "Save" button when the data on the server is up to date.
        if ( is_dirty ) {
            saveButton.classList.remove( 'disabled' );
        } else {
            saveButton.classList.add( 'disabled' );
        }

        // Shows the spinner animation.
        if ( editor.plugins.get( 'PendingActions' ).hasAny ) {
            saveButton.classList.add( 'btn-lg' );
        } else {
            saveButton.classList.remove( 'btn-lg' );
        }
    }

	
	document.querySelector("#save").onmouseover = (function(){
		document.querySelector("#save > a").href = "data:text/html;base64," + b64EncodeUnicode( ckeditor.getData() ) ;
		document.querySelector("#save > a").download =  file_name ;
	});
    document.querySelector('.save-button').onclick = (function(){
        const action = pendingActions.add( 'Saving changes' );
        const data = ckeditor.getData();
        $.ajax({
            type: 'POST',
            url: '{{ route('htmleditor.autosave') }}',
            data: {
                '_token': "{{ csrf_token() }}",
                'content' : data
            },
            success: function (response) {
                if (response == "success"){
                    $.notify('Change sucessfully saved'
                        , {position: 'bottom right', className: 'success', autoHideDelay: 3500});
                    $('.save-button').removeClass('btn-info').addClass('btn-secondary');
                }
                pendingActions.remove( action );
                if ( data == ckeditor.getData() ) {
                    is_dirty = false;
                }
                updateStatus( ckeditor );
            
            },
            error: function(response){
                $.notify('Error while saving'
                    , {position: 'bottom right', className: 'error', autoHideDelay: 3500});
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
            <a class="btn btn-primary" href="#" id="open"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Open</a>
        </div>
        
        <div class="col-auto me-auto ms-auto">		
            <a class="btn btn-secondary save-button">
                Save draft (will autosave every 3 minutes)
            </a>
        </div>
        
        <div id="save" class="col-auto">
            <a class="btn btn-primary"  download href="#"><i class="fa fa-download" aria-hidden="true"></i>Download </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="toolbar_wrapper" id="toolbar" >
           
        </div>
    </div>
    <div class="row">
        <div class="edit_wrapper" id="editor" >
            {!! $content !!}
        </div>
    </div>
</div>
@endsection