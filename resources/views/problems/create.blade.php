<form action="{!! route('problems.store') !!}" enctype=“multipart/form-data” method=”post” >
    <input type = "hidden" name ="_token" value="{!! csrf_token() !!}"/> 
    <input id="form_tests_zip" type="file" name="tests_zip" class="custom-file-input" />
    <button class = "btn btn-primary" type=“submit” name=“Submit” > Submit</button>
</form>
