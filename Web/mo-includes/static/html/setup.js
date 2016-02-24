function check_info() {
	item=new Array("mongodb_host","mongodb_port","mongodb_user","mongodb_pwd","redis_host","redis_port","redis_pwd","server_host","server_port","admin_name","admin_pwd");
	url="setup.php?action=check&mongodb_host="+$("#mongodb_host").val()+"&mongodb_port="+$("#mongodb_port").val()+
			"&mongodb_user="+$("#mongodb_user").val()+"&mongodb_pwd="+btoa($("#mongodb_pwd").val())+"&redis_host="+$("#redis_host").val()+
			"&redis_port="+$("#redis_port").val()+"&redis_pwd="+btoa($("#redis_pwd").val())+"&server_host="+$("#server_host").val()+
			"&server_port="+$("#server_port").val()+"&admin_name="+btoa($("#admin_name").val())+"&admin_pwd="+btoa($("#admin_pwd").val());
	req = $.ajax({
    type:'get',
    url:url,
	  success:function(result){
      $("#info").empty();
      result = JSON.parse(result);
      for (i in item) {
        $("#"+item[i]).parent().parent().removeClass("has-error");
        $("#"+item[i]).parent().parent().addClass("form-group has-success");
      }
      if (!result['ok']) {
        for (i in result['loc']) {
          $("#"+result['loc'][i]).parent().parent().removeClass("has-success");
          $("#"+result['loc'][i]).parent().parent().addClass("form-group has-error");
        }
        for (i in result['detail']) {
          $("#info").append('<div class="alert alert-danger">'+result['detail'][i]+'</div>');
        }
      } else {
        $("#info").append('<div class="alert alert-success">验证通过！即将进入安装环节~</div>');
        setTimeout("location.href='setup.php?step=3'",2000);
      }
      $('html,body').animate({scrollTop:0},'slow');
	  },
    cache:false
	});
}
