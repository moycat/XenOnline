/* Problen */
function deleteProblem(id) {
    $("#pidtodelTitle").text("#" + id);
    $("#pidtodelButton").attr('href', '/admin/problem/' + id + '/delete');
    $('#deleteProblem').modal();
}

/* Data upload */
adminNewData.count = 1;
function adminNewData() {
    new_file =
'<div id="data'+adminNewData.count+'">\
    <div class="row">\
        <div class="col-md-4">\
            <p><input type="file" id="input'+adminNewData.count+'" title="输入数据 #'+adminNewData.count+'" name="input[]" class="btn-info btn-sm" required></p>\
        </div>\
        <div class="col-md-4">\
            <p><input type="file" id="stdout'+adminNewData.count+'" title="输出数据 #'+adminNewData.count+'" name="stdout[]" class="btn-info btn-sm" required></p>\
        </div>\
        <div class="col-md-4">\
            <button type="button" class="btn btn-danger btn-sm" onclick="adminDelData('+adminNewData.count+')">\
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>\
            </button>\
        </div>\
    </div>\
</div>';
    $("#test-data").append(new_file);
    $("#input"+adminNewData.count).bootstrapFileInput();
    $("#stdout"+adminNewData.count).bootstrapFileInput();
    adminNewData.count++;
}
function adminDelData(id)
{
    $("#data"+id).remove();
}