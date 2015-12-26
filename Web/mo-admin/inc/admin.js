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
	$('#'+pid).webuiPopover({title:'题目#'+pid+' '+prob[pid]['title'],content:'<p><span class="label label-info">\
<span class="glyphicon glyphicon-tags"></span>&nbsp;标签</span> '+prob[pid]['tag']+'</p>\
<p><span class="label label-primary">\
<span class="glyphicon glyphicon-time"></span>&nbsp;限时</span> '+prob[pid]['time_limit']+'ms&nbsp;&nbsp;\
<span class="label label-primary">\
<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;内存</span> '+prob[pid]['memory_limit']+'MB</p>\
<p><span class="label label-warning">\
<span class="glyphicon glyphicon-send"></span>&nbsp;尝试</span> '+prob[pid]['submit']+'('+prob[pid]['try']+')&nbsp;&nbsp;<span class="label label-success"><span class="glyphicon glyphicon-ok">\
</span>&nbsp;通过</span> '+prob[pid]['ac']+'('+prob[pid]['solved']+')</p>\
<p><span class="label label-primary">\
<span class="glyphicon glyphicon-calendar"></span>&nbsp;发布时间</span> '+prob[pid]['post_time']+'</p>'
,placement:'bottom',animation:'fade'});
}
function client_detail(cid) {
	$('#client-'+cid).webuiPopover({title:'评测端#'+cid+' '+client[cid]['name'],content:'<p><span class="label label-info">\
<span class="glyphicon glyphicon-info-sign"></span>&nbsp;简介</span> '+'</p><p>'+client[cid]['intro']+'</p>\
<p><span class="label label-info">\
<span class="glyphicon glyphicon-time"></span>&nbsp;上次心跳</span> '+client[cid]['last_ping']+'</p>\
<p><span class="label label-info">\
<span class="glyphicon glyphicon-tasks"></span>&nbsp;平均负载</span> <code>'+client[cid]['load_1']+'</code>&nbsp;<code>'+client[cid]['load_5']+'</code>&nbsp;<code>'+client[cid]['load_15']+'</code></p>\
<p><span class="label label-info">\
<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;已用内存</span> '+client[cid]['memory']+'%</p>\
<p><span class="label label-primary">\
<span class="glyphicon glyphicon-transfer"></span>&nbsp;通信密钥</span> '+'</p>'+client[cid]['hash']
,placement:'bottom',animation:'fade',width:320});
}
function show_detail(loc, msg_title, msg_content, msg_width) {
	msg_width=msg_width||'auto';
	$(loc).webuiPopover({title:msg_title,content:msg_content,
		placement:'bottom',animation:'fade',width:msg_width});
}
