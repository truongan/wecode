@php($selected = 'resolver')
@extends('layouts.resolver')
@section('icon', 'fas fa-trophy')
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
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }
        td {
            transition: all 2s linear;
            font-size: 1rem;
            white-space: pre-wrap;
            /* border-bottom: solid 1px #acacac; */
        }

        .score {
            width: 4rem;
        }

        .score.wrong {
            background: #e87272;
        }

        .score.correct {
            background: #60e760;
        }

        .score.pending {
            background: #6666FF;
        }

        #scoreboard {
            margin: 0 auto;
            text-align: center;
            border-collapse: collapse;
            width: 100%;
        }

        #scoreboard thead {
            height: 3rem;
            border-bottom: 1px solid black;
            /* z-index: 1; */
            /* background-color: grey;
            color: white; */
            /* border-image: linear-gradient(transparent 10%, blue 10% 90%, transparent 90%) 0 0 0 1 / 3px; */
            /* border-width: 2px; */
        }

        th {
            box-shadow: -1px 0 0 0 silver inset, 0 2px 0 0 black;
        }

        .solved {
            width: 3rem;
            font-weight: bold;
        }

        .total {
            width: 4rem;
            font-size: 1rem;
        }

        .logo {
            width: 2rem;
        }

        .name {
            width: 30rem;
            text-align: right;
        }

        .name > p {
            margin: 0;
            margin-top: 4px;
            font-size: 1rem;
            font-weight: bold;
        }

        .name > .school_name {
            margin: 0;
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: normal;
            font-style: italic;
        }

        .rank {
            width: 3rem;
        }

        .prob_tries {
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .user_row {
            /* transform: translateY(0px); */
            transition: transform 2s linear;
            height: 8px;
            /* border-bottom: 1px solid black; */
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
    <div class="mx-n2" style="overflow: auto">
        <h1 style="text-align: center; margin: 1rem; font-weight: bold; ">{{ $assignment->name }}</h1>
        <p style="text-align: center; margin-bottom: 1rem">{{ $assignment->start_time->format('Y-m-d') }}</p>
        <p style="text-align: center; margin-bottom: 1rem">Start: {{ $assignment->start_time->format('H:i:s') }} - End: {{ $assignment->finish_time->format('H:i:s') }}</p>

        {{-- <button id="reverse-btn" class="btn btn-secondary"><< Go back</button>
        <button id="resolve-btn" class="btn btn-secondary">Resolve >> </button>
        <button id="auto-resolve-btn" class="btn btn-secondary">Auto Resolve >> </button> --}}

        <i>Click right arrow to resolve</i><br>
        <i>Click 1 to auto resolve for users whose rank is below 30</i>

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
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" colspan="2">USER</th>
                    <th scope="col" colspan="2">SCORE</th>
                    @foreach ($problem_id as $ordering => $id)
                        <th scope="col">
                            {{ chr($ordering + 65) }}
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
            // Count number of click (aka number of tries of all users whose rank is below break_rank)
            Object.values(php_tries[user.username]['tries_during']).map((tries_of_this_prob) => {
                if (tries_of_this_prob) num_of_autoclick++
            })
        }
    }
        console.log(num_of_autoclick)
        // console.log(users_list)
        // console.log(accepted_time)


        // Generate row from user list
        const generateUserResultCell = (user, php_problem_id) => {
            return php_problem_id.map((problem_id) => {
                // console.log(user)
                // console.log(user.tries_during)

                const total_tries_of_prob = Number(user.tries_before[problem_id]) + Number(user.tries_during[problem_id]);
                if (user.tries_during[problem_id] > 0) {
                    return ('<td class="score pending"> <p class="prob_tries">' + 
                        user.tries_before[problem_id] + '+' + user.tries_during[problem_id] + 
                        (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></td>')

                } else {
                    if (user.tries_before[problem_id] == 0) {
                        return ('"<td class="score"></td>')

                    } else if (user.accepted_time[problem_id] == 0) {
                        return ('<td class="score wrong"> <p class="prob_tries">' + 
                            user.tries_before[problem_id] + 
                            (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></td>')

                    } else {
                        return ('<td class="score correct">' +
                            user.accepted_time[problem_id] + 
                            '<p class="prob_tries">' + 
                            user.tries_before[problem_id] + 
                            (total_tries_of_prob == 1 ? ' try' : ' tries') + '</p></td>')
                    }
                }
            })
        }

        for (let i = 0; i < users_list.length; i++) {
            const row = $(
                '<tr class="user_row">' +
                '<td class="rank">' + (i + 1) + '</td>' +
            //    '<td class="logo"><img src="http://wecode.test/images/UCPC_iuh.png" height="20px"></td>' +
                //  '<td class="logo"><img src="' + users_list[i].image + '" height="20px"></td>' +
                '<td class="logo"><img src="' + users_list[i].image + '" height="20px"></td>' +



                '<td class="name"><p>' + users_list[i].display_name + '</p><p class="school_name">' + users_list[i].school_name + '</p></td>'
 +
                '<td class="solved">' + users_list[i].total_accepted + '</td>' +
                '<td class="total">' + users_list[i].total_accepted_time + '</td>' +
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
            const prob_ordering = Number(Object.keys(php_problem_id).find(key => php_problem_id[key] == prob_id)) + 5
            const score = last_user['accepted_time'][prob_id]
            const total_tries_of_prob = Number(last_user['tries_before'][prob_id]) + Number(last_user['tries_during'][prob_id])
            // console.log(total_tries_of_prob)
            // console.log(last_user['accepted_time'][prob_id])
            // console.log(last_user.html_row.find("td")[prob_ordering])

            users_list[last_user_row_index].tries_during[prob_id] = 0;
            
            if (score) {
                users_list[last_user_row_index].total_accepted += 1;
                users_list[last_user_row_index].total_accepted_time += score;
                last_user.html_row.find("td")[prob_ordering].textContent = score
                last_user.html_row.find("td")[prob_ordering].classList.remove("pending")
                last_user.html_row.find("td")[prob_ordering].classList.add("correct")
                
                last_user.html_row.find(".solved")[0].textContent = users_list[last_user_row_index].total_accepted
                last_user.html_row.find(".total")[0].textContent = users_list[last_user_row_index].total_accepted_time

                // last_user.html_row.find("td")[prob_ordering].textContent = score
                const span_tries_display = $('<p class="prob_tries">' + total_tries_of_prob + ' ' + (total_tries_of_prob == 1 ? 'try' : 'tries') + '</p>')
                // console.log(span_tries_display)
                last_user.html_row.find("td")[prob_ordering].append(span_tries_display[0])
                
                
            } else {
                last_user.html_row.find("td")[prob_ordering].textContent = ' '
                last_user.html_row.find("td")[prob_ordering].classList.remove("pending")
                last_user.html_row.find("td")[prob_ordering].classList.add("wrong")

                
                const span_tries_display = $('<p class="prob_tries">' + total_tries_of_prob + ' ' + (total_tries_of_prob == 1 ? 'try' : 'tries') + '</p>')
                // console.log(span_tries_display)
                last_user.html_row.find("td")[prob_ordering].append(span_tries_display[0])

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
                const transformY = users_list[i].transformY(i, 53)
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
                for (let i = 0; i<= num_of_autoclick; i++) {
                    resolve()
                }
            }
        })


        </script>
@endsection
