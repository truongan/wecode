/*
	Wecode judge
	author: truongan
	date: 20160330
*/

function format_problem(prob) {
	// if (!prob.id) return prob.text; // THis is necessary because one dummy options with "searching" text will be created by select2
	var data = prob.dataset;
	var element =
		`<span class="badge bg-primary">${data.id} </span>` +
		`<span class="badge rounded-pill bg-${data.sharable == 1 ? "success" : "secondary"}">${data.owner}</span>` +
		`<span class="   text-muted">(${data.tags})</span>` +
		`<span class=" fs-5">${data.name} </span>` +
		`<span class="text-muted ">(${data.note})</span>`;
	return element;
}
function update_problem_count() {
	$(".count_problems").html($(".list-group-item").length - 1);
}
function sum_score() {
	document.querySelector(".sum_score").innerHTML = Array.from(document.querySelectorAll(".problem-score")).reduce(
		(sum, input) => sum + parseInt(input.value),
		0,
	);
}

document.addEventListener("DOMContentLoaded", function () {
	document.querySelectorAll(".all_problems option").forEach((x) => (x.dataset["html"] = format_problem(x)));
	var problem_select = new SlimSelect({
		select: ".all_problems",
		events: {
			afterChange: (newval) => {
				var selected_values = new Set(newval.map((opt) => opt.data.id));

				//First, remove .list-group-item that are not selected
				document.querySelectorAll("#problem_list .list-group-item").forEach((group) => {
					value = group.querySelector('input[name="problem_id[]"]').value;
					if (selected_values.has(value) == false && group.classList.contains("d-none") == false) group.remove();
					else selected_values.delete(value);
				});

				//Then we add missing problem
				var need_add = Array.from(newval).filter((x) => selected_values.has(x.data.id));
				need_add.forEach((opt) => {
					var dummy_row = document.querySelector(".list-group-item.d-none");
					var new_row = dummy_row.cloneNode(true);

					new_row.classList.remove("d-none");
					new_row.querySelector('input[name="problem_id[]"]').value = opt.data.id;
					new_row.querySelector('input[name="problem_name[]"]').value = opt.data.name;

					new_row.querySelector(".lead").innerHTML =
						`<span class="badge text-dark bg-light">${opt.data.id}</span>` +
						`<span class="badge bg-secondary rounded-pill">${opt.data.owner}</span> ${opt.data.name}`;

					new_row.querySelector(".admin_note").innerHTML = opt.data.note;

					dummy_row.parentNode.insertBefore(new_row, dummy_row);

					update_problem_count();
				});
			},
		},
		settings: {
			placeholderText: "Select problem to add to this assignment",
			maxValuesShown: 1,
			closeOnSelect: false,
			keepOrder: true,
			allowDeselect: false,
		},
	});

	var a = Sortable.create(problem_list, {
		handle: ".list_handle",
		ghostClass: "list-group-item-secondary",
		chosenClass: "list-group-item-primary",
		animation: 150,
		filter: ".list_remover",
		onFilter: function (evt) {
			var item = evt.item,
				ctrl = evt.target;

			var id = item.querySelector('input[name="problem_id[]"]').value;
			var filtered = problem_select.getSelected().filter((x) => x != id);

			if (Sortable.utils.is(ctrl, ".list_remover")) {
				// Click on remove button
				item.parentNode.removeChild(item); // remove sortable item
				problem_select.setSelected(filtered, false);
				update_problem_count();
			}
		},
	});

	document.querySelector("#select_multiple_problems").addEventListener("click", function () {
		var min = document.getElementById("multiple_problems_min").value;
		var will_select = Array.from(document.querySelectorAll(".all_problems > option")).reduce((accu, obj) => {
			if (obj.dataset.no_of_assignment <= min) return [...accu, obj.value];
			else return accu;
		}, problem_select.getSelected());
		problem_select.setSelected(will_select);
	});

	document.querySelector("#distribute_score").addEventListener("click", function () {
		var scores = $(".problem-score");
		var count = scores.length - 1;
		if (count > 0) {
			scores.val($("#score_amount").val() / count);
		}
		scores.last().val(0);
		scores.last().change();
	});
	document.querySelector("#set_score").addEventListener("click", function () {
		var scores = $(".problem-score");
		scores.val($("#score_amount").val());
		scores.last().val(0);
		scores.last().change();
	});
	document.querySelector("#problem_list").addEventListener("change", (e) => {
		if (e.target.matches(".problem-score")) {
			sum_score();
		}
	});

	sum_score();
	update_problem_count();
});
