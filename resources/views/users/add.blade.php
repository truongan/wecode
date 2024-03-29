@extends('layouts.app')
@php($selected="settings")
@section('head_title','New Users')
@section('icon', 'fas fa-user-plus')

@section('title', ' New Users')

@section('title_menu')
    {{-- Nếu là admin thì hiển thị --}}

    <span class="ms-4 fs-6"><a href="{{ route('users.index') }}" ><i class="fa fa-list-alt color6"></i> Users list</a></span>
    <span class="ms-4 fs-6"><a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/users.md#add-users" target="_blank"><i class="fa fa-question-circle color6"></i> Help</a></span>
@endsection

@section('body_end')
<script> 
	document.querySelector("#add_users_button").addEventListener('click',function(){
		document.querySelector('#loading').style.display = 'inline';

		fetch(
			"{{ route('users.add') }}",
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: 	JSON.stringify({
					'_token':  "{{ csrf_token() }}",
					'send_mail':  (document.querySelector("#send_mail").checked ?1:0),
					'delay': document.querySelector("#delay").value,
					'new_users':document.querySelector("#new_users").value,
				})
			}
		)
		.then(response => {
			if (response.status == 200) response.text().then(
				data => document.getElementById('main_content').innerHTML = data	
			);
			else response.text().then(data => {
				document.querySelector('#main_content').innerHTML = "<div class='col-12'><iframe style='width:100%; height:80vh'></iframe></div>";
				frame = document.querySelector('iframe');
				frame.contentDocument.write(data);
			});
		});

	});
</script>
@endsection

@section('content')
<div class="col-12">
    <p>You can use this field to add multiple users at the same time.</p>
    <ul>
        <li>Usernames may contain lowercase letters or numbers and must be between 3 and 20 characters in length.</li>
        <li>Passwords must be between 6 and 30 characters in length.</li>
        <li>If you want to send passwords by email, do not add too many users at one time. This may result in mail delivery fail.</li>
    </ul>
    <div class="form row">
        <label for="send_email" class="me-3"><input type="checkbox" name="send_mail" id="send_mail" />Send usernames and passwords by email</label> 
        <label for="delay"> (Waits <input type="number" size="5" name="delay" id="delay" class="form-control" style="width:5rem;" value="2"/> second(s) before sending each email, so please be patient).</label>
    </div>
</div>

<div class="form-group col-12">
    <textarea name="new_users" id="new_users" rows="15" class="form-control add_text">
# Lines starting with a # sign are comments.
	# Each line (except comments) represents a user.
# The syntax of each line is:
#
# USERNAME, EMAIL, PASSWORD, ROLE, DISPLAY_NAME
#
# Roles: admin head_instructor instructor student
# You can use RANDOM[n] for password to generate random n-digit password.
</textarea>
</div>
<meta name="csrf-token" content="{!! Session::token() !!}">
<div class="form-group col-12">
    <input type="submit" class="btn btn-primary" id="add_users_button" value="Add Users"/>
    <span id="loading" style="display: none;"><img src="{{ asset('images/loading.gif') }}" /> Adding users... Please wait</span>
</div>
@endsection