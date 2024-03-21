@extends('layouts.app')
@php($selected = 'settings')
@section('head_title', 'Delete Users')
@section('icon', 'fas fa-trash')
@section('title', 'Delete Users')
@section('head')
    <meta name="csrf-token" content="{!! Session::token() !!}">
@endsection

@section('title_menu')
    <span class="ms-4 fs-6"><a href="{{ route('users.index') }}"><i class="fa fa-list-alt color6"></i> Users list</a></span>
@endsection

@section('body_end')
    <script>
        document.querySelector("#delete_btn").addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('#loading').style.display = 'inline';

            fetch(
                    "{{ route('users.delete') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            '_token': "{{ csrf_token() }}",
                            'usernames': document.querySelector("#usernames").value,
                        })
                    }
                )
                .then(response => {
                    if (response.status == 200) response.text().then(
                        data => document.getElementById('main_content').innerHTML = data
                    );
                    else response.text().then(data => {
                        document.querySelector('#main_content').innerHTML =
                            "<div class='col-12'><iframe style='width:100%; height:80vh'></iframe></div>";
                        frame = document.querySelector('iframe');
                        frame.contentDocument.write(data);
                    });
                });

        });
    </script>
@endsection

@section('content')
    <p>You can use this field to delete multiple users at the same time.</p>
    <ul>
        <li>Copy list of usernames from the User list page.</li>
        <li>Paste it down the below text field.</li>
    </ul>
    <form>
        <textarea name='usernames' id="usernames" rows="15" class="form-control add_text"></textarea>

        <div>
            <button type="submit" class="btn btn-danger" id="delete_btn">Delete users</button>
            <span id="loading" style="display: none;"><img src="{{ asset('images/loading.gif') }}" /> Deleting users...
                Please wait</span>
        </div>
    </form>

@endsection
