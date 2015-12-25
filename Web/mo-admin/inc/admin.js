var datacount = 1;
var extracount = 1;
function add_test_data() {
	new_data = "		 <div class=\"row\">\
			<div class=\"col-md-3\">\
			 <p><input type=\"file\" id=\"input" + datacount + "\" title=\"输入数据 #" + datacount + "\" name=\"input" + datacount + "\" class=\"btn-primary\"></p>\
			 </div>\
			 <div class=\"col-md-3\">\
			 <p><input type=\"file\" id=\"stdout" + datacount + "\" title=\"输出数据 #" + datacount + "\" name=\"stdout" + datacount + "\" class=\"btn-primary\"></p>\
			 </div>\
		 </div>"
	$(".test-data").append(new_data);
	$('#input'+datacount).bootstrapFileInput();
	$('#stdout'+datacount).bootstrapFileInput();
	datacount = datacount + 1;
}
function add_extra_data() {
	extracount = extracount + 1;
	new_extra = "<div class=\"row\">\
			<div class=\"col-md-3\">\
				<input id=\"extra-tag" + extracount + "\" class=\"form-control\" type=\"text\" name=\"extra-tag" + extracount + "\" placeholder=\"标签\">\
			</div>\
			<div class=\"col-md-9\">\
				<input id=\"extra-con" + extracount + "\" class=\"form-control\" type=\"text\" name=\"extra-con" + extracount + "\" placeholder=\"内容\">\
			</div>\
		 </div>"
	$(".extra").append(new_extra);
}
function prob_detail(pid) {
	$('#'+pid).webuiPopover({title:'Title',content:'Content',placement:'bottom'});
}
