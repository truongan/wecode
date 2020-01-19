<?php

use Illuminate\Database\Seeder;

class installation_seeding extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('settings')->insert(
            array(
                array('key' => 'site_name',               'value' => '_sitenametobereplace_'),
                array('key' => 'timezone',               'value' => 'Asia/Ho_Chi_Minh'),
                array('key' => 'tester_path',            'value' => base_path() . '/tester'),
                array('key' => 'assignments_root',       'value' => base_path() . '/assignments'),
                array('key' => 'file_size_limit',        'value' => '50'),
                array('key' => 'output_size_limit',      'value' => '1024'),
                array('key' => 'queue_is_working',       'value' => '0'),
                array('key' => 'default_late_rule',      'value' => "/* \n * Put coefficient (from 100) in variable \$coefficient.\n * You can use variables \$extra_time and \$delay.\n * \$extra_time is the total extra time given to users\n * (in seconds) and \$delay is number of seconds passed\n * from finish time (can be negative).\n *  In this example, \$extra_time is 172800 (2 days):\n */\n\nif (\$delay<=0)\n  // no delay\n  \$coefficient = 100;\n\nelseif (\$delay<=3600)\n  // delay less than 1 hour\n  \$coefficient = ceil(100-((30*\$delay)/3600));\n\nelseif (\$delay<=86400)\n  // delay more than 1 hour and less than 1 day\n  \$coefficient = 70;\n\nelseif ((\$delay-86400)<=3600)\n  // delay less than 1 hour in second day\n  \$coefficient = ceil(70-((20*(\$delay-86400))/3600));\n\nelseif ((\$delay-86400)<=86400)\n  // delay more than 1 hour in second day\n  \$coefficient = 50;\n\nelseif (\$delay > \$extra_time)\n  // too late\n  \$coefficient = 0;"),
                array('key' => 'enable_c_shield',        'value' => '0'),
                array('key' => 'enable_cpp_shield',      'value' => '0'),
                array('key' => 'enable_py2_shield',      'value' => '0'),
                array('key' => 'enable_py3_shield',      'value' => '0'),
                array('key' => 'enable_java_policy',     'value' => '0'),
                array('key' => 'enable_log',             'value' => '1'),
                array('key' => 'submit_penalty',         'value' => '300'),
                array('key' => 'enable_registration',    'value' => '0'),
                array('key' => 'registration_code',      'value' => '0'),
                array('key' => 'mail_from',              'value' => 'wcj@example.com'),
                array('key' => 'mail_from_name',         'value' => 'Wecode Judge'),
                array('key' => 'reset_password_mail',    'value' => "<p>\nSomeone requested a password reset for your {SITE_NAME} Wecode Judge account at {SITE_URL}.\n</p>\n<p>\nTo change your password, visit this link:\n</p>\n<p>\n<a href=\"{RESET_LINK}\">Reset Password</a>\n</p>\n<p>\nThe link is valid for {VALID_TIME}. If you don't want to change your password, just ignore this email.\n</p>"),
                array('key' => 'add_user_mail',          'value' => "<p>\nHello! You are registered in {SITE_NAME} Wecode Judge at {SITE_URL} as {ROLE}.\n</p>\n<p>\nYour username: {USERNAME}\n</p>\n<p>\nYour password: {PASSWORD}\n</p>\n<p>\nYou can log in at <a href=\"{LOGIN_URL}\">{LOGIN_URL}</a>\n</p>"),
                array('key' => 'moss_userid',            'value' => ''),
                array('key' => 'results_per_page_all',   'value' => '40'),
                array('key' => 'results_per_page_final', 'value' => '80'),
                array('key' => 'week_start',             'value' => '1'),
                array('key' => 'theme',             'value' => 'default'),
            )
        );

        DB::table('languages')->insert(
            [
                ['id' => 1,  'extension' => 	'c', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 20, 'name' => 	'C' ],
                ['id' => 2,  'extension' => 	'cpp', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 10, 'name' => 	'C++' ],
                ['id' => 3,  'extension' => 	'java', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 30, 'name' => 	'Java' ],
                ['id' => 4,  'extension' => 	'py3', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 40, 'name' => 	'Python 3' ],
                ['id' => 5,  'extension' => 	'py2', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 50, 'name' => 	'Python 2' ],
                ['id' => 6,  'extension' => 	'pas', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 60, 'name' => 	'Free Pascal' ],
                ['id' => 7,  'extension' => 	'numpy', 'default_time_limit' =>  	500, 'default_memory_limit' => 	50000 , 'sorting' => 70, 'name' => 	'numpy-mp' ],
            ]
        );
        DB::table('roles')->insert(
            [
                ['name' =>  'admin', 'id' => 1 ],
                ['name' =>  'head_instructor', 'id' => 2 ],
                ['name' =>  'instructor', 'id' => 3 ],
                ['name' =>  'student', 'id' => 4 ],
            ]
        );
    }
}
