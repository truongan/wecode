@extends('layouts.app')
@php($selected="settings")
@section('head_title','View User')
@section('icon', 'fas fa-users')

@section('title', ' Set multiple users trial time')

@section('title_menu')
	<a class="ms-3 fs-6 link-dark" href="{{route('users.index') }}"><i class="fas fa-list    "></i>Users list</a>
@endsection


@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="post" name="main_form" class="row g-3">
@csrf
    <div class="col-sm-6 form-floating">
        <textarea name="names" id="names" class="form-control" style="height:15em;"
            placeholder="Paste username list here" aria-describedby="help_names"
            value="">{{ old('names') }}</textarea>
        <label for="names">Username lists</label>
        <small id="help_names" class="text-muted">A list of usernames, sperarated by white space to set their trial time
            to.</small>
    </div>
    <div class="col-sm-6">
        <div class="form-check">
            <input value = 'new_time' class="form-check-input" type="radio" name="set_choice" id="set_choice1" >
            <label class="form-check-label" for="set_choice1">
                Set equal trial time for all users
            </label>
        </div>
        <div class="form-check">
            <input value = 'new_end' class="form-check-input" type="radio" name="set_choice" id="set_choice2" checked>
            <label class="form-check-label" for="set_choice2">
                Set all users's trial time to end on the same date.
            </label>
        </div>
        <div class="form-floating my-2">
            <input type="number" class="form-control" name="new_trial_time" id="floatingInput"
                placeholder="name@example.com">
            <label for="floatingInput">New trial time</label>
        </div>
		<div class="form-floating ">
			<input type="datetime-local" class="form-control" name="new_trial_end_time" id="floatingInput"
				placeholder="name@example.com">
			<label for="floatingInput">New trial end time</label>
		</div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@if (Session::has('success'))
    <div class="alert alert-success mt-3">
        Updated {{  session('success')}} user(s)
    </div>
@endif
@endsection

@section('body_end')
<script>
	document.main_form.set_choice.forEach(radio => radio.addEventListener('change', function () {
		document.main_form.new_trial_end_time.disabled = true;
		document.main_form.new_trial_time.disabled = true;
		if (document.main_form.set_choice.value == 'new_time') {
			document.main_form.new_trial_time.disabled = false;
		} else {
			document.main_form.new_trial_end_time.disabled = false;
		}
	}));
	document.main_form.set_choice[0].dispatchEvent(new Event('change'));
</script>
@endsection	