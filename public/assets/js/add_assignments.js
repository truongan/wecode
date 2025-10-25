/*
	Wecode judge
	author: truongan
	date: 20160330
*/

function format_problem(prob) {
	if (!prob.id) return prob.text; // THis is necessary because one dummy options with "searching" text will be created by select2
	var data = prob.element.dataset;
	var element = $(
		`<span class="badge bg-primary">${data.id} </span>` +
			`<span class="badge rounded-pill bg-${data.sharable == 1 ? "success" : "secondary"}">${data.owner}</span>` +
			`<span class="text-dark">${data.name} </span>` +
			`<span class="text-small text-dark-subtle">(${data.note})</span>` +
			`<span class="text-small text-dark">(${data.tags})</span>`,
	);
	return element;
}
function update_problem_count() {
	$(".count_problems").html($(".list-group-item").length - 1);
}

document.addEventListener("DOMContentLoaded", function () {
	var a = Sortable.create(problem_list, {
		handle: ".list_handle",
		ghostClass: "list-group-item-secondary",
		chosenClass: "list-group-item-primary",
		animation: 150,
		filter: ".list_remover",
		onFilter: function (evt) {
			var item = evt.item,
				ctrl = evt.target;
			var id = $(item).find('input[name="problem_id[]"]').val();
			console.log(id);
			console.log($('option[data-id="' + id + '"]'));
			$('option[data-id="' + id + '"]').prop("selected", false);

			$(".all_problems").trigger("change");
			$(".all_problems").select2("close");

			if (Sortable.utils.is(ctrl, ".list_remover")) {
				// Click on remove button
				item.parentNode.removeChild(item); // remove sortable item
			}
			update_problem_count();
		},
	});

	$(".all_problems").select2({
		placeholder: "Select problem to add to this assignment",
		templateResult: format_problem,
		closeOnSelect: false,
	});
	$(".all_problems").on("select2:select", function (e) {
		console.log(e);
		console.log((selected_data = $(e.params.data.element).data()));
		var dummy_row = $(".list-group-item.d-none");
		var new_row = dummy_row.clone();
		new_row.removeClass("d-none");

		new_row.find('input[name="problem_id[]"]').val(selected_data.id);
		new_row.find('input[name="problem_name[]"]').val(selected_data.name);

		new_row
			.find(".lead")
			.html(
				'<span class="badge text-dark bg-light">' +
					selected_data.id +
					"</span>" +
					'<span class="badge bg-secondary rounded-pill">' +
					selected_data.owner +
					"</span>" +
					selected_data.name,
			);
		new_row.find(".admin_note").html(selected_data.note);

		new_row.insertBefore(dummy_row).slideDown();

		update_problem_count();
	});
	$(".all_problems").on("select2:unselecting", function (e) {
		e.preventDefault();
	});

	$("#select_multiple_problems").click(function () {
		console.log("shit");
		var min = document.getElementById("multiple_problems_min").value;
		var will_select = [];
		document
			.querySelectorAll(".all_problems > option")
			.forEach(function (obj, idx) {
				console.log(obj.selected);
				console.log(obj.dataset.no_of_assignment);
				if (
					obj.dataset.no_of_assignment <= min &&
					obj.selected == false
				) {
					obj.selected = true;
					$(".all_problems").trigger("change");
					$(".all_problems").trigger({
						type: "select2:select",
						params: {
							data: { element: obj },
						},
					});
				}
			});
	});

	$("#distribute_score").click(function () {
		var scores = $(".problem-score");
		var count = scores.length - 1;
		if (count > 0) {
			scores.val($("#score_amount").val() / count);
		}
		scores.last().val(0);
		scores.last().change();
	});
	$("#set_score").click(function () {
		var scores = $(".problem-score");
		scores.val($("#score_amount").val());
		scores.last().val(0);
		scores.last().change();
	});
	$("ul").on("change", ".problem-score", function () {
		$(".sum_score").html("0");
		var i = 0;
		$(".problem-score").each(function () {
			i = i + parseInt($(this).val());
		});
		$(".sum_score").html(i);
	});
	$(".problem-score").change();
	update_problem_count();
});
