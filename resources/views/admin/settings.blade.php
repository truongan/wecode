@php($selected = 'settings')
@extends('layouts.app')

@section('icon', 'fas fa-cogs')

@section('title', 'Settings')

@section('content')

@if ($errors->any())
	<div class="alert alert-danger">Error updating settings.</div>
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div><br/>
    @endforeach
@endif

@if ($form_status ?? '' == 'ok' )
	<div class="alert alert-success">Settings updated successfully.</div>
@endif

@if(! ($defc ?? '') )
	<div class="alert alert-danger">"Tester path" is not correct.</div>
@endif 

<div class="col-12">
    <form action="{{ route('settings.update') }}" method="post">
		@csrf
		<h5>Display settings</h5><hr>
		<div class="form-row">
			<fieldset class="form-group col-md-4">
				<label for="form_site_name">Site name</label>
				<input id="form_site_name" type="text" name="site_name" class="form-control" value="{{ $site_name }}"/>
				<small class="form-text text-muted">Website's name to be display on Top Bar and HTML title.</small>
			</fieldset>

			<fieldset class="form-group col-md-4">
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

			<fieldset class="form-group col-md-2">
				<label for="form_week">Week Start Day</label>
				<select id="form_week" name="week_start" class="form-control custom-select">
					<option value="0" {{ $week_start == 0 ? 'selected="selected"' : ''}}>Sunday</option>
					<option value="1" {{ $week_start == 1 ? 'selected="selected"' : '' }}>Monday</option>
					<option value="2" {{ $week_start == 2 ? 'selected="selected"' : '' }}>Tuesday</option>
					<option value="3" {{ $week_start == 3 ? 'selected="selected"' : '' }}>Wednesday</option>
					<option value="4" {{ $week_start == 4 ? 'selected="selected"' : '' }}>Thursday</option>
					<option value="5" {{ $week_start == 5 ? 'selected="selected"' : '' }}>Friday</option>
					<option value="6" {{ $week_start == 6 ? 'selected="selected"' : '' }}>Saturday</option>
				</select>
			</fieldset>

			<fieldset class="form-group col-md-2">
				<label for="theme">Theme</label>
				<select id="theme" name="theme" class="form-control custom-select">
					<optgroup label="Light">
						<option value="lumen" {{ $theme == "lumen" ? 'selected="selected"' : '' }} >lumen</option>
						<option value="litera" {{ $theme == "litera" ? 'selected="selected"' : '' }} >litera</option>
						{{-- <option value="material-design" {{ $theme == "material-design" ? 'selected="selected"' : '' }} >material-design</option> #} --}}
						<option value="pulse" {{ $theme == "pulse" ? 'selected="selected"' : '' }} >pulse</option>
						<option value="cosmo" {{ $theme == "cosmo" ? 'selected="selected"' : '' }} >cosmo</option>
						<option value="journal" {{ $theme == "journal" ? 'selected="selected"' : '' }} >journal</option>
						<option value="flatly" {{ $theme == "flatly" ? 'selected="selected"' : '' }} >flatly</option>
						<option value="sketchy" {{ $theme == "sketchy" ? 'selected="selected"' : '' }} >sketchy</option>
						<option value="cerulean" {{ $theme == "cerulean" ? 'selected="selected"' : '' }} >cerulean</option>
						<option value="united" {{ $theme == "united" ? 'selected="selected"' : '' }} >united</option>
						<option value="yeti" {{ $theme == "yeti" ? 'selected="selected"' : '' }} >yeti</option>
						<option value="sandstone" {{ $theme == "sandstone" ? 'selected="selected"' : '' }} >sandstone</option>
						<option value="simplex" {{ $theme == "simplex" ? 'selected="selected"' : '' }} >simplex</option>
					</optgroup>
					<optgroup label="Dark">
						<option value="cyborg" {{ $theme == "cyborg" ? 'selected="selected"' : '' }} >cyborg</option>
						<option value="darkly" {{ $theme == "darkly" ? 'selected="selected"' : '' }} >darkly</option>
						<option value="slate" {{ $theme == "slate" ? 'selected="selected"' : '' }} >slate</option>
						<option value="superhero" {{ $theme == "superhero" ? 'selected="selected"' : '' }} >superhero</option>
						<option value="solar" {{ $theme == "solar" ? 'selected="selected"' : '' }} >solar</option>
					</optgroup>
					<optgroup label="Spacy">
						<option value="lux" {{ $theme == "lux" ? 'selected="selected"' : '' }} >lux</option>
						<option value="materia" {{ $theme == "materia" ? 'selected="selected"' : '' }} >materia</option>
						<option value="spacelab" {{ $theme == "spacelab" ? 'selected="selected"' : '' }} >spacelab</option>

						<!-- <option value="minty" {{ $theme == "minty" ? 'selected="selected"' : '' }} >minty</option> -->
					</optgroup>

					<option value="default" {{ $theme == "default" ? 'selected="selected"' : '' }} >default</option>
				</select>
				<small class="form-text text-muted">This settings only take effect for other user after they (re)login</small>
			</fieldset>
		</div>

		<br><h5>System settings</h5><hr>
		<div class="form-row">
			<fieldset class="form-group col-md">
				<label for="form_t_path">Full Path to <code>tester</code></label>
				<input id="form_t_path" type="text" name="tester_path" class="form-control medium" value="{{ $tester_path }}"/>
			</fieldset>
			<fieldset class="form-group col-md">
				<label for="form_a_path">Full Path to <code>assignments</code></label>
				<input id="form_a_path" type="text" name="assignments_root" class="form-control medium" value="{{ $assignments_root }}"/>
			</fieldset>
			{{-- <fieldset class="form-group col-md-2">
				<div class="custom-control custom-switch">
					<input id="form_log" type="checkbox" class="custom-control-input" name="enable_log" value="1" {{ $enable_log ? 'checked' : '' }}/>
					<label for="form_log" class="custom-control-label">Log</label>
				</div>
				<small class="form-text text-muted">Enable tester Log. This options is highly recommended</small>
			</fieldset> --}}
			<fieldset class="form-group col-md-3">
				<label for="form_concurent_queue_process">Number of queue process</label>
				<input id="form_concurent_queue_process" type="number" name="concurent_queue_process" class="form-control" value="{{ $concurent_queue_process ?? 2 }}" />
				<small class="form-text text-muted">The number of queue process to run at the same time. This settings is sensitive to the server hardware capabilities. Set this to 0 will disable queue and stop judging all submissions</small>
			</fieldset>

		</div>

		<br><h5>Judge settings</h5><hr>
		<div class="form-row">
			<fieldset class="form-group col-md-3">
				<label for="form_up_limit">Upload Size Limit (kB)</label>
				<input id="form_up_limit" type="number" name="file_size_limit" class="form-control medium" value="{{ $file_size_limit }}"/>
			</fieldset>
			<fieldset class="form-group col-md-3">
				<label for="form_out_limit">Output Size Limit (kB)</label>
				<input id="form_out_limit" type="number" name="output_size_limit" class="form-control medium" value="{{ $output_size_limit }}"/>
				<small class="form-text text-muted">Sets a limit for size of output file generated by submitted code</small>
			</fieldset>
			<fieldset class="form-group col-md-3">
				<label for="form_results_per_page_all">Results Per Page</label>
				<input id="form_results_per_page_all" type="number" name="results_per_page_all" class="form-control medium" value="{{ $results_per_page_all }}"/>
				<small class="form-text text-muted">In "All Submissions"<br>Enter 0 for no limit</small>
			</fieldset>
			<fieldset class="form-group col-md-3">
				<label for="form_results_per_page_final">Results Per Page</label>
				<input id="form_results_per_page_final" type="number" name="results_per_page_final" class="form-control medium" value="{{ $results_per_page_final }}"/>
				<small class="form-text text-muted">In "Final Submissions"<br>Enter 0 for no limit</small>
			</fieldset>
		</div>

		<div class="form-row">
			<fieldset class="form-group col-md-2">
				<label for="form_en_reg">Registration</label>
				<input id="form_en_reg" type="checkbox" name="enable_registration" value="1" {{ $enable_registration ? 'checked' : '' }}/>
				<small class="form-text text-muted">Open Public Registration.</small>
			</fieldset>
			<fieldset class="form-group col-md-2">
					<label for="form_reg_code">Registration Code</label>
					<input id="form_reg_code" type="number" name="registration_code" class="form-control medium" value="{{ $registration_code }}"/>
					<small class="form-text text-muted">If you want to enable registration (above option), It is better to give a registration code	to students in your class for validating registration. Enter 0 to disable.</small>
			</fieldset>
			<fieldset class="form-group col-md-2">
					<label for="form_submit_penalty">Submit penalty</label>
					<input id="form_submit_penalty" type="number" name="submit_penalty" class="form-control medium" value="{{ $submit_penalty }}"/>
					<small class="form-text text-muted">Penalty time (in seconds) for each Not accpeted submissions, in according to ICPC ruling</small>
			</fieldset>
			<fieldset class="form-group col-md-6">
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


		<br><h4>Emails settings</h4><hr>
		<div class="form-row">
			<fieldset class="form-group col-md-6">
				<label for="form_mail_from">Send Emails From</label>
				<input id="form_mail_from" type="text" name="mail_from" class="form-control medium" value="{{ $mail_from }}"/>
			</fieldset>
			<fieldset class="form-group col-md-6">
				<label for="form_mail_name">Send Emails "From" Name</label>
				<input id="form_mail_name" type="text" name="mail_from_name" class="form-control medium" value="{{ $mail_from_name }}"/>
			</fieldset>
		</div>
		<div class="form-row">
			<fieldset class="form-group col-md-6">
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
			<fieldset class="form-group col-md-6">
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
		</div>



	{{-- {# Since we employed docker container already. Primitive sand boxing can be retired #} --}}


		{{-- <br><h4 >
			Shield settings <span class="title_menu_item">
				<a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/shield.md" target="_blank"><i class="fa fa-question-circle color11"></i> Help</a>
			</span>
		</h4><hr>
		<div class="form-row">
			<fieldset class="form-group col-md-6">
				<label for="form_c_sh"  class="sr-only">C Shield</label>
				<label for="form_def_c">Shield Rules (for C)</label>
				<small class="form-text text-muted">Enable <a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/shield.md" target="_blank">Shield</a> for C</small>
				<div class="input-group">
					<span class="input-group-addon">
						<input id="form_c_sh" type="checkbox" name="enable_c_shield" value="1" {{ enable_c_shield ? 'checked' }}/>
					</span>
					<textarea id="form_def_c" name="def_c" rows="15" class="form-control add_text clear">{{ defc }}</textarea>
				</div>
			</fieldset>
			<fieldset class="form-group col-md-6">
				<label for="form_def_cpp">Shield Rules (for C++)</label>
				<label for="form_cpp_sh" class="sr-only" >C++ Shield</label><br>
				<small class="form-text text-muted">Enable <a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/shield.md" target="_blank">Shield</a> for C++</small>
				<div class="input-group">
					<span class="input-group-addon">
						<input id="form_cpp_sh" type="checkbox" name="enable_cpp_shield" value="1" {{ enable_cpp_shield ? 'checked' }}/>
					</span>
					<textarea id="form_def_cpp" name="def_cpp" rows="15" class="form-control add_text clear">{{ defcpp }}</textarea>
				</div>
			</fieldset>
			<fieldset class="form-group col-md-6">
				<label for="form_shield_py2">Shield (for Python 2)</label>
				<label for="form_py2_sh" class="sr-only">Python 2 Shield</label><br>
				<small class="form-text text-muted">Enable <a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/shield.md" target="_blank">Shield</a> for Python 2</small>
				<div class="input-group">
					<span class="input-group-addon">
					<input id="form_py2_sh" type="checkbox" name="enable_py2_shield" value="1" {{ enable_py2_shield ? 'checked' }}/>
					</span>
					<textarea id="form_shield_py2" name="shield_py2" rows="15" class="form-control add_text clear">{{ shield_py2 }}</textarea>
				</div>
			</fieldset>
			<fieldset class="form-group col-md-6">
				<label for="form_shield_py3">Shield (for Python 3)</label>
				<label for="form_py3_sh">Python 3 Shield</label><br>
				<small class="form-text text-muted">Enable <a href="https://github.com/truongan/wecode-judge/blob/docs/v1.4/shield.md" target="_blank">Shield</a> for Python 3</small>
				<div class="input-group">
					<span class="input-group-addon">
						<input id="form_py3_sh" type="checkbox" name="enable_py3_shield" value="1" {{ enable_py3_shield ? 'checked' }}/>
					</span>
					<textarea id="form_shield_py3" name="shield_py3" rows="15" class="form-control add_text clear">{{ shield_py3 }}</textarea>
				</div>
			</fieldset>
		</div> --}}

		<div class="row mb-2">
			<div class="offset-sm-8 col-sm-4">
				<input type="submit" value="Save Changes" class="form-control btn btn-primary"/>
			</div>
		</div>
	</form>
</div>
@endsection