var SORT_COLUMN    = 0;
var SORT_DIRECTION = "desc";

$(function() {

	var oTimer1    = null;
	var oTimer2    = null;
	var fTimer1Set = false;
	var fTimer2Set = false;

	$(".date").datepicker();

	var objTable = $("#grid").DataTable({
		language: {
			info:         "Showing _TOTAL_ entries",
			infoFiltered: "",
			infoEmpty:    "Showing 0 entries"
		},
		order:  [[SORT_COLUMN, SORT_DIRECTION]],
		paging: false
	});

	$("#grid thead tr:eq(0) th").each(function(intIndex) {
		var strHTML = $(this).html();

		$(this).html(strHTML + '<br><input class="filter" type="text" placeholder="Filter">');

		$("input", this).on("keyup change", function() {
			if (objTable.column(intIndex).search() !== this.value) {
				objTable.column(intIndex).search(this.value).draw();
			}
		});
	});

	$("#grid thead tr th input").click(function(objEvent) {
		objEvent.stopPropagation();
	});

	$("#options-button").click(function() {
		$("#admin-menu").hide();

		if (fTimer2Set) {
			clearTimeout(oTimer2);

			fTimer2Set = false;
		}

		$("#options-menu").show();
	});

	$("#options-button").mouseover(function() {
		if (fTimer1Set) {
			clearTimeout(oTimer1);

			fTimer1Set = false;
		}
	});

	$("#options-menu").mouseover(function() {
		if (fTimer1Set) {
			clearTimeout(oTimer1);

			fTimer1Set = false;
		}
	});

	$("#options-menu").mouseout(function() {
		fTimer1Set = true;

		oTimer1 = setTimeout(function() {
			$("#options-menu").hide();
		}, 1500);
	});

	$("#admin-button").click(function() {
		$("#options-menu").hide();

		if (fTimer1Set) {
			clearTimeout(oTimer1);

			fTimer1Set = false;
		}

		$("#admin-menu").show();
	});

	$("#admin-button").mouseover(function() {
		if (fTimer2Set) {
			clearTimeout(oTimer2);

			fTimer2Set = false;
		}
	});

	$("#admin-menu").mouseover(function() {
		if (fTimer2Set) {
			clearTimeout(oTimer2);

			fTimer2Set = false;
		}
	});

	$("#admin-menu").mouseout(function() {
		fTimer2Set = true;

		oTimer2 = setTimeout(function() {
			$("#admin-menu").hide();
		}, 1500);
	});

});
