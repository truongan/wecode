<form action="{!! route('problem_controller.add_problem') !!}" enctype=“multipart/form-data” method=”post”>
    <input type=“file” name=“zipFile” accept=“zip/*”>
    <input type=“submit” name=“Submit” value=“Submit”>
</form>