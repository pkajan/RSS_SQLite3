function sendID(number) {
    document.getElementById('id').value = number;
    return false;
}

var fileobj;
function upload_file(e) {
    e.preventDefault();
    fileobj = e.dataTransfer.files[0];
    ajax_file_upload(fileobj);
}

function file_explorer() {
    document.getElementById('selectfile').click();
    document.getElementById('selectfile').onchange = function () {
        fileobj = document.getElementById('selectfile').files[0];
        ajax_file_upload(fileobj);
    };
}

function ajax_file_upload(file_obj) {
    if (file_obj != undefined) {
        var form_data = new FormData();
        form_data.append('file', file_obj);
        var sizeLimit = 15728640; //15mb
        if (file_obj.size > sizeLimit) {
            alert("Filesize if too big!\nLimit is " + sizeLimit);
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
                alert(response);
                $('#selectfile').val('');
                location.reload();//reload page
            }
        });
    }
}

function ajax_magnet() {
    var form_data2 = new FormData();
    form_data2.append('title', document.getElementById("title").value);
    form_data2.append('link', document.getElementById("link").value);
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        contentType: false,
        processData: false,
        data: form_data2,
        success: function (response) {
            alert(response);
            location.reload();//reload page
        }
    });
}

function logout() {
    $.ajax({
        type: 'POST',
        url: 'logout.php',
        contentType: false,
        processData: false,
        data: null,
        success: function (response) {
            alert(response);
            location.reload();//reload page
        }
    });
}
