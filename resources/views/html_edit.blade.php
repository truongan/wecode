@extends('layouts.app')
@section('head_title','Edit by HTML')
@section('icon', 'fas fa-edit')

@section('title', 'Edit by HTML')

@section('body_end')
<script type="text/javascript">
    mathjax_path = "{{ asset('assets/MathJax-2.7.9') }}/MathJax.js?config=TeX-MML-AM_CHTML"
</script>

<script src="{{ asset('assets/ckeditor/ckeditor.js') }}" charset="utf-8"></script>
<script type="text/javascript">
function b64EncodeUnicode(str) {
	//this function is shamelessly copied from: https://developer.mozilla.org/en/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
}
$(document).ready(function(){
	var file_name ="";

	
	$("#opendialog").bind("change",function(e){
		console.log(this.files);

		file_name = this.files[0].name
		
		if (this.files && this.files[0]){
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#editor').html(e.target.result);
			}
			
			reader.readAsText(this.files[0]);
		}
	});
	
	$("#open").click(function(){
		$("#opendialog").click();
	});
	
	$("#save").mouseover(function(){
		$("#save > a").attr("href", "data:text/html;base64," + b64EncodeUnicode( CKEDITOR.instances.editor.getData() )) ;
		$("#save > a").attr("download", file_name) ;
		console.log($("#save > a"));
	});
    $('.save-button').click(function(){
            $.ajax({
                type: 'POST',
                url: '{{ route('htmleditor.autosave') }}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'content' : CKEDITOR.instances.editor.getData()
                },
                success: function (response) {
                    if (response == "success"){
                        $.notify('Change sucessfully saved'
                            , {position: 'bottom right', className: 'success', autoHideDelay: 3500});
                        $('.save-button').removeClass('btn-info').addClass('btn-secondary');
                    }
                },
                error: function(response){
                    $.notify('Error while saving'
                        , {position: 'bottom right', className: 'error', autoHideDelay: 3500});
                }
            });
        }); 
});

</script>
@endsection

@section('content')
<div class="d-flex flex-column">
    {{-- {% for error in errors %}
        <p class="text-danger">{{ error|raw }}</p>
    {% endfor %} --}}
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

    <div class="row">
        <div class="edit_wrapper" id="editor" contenteditable="true">
            {!! $content !!}
        </div>
    </div>
</div>
@endsection