$(document).ready(function() {
	$("#poll-options").sortable({
		axis: "y",
		placeholder: "option-placeholder",
		handle: "span.option-handle",
		cursor: "pointer",
		update: function() {
			manage_buttons();
		}
	});

	// add the current time to datepicker
	var date_obj = new Date();
	var date_obj_hours = date_obj.getHours();
	var date_obj_minutes = date_obj.getMinutes();
	var date_obj_am_pm = (date_obj_hours < 12) ? "AM" : "PM";

	if(date_obj_hours != 0) {
		date_obj_hours = ((date_obj_hours + 11) % 12) + 1;
	}

	if(date_obj_minutes < 10) {
		date_obj_minutes = "0" + date_obj_minutes;
	}

	var date_obj_time = "\' "+date_obj_hours+":"+date_obj_minutes+date_obj_am_pm+"\'";

	$("input#expiration_date").datepicker({ dateFormat:$.datepicker.ISO_8601 + date_obj_time });

	$("#add-option").click(function(e) {
		// create option field
		var html = "<li><span class=\"option-handle\"><img src=\"" + $('#poll-options li:eq(0) .option-handle img').attr('src') + "\" alt=\"handle\" /></span><input type=\"text\" name=\"options[]\" value=\"\" class=\"field\" />&nbsp;<a href=\"#\" class=\"remove-option\">[remove]</a></li>";

		// add new option field
		$("#poll-options").append($(html));

		manage_buttons();
		return false;
	});

	$("#add-option-other").click(function() {
		if( $('#poll-options').find('input[name=other]').length != 0 ) { return false; }

		// create option field
		var html = "<li><span class=\"option-handle\"><img src=\"" + $('#poll-options li:eq(0) .option-handle img').attr('src') + "\" alt=\"handle\" /></span><input type=\"text\" name=\"other\" value=\"Other (specify)\" class=\"field\" />&nbsp;<a href=\"#\" class=\"remove-option\">[remove]</a></li>";

		// add new option field
		$("#poll-options").append($(html));

		manage_buttons();
		return false;
	});

	$("select[name=poll_selector]").change(function() {
		if( this.value != '' ) {
			$(this).parent().next('.mainTable').fadeOut();
		} else {
			$(this).parent().next('.mainTable').fadeIn();
		}
	});

	manage_buttons();
});

function manage_buttons() {
	// hide first and second option\'s remove button
	$("#poll-options li a.remove-option").hide();
	$("#poll-options li:gt(1) a.remove-option").show();

	// bind button element
	$("a.remove-option").unbind("click");
	$("a.remove-option").bind("click", function(e) {
		e.preventDefault();
		$(this).closest("li").remove();
	});
}
