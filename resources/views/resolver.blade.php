@php($selected = 'resolver')
@extends('layouts.app')
@section('icon', 'fas fa-snowflake')
@section('head_title', 'Resolver')
@section('title', 'Resolver')

@section('other_assets')
    <link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css' />
    <script>
        if (!!window.performance && window.performance.navigation.type === 2) {
            window.location.reload();
        }
    </script>
    <style>
        .user_row {
			transform: translateY(0);
			transition: transform 1s linear;
        }

        td {
            transition: all 1s linear;
        }
    </style>
@endsection

@section('title_menu')

    @php($sl = 0)
    @if (isset(Auth::user()->selected_assignment_id))
        @php($sl = 1)
    @endif
@endsection


@section('content')
    <div class="mx-n2">
        <h1>resolver</h1>

		<button id="reverse-btn" class="btn btn-secondary"> << Go back</button>
		<button id="resolve-btn" class="btn btn-secondary">Resolve >> </button>

        {{-- TABLE --}}
        <table id="wecode_leaderboard" class="wecode_table table table-striped table-bordered table-sm">
            <thead class="thead-old table-dark">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Score</th>
                    @foreach ($problem_id as $ordering => $id)
                        <th>
							{{ chr ($ordering + 65) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
			<tbody></tbody>
        </table> 

        {{-- TABLE --}}
    </div>
@endsection

@section('body_end')
<script>
	// Variables
	const php_problem_id = @json($problem_id)
	// Need to enter here
	const php_accepted_time = @json($accepted_time)
	// Need to enter here
	const php_accepted = @json($accepted)
	// Need to enter here
	const php_tries = @json($tries)
	// Need to enter here
	const php_data = @json($data)
	// Need to enter here

	// Get user list
	let users_list = []
	for (let i = 0; i < php_data['username'].length; i++) {
		const user = {
			username: php_data['username'][i],
			total_accepted: php_data['accepted'][i],
			total_accepted_time: php_data['accepted_time'][i],
		}

		user['accepted_time'] = php_accepted_time[user.username]
		user['accepted'] = php_accepted[user.username]
		user['tries_before'] = php_tries[user.username]['tries_before']
		user['tries_during'] = php_tries[user.username]['tries_during']

		users_list.push(user)
	}
	// console.log(users_list)
	// console.log(accepted_time)
	

	// Generate row from user list
	const generateUserResultCell = (user, php_problem_id) => {
		return php_problem_id.map((problem_id) => {
			// console.log(user)
			// console.log(user.tries_during)

			if (user.tries_during[problem_id] > 0) {
				return ('<td class="bg-warning">' + user.tries_before[problem_id] + '+' + user.tries_during[problem_id] + ' tries' + '</td>')
			} else {
				if (user.tries_before[problem_id] == 0) {
					return ('<td>' + '-' + '</td>')
				} else if (user.accepted_time[problem_id] == 0) {
					return ('<td class="bg-danger">' + user.accepted_time[problem_id] + '</td>')
				} else {
					return ('<td class="bg-success">' + user.accepted_time[problem_id] + '</td>')
				}
			}
		})
	}

	for (let i = 0; i < users_list.length; i++) {
		const row = $(
			'<tr class="user_row">' +
				'<td class="rank">' + (i + 1) + '</td>' +
				'<td class="name">' + users_list[i].username + '</td>' +
				'<td class="score">' + users_list[i].total_accepted_time + '</td>' +
				(generateUserResultCell(users_list[i], php_problem_id)) +
			'</tr>'
		)
		users_list[i].html_row = row
		$("#wecode_leaderboard > tbody").append(row)
		
	}
	// console.log(users_list)

	function resolve() {
		// Get last user with tries
		let last_user = null;
        let last_user_row_index = 0;
		for (let i = users_list.length - 1; i >= 0; i--) {
			const total_tries_during = Object.values(users_list[i].tries_during).reduce((prev, curr) => prev + curr)
			if (total_tries_during > 0) {
				last_user = users_list[i];
                last_user_row_index = i;
				break;
			}
		}
		// console.log(last_user)

		// Choose a problem to resolve
		let prob_id = 0;
		for (let key in last_user.tries_during) {
			if (last_user.tries_during[key] > 0) {
				prob_id = key
				break;
			}
		}
		// console.log(prob_id)

		// Update user
		const prob_ordering = Number(Object.keys(php_problem_id).find(key => php_problem_id[key] == prob_id)) + 3
		const score = last_user['accepted_time'][prob_id]
        // console.log(last_user['accepted_time'][prob_id])
		// console.log(last_user.html_row.find("td")[prob_ordering])

        users_list[last_user_row_index].tries_during[prob_id] = 0;
        users_list[last_user_row_index].total_accepted += 1;
        users_list[last_user_row_index].total_accepted_time += score;

		if (score) {
			last_user.html_row.find("td")[prob_ordering].textContent = score
            last_user.html_row.find("td")[prob_ordering].classList.remove("bg-warning")
            last_user.html_row.find("td")[prob_ordering].classList.add("bg-success")

            last_user.html_row.find("td")[3].textContent = users_list[last_user_row_index].total_accepted_time
			// last_user.html_row.find("td")[prob_ordering].textContent = score


		} else {
			last_user.html_row.find("td")[prob_ordering].textContent = 0
            last_user.html_row.find("td")[prob_ordering].classList.remove("bg-warning")
            last_user.html_row.find("td")[prob_ordering].classList.add("bg-danger")

		}

        console.log(users_list[last_user_row_index])


        // Sort the user list
        users_list.sort(function (prev, curr) {
            const accepted_order_ASC = Number(prev.total_accepted) - Number(curr.total_accepted)
            const accepted_time_order_ASC = Number(prev.total_accepted_time) - Number(curr.total_accepted_time)
            return -accepted_order_ASC || accepted_time_order_ASC
        })

        // Update rank
		for(let i = 0; i < users_list.length; i++) {
			users_list[i].html_row.find(".rank").textContent = i + 1	
		}

        console.log(users_list)

		// Reposition
		// const row_height = last_user.html_row.outerHeight()
		// let row_height = 0
		// for (let i = 0; i < users_list.length; i++) {
		// 	users_list[i].html_row.css("top", row_height + "px")
		// 	row_height += header_height
		// }
	}

	$("#resolve-btn").click(resolve)

</script>
@endsection
