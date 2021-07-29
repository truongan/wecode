@php($selected = 'settings')
@extends('layouts.app')
@section('head_title','Settings')
@section('icon', 'fas fa-cogs')

@section('title', 'Settings')

@section('content')

@if ($errors->any())
	<div class="alert alert-danger">Error updating settings.</div>
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div><br/>
    @endforeach
@endif


<div class="">
    <form action="{{ route('settings.update') }}" method="post">
		@csrf
		<h5>Display settings</h5><hr>
		<div class="form-old-row row">
			<fieldset class="col-md-4">
				<label for="form_site_name">Site name</label>
				<input id="form_site_name" type="text" name="site_name" class="form-control" value="{{ $site_name }}"/>
				<small class="form-text text-muted">Website's name to be display on Top Bar and HTML title.</small>
			</fieldset>

			<fieldset class="col-md-4">
				<label for="form_timezone">Timezone </label>
				<input id="form_timezone" type="text" name="timezone" class="form-control" value="{{ $timezone }}"/>
                @error('timezone')
                    <div class="alert alert-danger"> {{ $message }}</div>
                @enderror
				<small class="form-text ">
					<a target="_blank" href="http://www.php.net/manual/en/timezones.php">list of timezones</a>
					<span class="form-text text-muted timer"></span>
				</small>
			</fieldset>

			<fieldset class="col-md-2">
				<label for="form_week">Week Start Day</label>
				<select id="form_week" name="week_start" class="form-select">
					<option value="0" {{ $week_start == 0 ? 'selected="selected"' : ''}}>Sunday</option>
					<option value="1" {{ $week_start == 1 ? 'selected="selected"' : '' }}>Monday</option>
					<option value="2" {{ $week_start == 2 ? 'selected="selected"' : '' }}>Tuesday</option>
					<option value="3" {{ $week_start == 3 ? 'selected="selected"' : '' }}>Wednesday</option>
					<option value="4" {{ $week_start == 4 ? 'selected="selected"' : '' }}>Thursday</option>
					<option value="5" {{ $week_start == 5 ? 'selected="selected"' : '' }}>Friday</option>
					<option value="6" {{ $week_start == 6 ? 'selected="selected"' : '' }}>Saturday</option>
				</select>
			</fieldset>

			<fieldset class="col-md-2">
				<label for="theme">Theme</label>
				<select id="theme" name="theme" class="form-select">
					<optgroup label="light">
						<option value="cerulean" {{ $theme == 'cerulean' ? 'selected="selected"' : '' }} >cerulean</option>
						<option value="cosmo" {{ $theme == 'cosmo' ? 'selected="selected"' : '' }} >cosmo</option>
						<option value="flatly" {{ $theme == 'flatly' ? 'selected="selected"' : '' }} >flatly</option>
						<option value="journal" {{ $theme == 'journal' ? 'selected="selected"' : '' }} >journal</option>
						<option value="litera" {{ $theme == 'litera' ? 'selected="selected"' : '' }} >litera</option>
						<option value="lumen" {{ $theme == 'lumen' ? 'selected="selected"' : '' }} >lumen</option>
						<option value="lux" {{ $theme == 'lux' ? 'selected="selected"' : '' }} >lux</option>
						<option value="materia" {{ $theme == 'materia' ? 'selected="selected"' : '' }} >materia</option>
						<option value="minty" {{ $theme == 'minty' ? 'selected="selected"' : '' }} >minty</option>
						<option value="morph" {{ $theme == 'morph' ? 'selected="selected"' : '' }} >morph</option>
						<option value="pulse" {{ $theme == 'pulse' ? 'selected="selected"' : '' }} >pulse</option>
						<option value="sandstone" {{ $theme == 'sandstone' ? 'selected="selected"' : '' }} >sandstone</option>
						<option value="simplex" {{ $theme == 'simplex' ? 'selected="selected"' : '' }} >simplex</option>
						<option value="sketchy" {{ $theme == 'sketchy' ? 'selected="selected"' : '' }} >sketchy</option>
						<option value="spacelab" {{ $theme == 'spacelab' ? 'selected="selected"' : '' }} >spacelab</option>
						<option value="united" {{ $theme == 'united' ? 'selected="selected"' : '' }} >united</option>
						<option value="yeti" {{ $theme == 'yeti' ? 'selected="selected"' : '' }} >yeti</option>
						<option value="zephyr" {{ $theme == 'zephyr' ? 'selected="selected"' : '' }} >zephyr</option>
					</optgroup>
					<optgroup label="dark">
						<option value="cyborg" {{ $theme == 'cyborg' ? 'selected="selected"' : '' }} >cyborg</option>
						<option value="darkly" {{ $theme == 'darkly' ? 'selected="selected"' : '' }} >darkly</option>
						<option value="quartz" {{ $theme == 'quartz' ? 'selected="selected"' : '' }} >quartz</option>
						<option value="slate" {{ $theme == 'slate' ? 'selected="selected"' : '' }} >slate</option>
						<option value="solar" {{ $theme == 'solar' ? 'selected="selected"' : '' }} >solar</option>
						<option value="superhero" {{ $theme == 'superhero' ? 'selected="selected"' : '' }} >superhero</option>
						<option value="vapor" {{ $theme == 'vapor' ? 'selected="selected"' : '' }} >vapor</option>
					</optgroup>
					
					<option value="default" {{ $theme == "default" ? 'selected="selected"' : '' }} >default</option>
				</select>
				<small class="form-text text-muted">This settings only take effect for other user after they (re)login</small>
			</fieldset>
		</div>

		<br><h5>System settings</h5><hr>
		<div class="form-old-row row">
			{{-- <fieldset class="col-md">
				<label for="form_t_path">Full Path to <code>tester</code></label>
				<input id="form_t_path" type="text" name="tester_path" class="form-control medium" value="{{ $tester_path }}"/>
			</fieldset>
			<fieldset class="col-md">
				<label for="form_a_path">Full Path to <code>assignments</code></label>
				<input id="form_a_path" type="text" name="assignments_root" class="form-control medium" value="{{ $assignments_root }}"/>
			</fieldset> --}}
			{{-- <fieldset class="col-md-2">
				<div class="custom-control custom-switch">
					<input id="form_log" type="checkbox" class="custom-control-input" name="enable_log" value="1" {{ $enable_log ? 'checked' : '' }}/>
					<label for="form_log" class="custom-control-label">Log</label>
				</div>
				<small class="form-text text-muted">Enable tester Log. This options is highly recommended</small>
			</fieldset> --}}
			<fieldset class="col-md">
					<label for="form_submit_penalty">Submit penalty</label>
					<input id="form_submit_penalty" type="number" name="submit_penalty" class="form-control medium" value="{{ $submit_penalty }}"/>
					<small class="form-text text-muted">Penalty time (in seconds) for each Not accpeted submissions, in according to ICPC ruling</small>
			</fieldset>
			<fieldset class="col-md">
				<label for="form_concurent_queue_process">Number of queue process</label>
				<input id="form_concurent_queue_process" type="number" name="concurent_queue_process" class="form-control" value="{{ $concurent_queue_process ?? 2 }}" />
				<small class="form-text text-muted">The number of queue process to run at the same time. This settings is sensitive to the server hardware capabilities. Set this to 0 will disable queue and stop judging all submissions</small>
			</fieldset>
			<fieldset class="col-md">
				<label for="form_default_language_number">Number of default languages</label>
				<input id="form_default_language_number" type="number" name="default_language_number" class="form-control" value="{{ $default_language_number ?? 2 }}" />
				<small class="form-text text-muted">The number languages to be enabled by default while adding problem</small>
			</fieldset>
		</div>

		<br><h5>Judge settings</h5><hr>
		<div class="form-old-row row mb-3">
			<fieldset class="col-md-3">
				<label for="form_up_limit">Upload Size Limit (kB)</label>
				<input id="form_up_limit" type="number" name="file_size_limit" class="form-control medium" value="{{ $file_size_limit }}"/>
			</fieldset>
			<fieldset class="col-md-3">
				<label for="form_out_limit">Output Size Limit (kB)</label>
				<input id="form_out_limit" type="number" name="output_size_limit" class="form-control medium" value="{{ $output_size_limit }}"/>
				<small class="form-text text-muted">Sets a limit for size of output file generated by submitted code</small>
			</fieldset>
			<fieldset class="col-md-3">
				<label for="form_results_per_page_all">Results Per Page</label>
				<input id="form_results_per_page_all" type="number" name="results_per_page_all" class="form-control medium" value="{{ $results_per_page_all }}"/>
				<small class="form-text text-muted">In "All Submissions"<br>Enter 0 for no limit</small>
			</fieldset>
			<fieldset class="col-md-3">
				<label for="form_results_per_page_final">Results Per Page</label>
				<input id="form_results_per_page_final" type="number" name="results_per_page_final" class="form-control medium" value="{{ $results_per_page_final }}"/>
				<small class="form-text text-muted">In "Final Submissions"<br>Enter 0 for no limit</small>
			</fieldset>
		</div>

		<div class="form-old-row row">
			<fieldset class="col-md-2 ">
				<div class="form-check">
					<input class="form-check-input" id="form_en_reg" type="checkbox" name="enable_registration" value="1" {{ $enable_registration ? 'checked' : '' }}/>
					<label for="form_en_reg" class="form-check-label">Registration</label>
					<small class="form-text text-muted">Open Public Registration.</small>
				</div>
			</fieldset>
			
			<fieldset class="col-md-4">
					<label for="form_reg_code">Registration Code</label>
					<input id="form_reg_code" type="number" name="registration_code" class="form-control medium" value="{{ $registration_code }}"/>
					<small class="form-text text-muted">If you want to enable registration (above option), It is better to give a registration code	to students in your class for validating registration. Enter 0 to disable.</small>
			</fieldset>

			<fieldset class="col-md-6">
				<div class="row">
					<div class="col-sm-3">
						<label for="form_late_rule">Default Coefficient Rule</label>
						<small class="form-text text-muted">PHP script without &lt;?php ?&gt; tags</small>
					</div>
						<div class="col-sm-9">
						<textarea id="form_late_rule" name="default_late_rule" rows="15" class="form-control add_text clear">{{ $default_late_rule }}</textarea>
					</div>
				</div>
			</fieldset>
		</div>

		{{-- Hide these for now until we have proper mail implementation later  --}}
		{{-- <br><h4>Emails settings</h4><hr>
		<div class="form-old-row row mb-3">
			<fieldset class="col-md-6">
				<label for="form_mail_from">Send Emails From</label>
				<input id="form_mail_from" type="text" name="mail_from" class="form-control medium" value="{{ $mail_from }}"/>
			</fieldset>
			<fieldset class="col-md-6">
				<label for="form_mail_name">Send Emails "From" Name</label>
				<input id="form_mail_name" type="text" name="mail_from_name" class="form-control medium" value="{{ $mail_from_name }}"/>
			</fieldset>
		</div>
		<div class="form-old-row row">
			<fieldset class="col-md-6">
				<div class="row">
					<div class="col-sm-3">
						<label for="form_mail_reset">Password Reset Email</label>
						<small class="form-text text-muted">You can use {SITE_URL}, {RESET_LINK} and {VALID_TIME}</small>
					</div>
					<div class="col-sm-9">
						<textarea id="form_mail_reset" name="reset_password_mail" rows="15" class="form-control add_text clear">{{ $reset_password_mail }}</textarea>
					</div>
				</div>
			</fieldset>
			<fieldset class="col-md-6">
				<div class="row">
					<div class="col-sm-3">
						<label for="form_mail_add">Add User Email</label>
						<small class="form-text text-muted">You can use {SITE_URL}, {LOGIN_URL}, {ROLE}, {USERNAME} and {PASSWORD}</small>
					</div>
					<div class="col-sm-9">
						<textarea id="form_mail_add" name="add_user_mail" rows="15" class="form-control add_text clear">{{ $add_user_mail }}</textarea>
					</div>
				</div>
			</fieldset>
		</div> --}}




		<div class="row my-2">
			{{-- <div class=" col-sm-4"> --}}
				<input type="submit" value="Save Changes" class="form-control btn btn-primary"/>
			{{-- </div> --}}
		</div>
	</form>
</div>
@endsection