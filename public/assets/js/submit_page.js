/**
 * Wecode judge
 * @file submit_page.js
 * @author truongan
 */

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length,c.length);
		}
	}
	return "";
}


function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
function b64EncodeUnicode(str) {
	//this function is shamelessly copied from: https://developer.mozilla.org/en/docs/Web/API/WindowBase64/Base64_encoding_and_decoding
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
}

document.addEventListener("DOMContentLoaded", function(){
	var theme = getCookie("code_theme");
	if (theme == "") theme = "dawn";

	var before = ace.edit("before");
	var after = ace.edit("after");
	var editor = ace.edit("editor");
	var all_ace_s = [before, editor, after];


	function get_template(problem_id, assignment_id){
		fetch(
			get_template_route,
			{
				// cache: true,
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'problem_id': problem_id,
					'assignment_id': assignment_id,
					'language_id':  document.querySelectorAll('select#languages')[0].value,
				})
			}
		).then(response => { 
			if ( response.status == 200) response.json().then(data => {
				download_full = document.querySelector('#download_full')
				if (data.full != ""){
					download_full.innerHTML = '<a download="template.txt" href = "data:text/plain,'+ encodeURIComponent(data.full)  + '">Click to download the full template</a>'; 
					download_full.classList.remove('d-none');
				} else download_full.classList.add('d-none');

				banned = document.querySelector('#banned');
				if (data.banned != ""){
					var ban_span = "";
					data.banned.split("\n").map(function(str){
						if (str != "")
		
						ban_span += "<button type='button' class=' shadow rounded-pill btn btn-sm btn-danger banned_btn'>"+ str +"</button>";
					});
					banned.innerHTML = ('<h6>The following keyword(s) are banned. They must not appear anywhere in your submission (not even in comment)<br/>'
									+ ban_span
									+ '</h6>');
					banned.classList.remove('d-none');
				} else banned.classList.add('d-none');
				if (data.before != ""){
					ace.edit("before").setValue(data.before);
					document.querySelector('#before-grp').classList.remove('d-none')
				} else document.querySelector('#before-grp').classList.add('d-none')
		
				if (data.after != ""){
					ace.edit("after").setValue(data.after);
					document.querySelector("#after-grp").classList.remove('d-none');
				} 
				else document.querySelector("#after-grp").classList.add('d-none');
		
				all_ace_s.map(function(editor){
					// console.log(editor)
					editor.resize();
				});
			})
			else{
				console.log(response)
			}
		})
	}

	$("select#problems").change(function(){
		var v = $(this).val();
		
		console.log($(this).children('option:selected').first().data('statement'));
		$("#problem_link").attr('href', $(this).children('option:selected').first().data('statement'));

		$('select#languages').empty();
		//$('<option value="0" selected="selected">-- Select Language --</option>').appendTo('select#languages');
		if (v==0 || problem_languages[v] == 0){
			get_template($(this).val(), $('#assignment_id_input').val());

			return;
		}

		first_lang = problem_languages[v][0].id;

		for (var i=0;i<problem_languages[v].length;i++)
			$('<option value="'+problem_languages[v][i].id+'">'+problem_languages[v][i].name+'</option>').appendTo('select#languages');
		
		$("select[name=language]").val(first_lang);
		$("select[name=language]").change();

		get_template($(this).val(), $('#assignment_id_input').val());
	});


	before.setReadOnly(true);
	after.setReadOnly(true);

	console.log(all_ace_s);

	all_ace_s.map(function(editor){
		editor.setTheme("ace/theme/" + theme);
	});
	$("#theme").val(theme);

	// all_ace_s.map(function (editor){editor.session.setMode("ace/mode/c_cpp");});

	$("form").submit(function(){
		$("textarea").val(editor.getValue());
	});

	$("select[name=language]").change(function(){
	
		var lang_to_mode = {"C++":"c_cpp"
			, "Java":"java"
			, "C":"c_pp"
			, "Python 2":"python"
			, "Python 3":"python"
			, "numpy-mp":"python"
			, "Free Pascal":"pascal"
			, "Javascript":"javascript"
		};
		var select = $(this)[0];
		select = select.options[select.selectedIndex].text;
		mode = "ace/mode/" + lang_to_mode[select];
		all_ace_s.map(function(editor){
			editor.session.setMode(mode);
		});
		get_template(document.querySelector('select#problems').value, $('#assignment_id_input').val());
	});

	$("#theme").change(function(){
		t = $(this).val();

		all_ace_s.map(function(editor){
			editor.setTheme("ace/theme/" + t);
		});
		setCookie('code_theme', t, 30);
	});
});
