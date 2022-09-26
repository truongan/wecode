/**
 * Sharif Judge
 * @file shj_submissions.js
 * @author pntruongan <pnruongan 2005 *at* gmail com>
 *
 *     Javascript codes for "All Submissions" and "Final Submissions" pages
 */

shj.modal_open = false;

Prism.plugins.toolbar.registerButton('select-code', function(env) {
	var button = document.createElement('button');
	button.innerHTML = 'Select Code';

	button.addEventListener('click', function () {
		// Source: http://stackoverflow.com/a/11128179/2757940
		if (document.body.createTextRange) { // ms
			var range = document.body.createTextRange();
			range.moveToElementText(env.element);
			range.select();
		} else if (window.getSelection) { // moz, opera, webkit
			var selection = window.getSelection();
			var range = document.createRange();
			range.selectNodeContents(env.element);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	});

	return button;
});
// selectText is used for "Select All" when viewing a submitted code
jQuery.fn.selectText = function(){
	var doc = document
		, element = this[0]
		, range, selection
		;
	if (doc.body.createTextRange) {
		range = document.body.createTextRange();
		range.moveToElementText(element);
		range.select();
	} else if (window.getSelection) {
		selection = window.getSelection();
		range = document.createRange();
		range.selectNodeContents(element);
		selection.removeAllRanges();
		selection.addRange(range);
	}
};

shj.html_encode = function(value) {
	return $('<div/>').text(value).html();
}

$(document).ready(function () {
	$(document).on('click', '#select_all', function (e) {
		e.preventDefault();
		$('.code-column').selectText();
	});

	//$(".btn").click(function () {
	$("td").on('click', '.btn', function () {
		$('.modal-body').html('<div class="text-center">		<div class="spinner-border" ole="status">			<span class="sr-only">Loading...</span>		</div>	</div>'); //First, clean up the modal body probably left over from previous view
		var button = $(this);
		var row = button.parents('tr');
		var type = button.data('type');
		var view_code_request = $.ajax({
			
			cache: true,
			type: 'POST',
			url: site_url + '/submissions/view_code',
			data: {
				'_token': $('input[name=_token]').val(),
				type: type,
				submit_id: row.data('id')
			},
			success: function (data) {
				if (type == 'code')
					 data.text = shj.html_encode(data.text);
				$('.modal-body').html('<pre class="code-column line-numbers"><code>'+data.text+'</code></pre>');
				$('.modal-title').html('<code>'+data.file_name+' | Submit ID: '+row.data('id')+' | Username: '+row.data('u')+' | Problem: '+row.data('p')+'</code>');
				if (type == 'code'){
					// $('code.code-column').snippet(data.lang, {style: shj.color_scheme});
					$('pre.code-column').addClass('lang-' + data.lang);
					Prism.highlightAll();
				}

				if(type == 'log') $('pre.code-column').addClass('wcj_log');

			}
		});
		if (!shj.modal_open) {
			shj.modal_open = true;
			$("#submission_modal").modal('show');
			$("#submission_modal").on("hidden.bs.modal", function(){
				shj.modal_open = false;
			});
			$("#submission_modal").on("hide.bs.modal", function(){
				view_code_request.abort();
			});

		}

	});

	$(".shj_rejudge").click(function () {
		var row = $(this).parents('tr');
		var vanilajs_row = row[0];
		// console.log(row);
		// console.log(row.data());
		// console.log(row.data('id'));
		$.ajax({
			type: 'POST',
			url: site_url + '/submissions/rejudge',
			data: {
				submission_id: row.data('id'),
			},
			error: shj.loading_error,
			success: function (response) {
				if (response.done) {
					// console.log(row.children());
					row.children('.js-verdict').html('<div class="btn btn-secondary pending" data-type="result">PENDING</div>');
					vanilajs_row.querySelector('.js-time').innerHTML = '';
					vanilajs_row.querySelector('.js-mem').innerHTML = '';
					vanilajs_row.querySelector('.js-score').innerHTML = '';
					
					$.notify('Rejudge in progress', {position: 'bottom right', className: 'info', autoHideDelay: 2500});
					setTimeout(update_status, update_status_interval);
				}
				else
					shj.loading_failed(response.message);
			}
		});
	});
	$(".set_final").click(
		function () {
			var row = $(this).parents('tr');
			var submit_id = row.data('id');
			var problem = row.data('p');
			var username = row.data('u');

			$.ajax({
				type: 'POST',
				url: site_url + '/submissions/select',
				data: {
					'submission': submit_id,

					'_token': $('input[name=_token]').val(),
				},
				error: shj.loading_error,
				success: function (response) {
					if (response.done) {
						$("tr[data-u='" + username + "'][data-p='" + problem + "'] i.set_final").removeClass('fa-check-circle color11').addClass('fa-circle');
						row.find("i.set_final").removeClass('fa-circle').addClass('fa-check-circle color11');
					}
					else
						shj.loading_failed(response.message);
				}
			});
		}
	);

	setTimeout(update_status, update_status_interval);

	$(".data-table").DataTable({
		"pageLength": 50,
		"lengthMenu": [ [20, 50, 100, 200, -1], [20, 50, 100, 200, "All"] ]
	});
});


update_status_interval = 6000;
function update_status(){

	$('tr').each(function(){
		var status = $(this).children('.js-verdict');
		var row = $(this)[0];
		if (status.children('div').hasClass('pending')){
			$.ajax({
				type: 'POST',
				url: site_url + '/submissions/view_status',
				data: {
					submit_id: $(this).data('id'),
				}, 
				error: shj.loading_error,
				success: function (response) {
					response = JSON.parse(response);
					console.log(response.status);
					console.log(typeof response);
					var element;
					switch (response.status.toLowerCase() ){
						case 'pending':
							element = ('<div class="btn btn-secondary pending" data-type="result" >PENDING</div>');
							$.notify('Still pending', {position: 'bottom right', className: 'info', autoHideDelay: 2000});

						break;

						case  'score' :
							if (response.judgement.mems.length > 0) row.querySelector('.js-time').innerHTML = Math.max(...response.judgement.times);
							if (response.judgement.mems.length > 0) row.querySelector('.js-mem').innerHTML = Math.max(...response.judgement.mems);
							row.querySelector('.js-score').innerHTML = '<span class = "lead " > ' + response.final_score + '</span>';
							row.querySelector('.js-score').classList.add( response.pre_score == 10000 ? 'text-success' : 'text-danger');

							$.notify('Submission has been judged', {position: 'bottom right', className: 'success', autoHideDelay: 2000});

						break;

						default:
							element = ('<div class="btn btn-primary"  data-type="result">' + response.status + '</div>');
					}
					// status.html(element);
					row.querySelector('.js-verdict').innerHTML = response.rendered_verdict;
				}
			});
		}
	});

	if ($('div.pending')[0]){
		setTimeout(update_status, update_status_interval);

	}
}
