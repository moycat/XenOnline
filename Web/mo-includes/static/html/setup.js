function check_info() {
	url="setup.php?action=check&mongodb_host="+$("#mongodb_host").val()+"&mongodb_port="+$("#mongodb_port").val()+
			"&mongodb_user="+$("#mongodb_user").val()+"&mongodb_pwd="+$("#mongodb_pwd").val();
	request=new XMLHttpRequest();
	request.open("GET",url,true);
	request.send();
}