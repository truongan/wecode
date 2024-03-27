@php($selected = 'resolver')
@extends('layouts.resolver')
@section('icon', 'fas fa-trophy')
@section('head_title', 'Resolver')
@section('title', 'Resolver')

@section('title_menu')
<nav>
    <a class="text-secondary" href="{{ route('assignments.show', [$assignment->id, 0]) }}" style="text-decoration: none;"><i class="fas fa-clipboard-list fa-fw"></i>
        <small> Problemset</small>
    </a>
</nav>
@endsection

@section('contest_time')
<i class="fas fa-clock fa-fw fa-spin text-secondary"></i>
<small>
    <span class="text-secondary">{{ $assignment->start_time->format('d-m-Y') }} | Start: {{ $assignment->start_time->format('H:i:s') }} - End: {{ $assignment->finish_time->format('H:i:s') }}</span>
</small>
@endsection

@section('other_assets')
    <link rel='stylesheet' type='text/css' href='{{ asset('assets/DataTables/datatables.min.css') }}' />
    <script>
        if (!!window.performance && window.performance.navigation.type === 2) {
            window.location.reload();
        }
    </script>
    <style>
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }

        #scoreboard {
            margin: 0 auto;
            text-align: center;
            border-collapse: collapse;
        }

        .scoreheader {
            font-variant: small-caps;
            white-space: nowrap;
        }

        .scoreheader th {
            text-align: center;
            box-shadow: -1px 0 0 0 silver inset, 0 1px 0 0 black;
            border: none;
            position: sticky;
            top: 0px;
            z-index: 1000;
            font-size: small;
            background-color: white;
        }

        #scoreboard tr {
            border-bottom: 1px solid black;
            height: 42px;
        }

        #scoreboard tbody {
            border-top: 2px solid black;
        }

        #scoreboard .user_row {
            transition: transform 1.5s linear;
        }

        #scoreboard td {
            border-right: 1px solid silver;
            padding: 0px;
            font-size: small;
            vertical-align: middle;
            text-align: center;
        }

        #scoreboard .logo {
            white-space: nowrap;
            border: 0;
            text-align: center;
        }

        #scoreboard .logo_img {
            vertical-align: middle;
            width: 40px;
            padding: 5px;
        }

        #scoreboard .name {
            padding: 0px 5px 0px;
            text-align: right;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #scoreboard p {
            margin: 0;
        }

        #scoreboard div {
            margin: 0px 1px 0px 1px;
        }

        #scoreboard .name .school_name {
            font-size: 80%;
            font-weight: normal;
            color: dimgrey;
        }

        #scoreboard .solved {
            border-right: 0;
            font-weight: bold;
        }

        #scoreboard .solved div, #scoreboard .total div {
            padding: 0px 5px 0px 5px;
        }
        
        #scoreboard td.score_cell {
            min-width: 4.4em;
            border-right: none;
            white-space: pre-wrap;
        }

        #scoreboard .score {
            width: 4.2em;
            font-size: 120%;
            transition: all 1s linear;
        }

        #scoreboard .prob_tries {
            font-weight: normal;
            font-size: 70%;
        }

        #scoreboard .problem-badge {
            background: #0a58ca;
            font-size: 100%;
        }

        #scoreboard .score_wrong {
            background: #e87272;
        }

        #scoreboard .score_correct {
            background: #60e760;
        }

        #scoreboard .score_pending {
            background: #6666FF;
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
    {{-- TABLE --}}
    <table id="scoreboard">
        <colgroup>
            <col id="score_rank">
            <col id="score_logo">
            <col id="score_username">
        </colgroup>
        <colgroup>
            <col id="score_solved">
            <col id="score_total">
        </colgroup>
        <colgroup>
            @foreach ($problem_id as $ordering => $id)
                <col id="score_prob"></col>
            @endforeach
        </colgroup>
        <thead>
            <tr class="scoreheader">
                <th title="rank" scope="col">RANK</th>
                <th title="team name" scope="col" colspan="2">TEAM</th>
                <th title="solved / penalty time" scope="col" colspan="2">SCORE</th>
                @foreach ($problem_id as $ordering => $id)
                    <th scope="col">
                        <a href="{{ route('problems.show', ['problem' => $id]) }}" target="_blank">
                            <span class="badge problem-badge">
                                {{ chr($ordering + 65) }}
                            </span>
                        </a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    {{-- TABLE --}}
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

        const break_rank = 30;
        let num_of_autoclick = 0;

        // Get user list
        let users_list = []
        for (let i = 0; i < php_data['username'].length; i++) {
            const user = {
                username: php_data['username'][i],
                total_accepted: php_data['accepted'][i],
                total_accepted_time: php_data['accepted_time'][i],
                init_rank: i,
                school_name : php_data['school_name'][i],
                image : php_data['image'][i],
                display_name : php_data['display_name'][i]
                // Name_school: php_data['Name_school'][i], // Thêm thuộc tính 'Name_school' vào đối tượng người dùng
            }

            user['accepted_time'] = php_accepted_time[user.username]
            user['accepted'] = php_accepted[user.username]
            user['tries_before'] = php_tries[user.username]['tries_before']
            user['tries_during'] = php_tries[user.username]['tries_during']
            user['transformY'] = function (new_rank, row_height) {
                return -((this.init_rank - new_rank)*row_height)
            }

            users_list.push(user)
            
            if (i > break_rank-1) {
                // Count number of click (est from the initial rank, not exactly resolve whose rank is below 30)
                Object.values(php_tries[user.username]['tries_during']).map((tries_of_this_prob) => {
                    if (tries_of_this_prob) num_of_autoclick++
                })
            }
        }
        // console.log(num_of_autoclick)
        // console.log(users_list)
        // console.log(accepted_time)


        // Generate row from user list
        const generateUserResultCell = (user, php_problem_id) => {
            return php_problem_id.map((problem_id) => {
                // console.log(user)
                // console.log(user.tries_during)

                const total_tries_of_prob = Number(user.tries_before[problem_id]) + Number(user.tries_during[problem_id]);
                if (user.tries_during[problem_id] > 0) {
                    return ('<td class="score_cell"><div class="score score_pending">&nbsp;<p class="prob_tries">' + 
                        user.tries_before[problem_id] + '+' + user.tries_during[problem_id] + 
                        (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></div></td>')

                } else {
                    if (user.tries_before[problem_id] == 0) {
                        return ('"<td class="score_cell"><div class="score"></div></td>')

                    } else if (user.accepted_time[problem_id] == 0) {
                        return ('<td class="score_cell"><div class="score score_wrong">&nbsp;<p class="prob_tries">' + 
                            user.tries_before[problem_id] + 
                            (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></div></td>')

                    } else {
                        return ('<td class="score_cell"><div class="score score_correct">' +
                            user.accepted_time[problem_id] + 
                            '<p class="prob_tries">' + 
                            user.tries_before[problem_id] + 
                            (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></div></td>')
                    }
                }
            })
        }

        for (let i = 0; i < users_list.length; i++) {
            const row = $(
                '<tr class="user_row">' +
                '<td class="rank">' + (i + 1) + '</td>' +
                '<td class="logo"><img class="logo_img" src="http://wecode.test/images/logo_uit.png"></td>' +
                // '<td class="logo"><img class="logo_img" src="' + users_list[i].image + '"></td>' +
                '<td class="name"><div><p class="display_name">' + users_list[i].display_name + '</p><p class="school_name">' + users_list[i].school_name + '</p></div></td>' +
                '<td class="solved"><div>' + users_list[i].total_accepted + '</div></td>' +
                '<td class="total"><div>' + users_list[i].total_accepted_time + '</div></td>' +
                (generateUserResultCell(users_list[i], php_problem_id)) +
                '</tr>'
            )
            users_list[i].html_row = row
            $("#scoreboard > tbody").append(row)

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

            if (!last_user) return;

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
            const prob_ordering = Number(Object.keys(php_problem_id).find(key => php_problem_id[key] == prob_id))
            const score = last_user['accepted_time'][prob_id]
            const total_tries_of_prob = Number(last_user['tries_before'][prob_id]) + Number(last_user['tries_during'][prob_id])
            // console.log(total_tries_of_prob)
            // console.log(last_user['accepted_time'][prob_id])
            // console.log(last_user.html_row.find(".score")[prob_ordering])

            users_list[last_user_row_index].tries_during[prob_id] = 0;
            
            if (score) {
                users_list[last_user_row_index].total_accepted += 1;
                users_list[last_user_row_index].total_accepted_time += score;
                last_user.html_row.find(".score")[prob_ordering].textContent = score
                last_user.html_row.find(".score")[prob_ordering].classList.remove("score_pending")
                last_user.html_row.find(".score")[prob_ordering].classList.add("score_correct")
                
                last_user.html_row.find(".solved")[0].textContent = users_list[last_user_row_index].total_accepted
                last_user.html_row.find(".total")[0].textContent = users_list[last_user_row_index].total_accepted_time

                // last_user.html_row.find(".score")[prob_ordering].textContent = score
                const span_tries_display = $('<p class="prob_tries">' + total_tries_of_prob + ' ' + (total_tries_of_prob == 1 ? 'try' : 'tries') + '</p>')
                // console.log(span_tries_display)
                last_user.html_row.find(".score")[prob_ordering].append(span_tries_display[0])
                
                
            } else {
                last_user.html_row.find(".score")[prob_ordering].textContent = ' '
                last_user.html_row.find(".score")[prob_ordering].classList.remove("score_pending")
                last_user.html_row.find(".score")[prob_ordering].classList.add("score_wrong")

                
                const span_tries_display = $('<p class="prob_tries">' + total_tries_of_prob + ' ' + (total_tries_of_prob == 1 ? 'try' : 'tries') + '</p>')
                // console.log(span_tries_display)
                last_user.html_row.find(".score")[prob_ordering].append(span_tries_display[0])

            }
            
            // console.log(users_list[last_user_row_index])

            // Sort the user list
            users_list.sort(function(prev, curr) {
                const accepted_order_ASC = Number(prev.total_accepted) - Number(curr.total_accepted)
                const accepted_time_order_ASC = Number(prev.total_accepted_time) - Number(curr.total_accepted_time)
                return -accepted_order_ASC || accepted_time_order_ASC
            })
            
            // Update rank
            for (let i = 0; i < users_list.length; i++) {
                users_list[i].html_row.find(".rank")[0].textContent = i + 1
            }
            
            // console.log(php_data)
            
            // console.log(users_list)

            // Reposition
            for (let i = 0; i < users_list.length; i++) {
                const transformY = users_list[i].transformY(i, 42)
                // console.log(transformY)
                users_list[i].html_row.css('transform', 'translateY(' + transformY + 'px)')
                // console.log(users_list[i].html_row)
            }

            // console.log(users_list)

            // users_list[last_user_row_index].tries_during[prob_id] = 0;
        }
        
        // $("#resolve-btn").click(resolve)

        // $("#auto-resolve-btn").click(() => {
        //     for (let i = 0; i<= num_of_autoclick; i++) {
        //         $("#resolve-btn").click()
        //     }
        // })

        $(document).on('keydown', function(e){
            if (e.key == "ArrowRight")
                resolve()
            else if (e.key == "1") {
                for (let i = 0; i <= num_of_autoclick; i++) {
                    resolve()
                }
            }
        })


        </script>
@endsection
