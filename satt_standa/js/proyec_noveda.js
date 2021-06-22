function Details(matrix, tercer, servic) {
    try {
        var url_archiv = document.getElementById('url_archivID');
        var dir_aplica = document.getElementById('dir_aplicaID');
        $("#popupDIV").dialog("open");
        var atributes = "opcion=3&cod_transp=" + tercer + "&pos_matrix=" + matrix + "&cod_tipdes=" + servic;
        AjaxGetData("../" + dir_aplica.value + "/inform/" + url_archiv.value, atributes, 'popupDIV', "post");
    } catch (e) {
        alert("Error -> infoNovedades() " + e.message);
    }
}

function exportarXls() {
    var dir_aplica = document.getElementById('dir_aplicaID');
    top.window.open("../" + dir_aplica.value + "/inform/ajax_proyec_noveda.php?opcion=4&Ajax=on");
}

function Totals(tercer, servic) {
    try {
        var url_archiv = document.getElementById('url_archivID');
        var dir_aplica = document.getElementById('dir_aplicaID');
        $("#popupDIV").dialog("open");
        var atributes = "opcion=5&cod_transp=" + tercer + "&cod_tipdes=" + servic;
        AjaxGetData("../" + dir_aplica.value + "/inform/" + url_archiv.value, atributes, 'popupDIV', "post");
    } catch (e) {
        alert("Error -> infoNovedades() " + e.message);
    }
}