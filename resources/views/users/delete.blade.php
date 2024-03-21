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

@section('other_assets')
    <link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}' />
@endsection

@section('body_end')
    <script type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script>
        let selected_users = [];

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
                            'usernames': selected_users,
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

        $('.checkbox').click(function() {
            let selectedUsername = $(this).closest('tr').find('#un').text().trim();
            console.log(selectedUsername)

            if (this.classList.contains('fa-check-circle')) {
                this.classList.remove('fa-check-circle');
                this.classList.add('fa-circle');
                selected_users = selected_users.filter(user => user !== selectedUsername);
            } else {
                this.classList.remove('fa-circle');
                this.classList.add('fa-check-circle');
                selected_users.push(selectedUsername);
            }
            console.log(selected_users)
        });

        $("table").DataTable({
            "pageLength": 50,
            "lengthMenu": [
                [20, 50, 100, 200, -1],
                [20, 50, 100, 200, "All"]
            ]
        });
    </script>
@endsection

@section('content')
    <div class="mb-3">
        <button type="submit" class="btn btn-danger" id="delete_btn">Delete users</button>
        <span id="loading" style="display: none;"><img src="{{ asset('images/loading.gif') }}" /> Deleting users...
            Please wait</span>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-old table-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Username</th>
                        <th>Display Name</th>
                        <th>Email</th>
                        <th>Trial end</th>
                        <th>First Login</th>
                        <th>Last Login</th>
                    </tr>
                </thead>
                @foreach ($users as $user)
                    <tr data-id="{{ $user->id }}">
                        <td>
                            <i class="checkbox pointer far fa-circle fa-2x"></i>
                        </td>
                        <td> {{ $loop->iteration }} </td>
                        <td id="un"> {{ $user->username }} </td>
                        <td>{{ $user->display_name }}</td>
                        <td>{{ $user->email }}<br />{{ $user->role->name }}</td>
                        <td>{{ $user->trial_time ? $user->created_at->addHours($user->trial_time)->diffForHumans() : 'Permanent user' }}
                        </td>
                        <td>
                            <small>{{ $user->first_login_time? $user->first_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll'): 'Never' }}</small>
                        </td>
                        <td>
                            <small>{{ $user->last_login_time? $user->last_login_time->setTimezone($settings['timezone'])->locale('en-GB')->isoFormat('lll'): 'Never' }}
                            </small>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@endsection
