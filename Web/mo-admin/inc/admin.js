var datacount = 1;
function add_test_data() {
	datacount = datacount + 1;
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
}
