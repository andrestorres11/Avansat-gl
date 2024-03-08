/*
@File: functions.js
@Author: Ing. Christiam Barrera Arango
@Date: 2008-09-16
@Company: Intrared LTDA
*/


//-----------------------------------------------------------------------------------------------------
//@OBTIENE EL VALOR EXTRA DEL SELECT DINAMICO Y LO ALOJA EN EL ELEMENTO HTML INDICADO
function SelectExtra(fTag, fId, fEvent) {
    try {
        if (typeof(fTag) == 'string') {
            var fTag = document.getElementById(fTag);
        }
        var array_extra = fId.split("|");
        var size_array = array_extra.length;
        for (i = 0; i < size_array; i++) {
            var fTarget = document.getElementById(array_extra[i]);
            var fSource = document.getElementById('hselect_' + fTag.name + String(i) + "-" + String(fTag.selectedIndex) + 'ID');
            switch (fEvent) {
                case 'inner':
                    fTarget.innerHTML = fSource.value;
                    break;
                case 'field':
                    fTarget.disabled = false;
                    fTarget.value = fSource.value;
                    break;
            }
        }
    } catch (e) {
        alert("Error " + e.message);
    }
}


//---------------------------------------------------------------------------
//@VALIDA QUE LOS CAPOS NO ESTEN VACIOS PARA AÑADIR NUEVO O ENVIAR FORMULARIO
function ValidateGridRow(fForm, fContainer) {
    //-----------------------------------------------------------------
    //@SE OBTIENE EL FORMULARIO
    if (typeof(fForm) == 'string')
        var fForm = document.getElementById(fForm);
    //-----------------------------------------------------------------
    //@SE OBTIENE EL DIV CONTENEDOR
    if (typeof(fContainer) == 'string')
        var fContainer = document.getElementById(fContainer);
    //----------------------------------------------------------------
    //SE OBTIENE EL TD CONTENEDOR DEL DIV
    var fCell = fContainer.parentNode;
    var fTitles = getFirstChild(getFirstChild(getFirstChild(fContainer)));
    var actual = getFirstChild(fTitles);
    var c = 0;
    while (actual.tagName.toUpperCase() == 'TD') {
        actual = actual.nextSibling;
        //alert( actual.tagName );
        c++;
    }

    //alert( getNextSibling( actual ).tagName );

}

//--------------------------------------------------------------------------
//@ELIMINA LA FILA AL HACER CLICK SOBRE EL BOTON DE ELIMINAR
function DeleteGridRow(fForm, fElement, fHidden) {
    //-----------------------------------------------------------------
    //@SE OBTIENE EL FORMULARIO
    if (typeof(fForm) == 'string')
        var fForm = document.getElementById(fForm);
    //----------------------------------------------------------------
    //SE OBTIENE LA FILA( TR ) SELECCI0NADA
    var fRow = fElement.parentNode.parentNode.parentNode.parentNode;
    //----------------------------------------------------------------
    //SE OBTIENE LA TABLA CONTENEDORA
    var fContainer = fRow.parentNode;
    //alert( fContainer.lastChild.lastChild.id );
    //----------------------------------------------------------------
    //SE LA PRIMERA FILA HIJA DE LA TABLA CONTENEDORA
    var first = getFirstChild(fContainer);
    //----------------------------------------------------------------
    //SE INICIALIZA LA FILA ACTUAL
    var actual = first;
    //----------------------------------------------------------------
    //SE OBTIENE LA REAL FILA DEL EVENTO
    var c = 0;
    while (actual.id != fRow.id) {
        actual = getNextSibling(actual);
        c++;
    }
    //-------------------------------------------------------------------
    //@CALCULA EL SIGUIENTE HERMANO SI EXISTE
    /*
    if ( navigator.appName == "Netscape" )
    { 
      var next = getNextSibling( actual );
    }
    else if ( navigator.appName.indexOf( "Explorer" ) != -1 ) 
    { 
      var next = actual.nextSibling;
    }
    */
    var next = actual.nextSibling;

    //--------------------------------------------------------------------------
    //@VERIFICA SI LA FILA ES LA PRIMERA Y ADEMAS ES LA UNICA PARA NO ELIMINARLA
    if (!next && c == 0)
        return false;
    else {
        //----------------------------------------------------------------------------
        //@SI ES FIREFOX SE CONSTRUYE MATRIZ DE DATOS DE LA GRILLA
        if (navigator.appName == "Netscape") {
            var fMatrix = GetGridData(fForm, fContainer);
        }
        removeElement(actual.id);
    }


    var fSize = ReWriteGrid(fContainer);

    document.getElementById(fHidden).value = Number(fSize) + 1;

    //alert( 'fSize = ' + ( document.getElementById( fHidden ).value ) );

    //----------------------------------------------------------------------------
    //@SI ES FIREFOX SE ASIGNAN A LA GRILLA LOS DATOS DESDE LA MATRIX
    if (navigator.appName == "Netscape") {
        SetGridData(fForm, fContainer, fMatrix);
    }
    return false;
}


//--------------------------------------------------------------------------
//@CREA UNA NUEVA FILA AL HACER CLICK SOBRE EL BOTON NUEVO
function NewGridRow(fForm, fContainer, fHidden) {
    //-----------------------------------------------------------------
    //@SE OBTIENE EL FORMULARIO
    if (typeof(fForm) == 'string')
        var fForm = document.getElementById(fForm);
    //----------------------------------------------------------------
    //ValidateGridRow( fForm, fContainer );
    //----------------------------------------------------------------
    //SE OBTIENE EL DIV CONTENEDOR
    var fContainer = document.getElementById(fContainer);

    if (navigator.appName == "Netscape") {
        var fSpliter = '<table';
    } else if (navigator.appName.indexOf("Explorer") != -1) {
        var fSpliter = '<TABLE';
    }
    var fArray = fContainer.innerHTML.split(fSpliter);

    var html = fSpliter + ' ' + fArray[1];

    //----------------------------------------------------------------------------
    //@SI ES FIREFOX SE CONSTRUYE MATRIZ DE DATOS DE LA GRILLA
    if (navigator.appName == "Netscape") {
        var fMatrix = GetGridData(fForm, fContainer);
    }

    fContainer.innerHTML += html;


    var fSize = ReWriteGrid(fContainer);
    document.getElementById(fHidden).value = Number(fSize) + 1;

    //alert( 'fSize = ' + ( document.getElementById( fHidden ).value ) );


    //----------------------------------------------------------------------------
    //@SI ES FIREFOX SE ASIGNAN A LA GRILLA LOS DATOS DESDE LA MATRIX
    if (navigator.appName == "Netscape") {
        SetGridData(fForm, fContainer, fMatrix);
    }

    ClearGridRowData(fForm, 'index' + fSize);
    return false;
}



//-----------------------------------------------------------------------------
//@REESCRIBE LA GRILLA ORDENANDO SUCESIVAMENTE LOS INDICES DE LOS NAME Y ID
function ReWriteGrid(fContainer) {
    //----------------------------------------------------------------
    //SE OBTIENE EL DIV CONTENEDOR
    if (typeof(fContainer) == 'string')
        var fContainer = document.getElementById(fContainer);
    //----------------------------------------------------------------
    //SE OBTIENE LA PRIMERA FILA HIJA DEL DIV CONTENEDOR
    var first = getFirstChild(fContainer);

    if (navigator.appName == "Netscape") {
        var fSpliter = '<table';
    } else if (navigator.appName.indexOf("Explorer") != -1) {
        var fSpliter = '<TABLE';
    }
    var fArray = fContainer.innerHTML.split(fSpliter);

    var fInner = '';
    fContainer.innerHTML = '';
    for (var i = 1; i < fArray.length; i++) {
        var fIndex = fArray[i].split('|');
        fIndex = String(fIndex[1]);
        fInner = string_replace(fArray[i], fIndex, 'XXX-XXX' + String(i - 1));
        fContainer.innerHTML += fSpliter + ' ' + string_replace(fInner, 'XXX-XXX' + String(i - 1), 'index' + String(i - 1));
    }
    return String(i - 2);
}

//-----------------------------------------------------------------------------
//@ALMACENA EN UNA MATRIZ LOS DATOS DE LA GRILLA( FIX TO FIREFOX )
function GetGridData(fForm, fContainer) {
    //-----------------------------------------------------------------
    //@SE OBTIENE EL FORMULARIO
    if (typeof(fForm) == 'string')
        var fForm = document.getElementById(fForm);
    //----------------------------------------------------------------
    //SE OBTIENE EL DIV CONTENEDOR
    if (typeof(fContainer) == 'string')
        var fContainer = document.getElementById(fContainer);
    //----------------------------------------------------------------
    //SE OBTIENE LA PRIMERA FILA HIJA DEL DIV CONTENEDOR
    var first = getFirstChild(fContainer);

    if (navigator.appName == "Netscape") {
        var fSpliter = '<table';
    } else if (navigator.appName.indexOf("Explorer") != -1) {
        var fSpliter = '<TABLE';
    }
    var fArray = fContainer.innerHTML.split(fSpliter);

    var fInner = '';


    fMatrix = new Array();

    for (var i = 1; i < fArray.length; i++) {
        var fIndex = fArray[i].split('|');
        fIndex = String(fIndex[1]);
        fMatrix[i - 1] = GetGridInputs(fForm, fIndex);
    }
    return fMatrix;
}

//-----------------------------------------------------------------
//OBTIENE LOS DATOS DE LOS INPUTS QUE POSEEN EL INDEX
function GetGridInputs(fForm, fIndex) {
    var fElements = fForm.elements;
    var fArrayRow = new Array();
    var c = 0;
    for (var i = 0; i < fElements.length; i++) {
        if (fElements[i].id.indexOf(fIndex) != -1) {
            id_obtenido = fElements[i].id;
            if (fElements[i].type == 'text') {
                var fItem = document.getElementById(fElements[i].id).value;
                fArrayRow[c] = fItem;
                c++;
            }
        }
    }
    return fArrayRow;
}

//-----------------------------------------------------------------------------
//@ASIGNA DESDE UNA MATRIZ LOS DATOS DE LA GRILLA( FIX TO FIREFOX )
function SetGridData(fForm, fContainer, fMatrix) {
    //-----------------------------------------------------------------
    //@SE OBTIENE EL FORMULARIO
    if (typeof(fForm) == 'string')
        var fForm = document.getElementById(fForm);
    //----------------------------------------------------------------
    //SE OBTIENE EL DIV CONTENEDOR
    if (typeof(fContainer) == 'string')
        var fContainer = document.getElementById(fContainer);
    //----------------------------------------------------------------
    //SE OBTIENE LA PRIMERA FILA HIJA DEL DIV CONTENEDOR
    var first = getFirstChild(fContainer);

    if (navigator.appName == "Netscape") {
        var fSpliter = '<table';
    } else if (navigator.appName.indexOf("Explorer") != -1) {
        var fSpliter = '<TABLE';
    }
    var fArray = fContainer.innerHTML.split(fSpliter);

    var fInner = '';

    for (var i = 1; i < fArray.length; i++) {
        var fIndex = fArray[i].split('|');
        fIndex = String(fIndex[1]);
        //alert( fIndex );
        //alert( fIndex );
        SetGridInputs(fForm, fIndex, i - 1, fMatrix);
        //fMatrix[i-1] = GetGridInputs( fForm, fIndex );
    }
    return false;
}

//-----------------------------------------------------------------
//ASIGNA LOS DATOS DE LOS INPUTS EN LA FILA SEGUN EL INDEX
function SetGridInputs(fForm, fIndex, fRow, fMatrix) {
    var fElements = fForm.elements;
    var fArrayRow = new Array();
    var c = 0;
    for (var i = 0; i < fElements.length; i++) {
        if (fElements[i].id.indexOf(fIndex) != -1) {
            id_obtenido = fElements[i].id;
            if (fElements[i].type == 'text') {
                //alert( fMatrix[fRow] );
                //alert( "fMatrix["+String( fRow )+"]["+String( c )+"] = " + fMatrix[fRow][c] );
                var fItem = document.getElementById(fElements[i].id);
                fItem.value = fMatrix[fRow][c];

                c++;
            }
        }
    }
    return fArrayRow;
}


//--------------------------------------------------------------------------------------
//@LIMPIA LOS VALORES DE UNA FILA DE LA GRILLA
function ClearGridRowData(fForm, fIndex) {
    var fElements = fForm.elements;
    for (var i = 0; i < fElements.length; i++) {
        if (fElements[i].id.indexOf(fIndex) != -1) {
            //alert( fElements[i].id + ' = ' + fElements[i].value );
            if (fElements[i].type == 'text') {
                var fItem = document.getElementById(fElements[i].id);
                fItem.value = '';
            }
        }
    }
    return false;
}


//--------------------------------------------------
//OBTIENE EL NODO HERMANO DEL ELEMENTO HTML INDICADO
function getNextSibling(startBrother) {
    endBrother = startBrother.nextSibling;
    while (endBrother.nodeType != 1) {
        endBrother = endBrother.nextSibling;
    }
    return endBrother;
}

//-------------------------------------------------------
//OBTIENE EL NODO PRIMER HIJO DEL ELEMENTO HTML INDICADO  
function getFirstChild(elm) {
    if (!elm.childNodes.length) {
        return;
    }
    var children = elm.childNodes.length;
    for (var i = 0; i <= children; ++i) {
        if (elm.childNodes[i].nodeType == 1) {
            return elm.childNodes[i];
        }
    }
    return;
}

//--------------------------------------------------------------------------------
//ELMINA UN ELEMENTO HTML
function DeleteElement(fId) {
    var element = document.getElementById(fId);
    if (!element) {
        alert("El elemento selecionado no existe");
    } else {
        var padre = element.parentNode;
        padre.removeChild(element);
    }
}



function removeElement(id) {
    var Node = document.getElementById(id);
    Node.parentNode.removeChild(Node);
}


//--------------------------------------------------------------------------------------------------------------
//RETORNA UNA MATRIZ CON LOS AÑOS COMPRENDIDOS ENTRE LOS LIMITES INGRESADOS APTA PARA LOS OPTIONS DE LOS SELECTS
function ArrayYears(fElement, fYear1, fYear2) {
    try {
        fElement = typeof(fElement) == 'string' ? document.getElementById(fElement) : fElement;
        fElement.options.length = 0;

        var fToday = new Date();
        var fYear = fToday.getYear();
        fYear1 = !fYear1 ? fYear - 5 : fYear1;
        fYear2 = !fYear2 ? fYear - 0 : fYear2;
        var a = 0;
        for (var y = Number(fYear1); y <= Number(fYear2); y++) {
            if (a == 0) {
                fElement.options[0] = new Option('--', '');
                a++;
            }
            fElement.options[a] = new Option(y, y);
            a++;
        }
    } catch (e) {
        alert(e.message);
    }
}
//--------------------------------------------------------------------------------------------------------------
//RETORNA UNA MATRIZ CON LOS MESES COMPRENDIDOS ENTRE LOS LIMITES INGRESADOS APTA PARA LOS OPTIONS DE LOS SELECTS
function ArrayMonths(fElement, fMonth1, fMonth2) {
    try {
        fElement = typeof(fElement) == 'string' ? document.getElementById(fElement) : fElement;
        fElement.options.length = 0;

        fMonth1 = !fMonth1 ? 1 : fMonth1;
        fMonth2 = !fMonth2 ? 12 : fMonth2;
        var a = 0;
        for (var m = Number(fMonth1); m <= Number(fMonth2); m++) {
            var value = m < 10 ? '0' + String(m) : String(m);
            switch (value) {
                case '01':
                    var label = 'Enero';
                    break;
                case '02':
                    var label = 'Febrero';
                    break;
                case '03':
                    var label = 'Marzo';
                    break;
                case '04':
                    var label = 'Abril';
                    break;
                case '05':
                    var label = 'Mayo';
                    break;
                case '06':
                    var label = 'Junio';
                    break;
                case '07':
                    var label = 'Julio';
                    break;
                case '08':
                    var label = 'Agosto';
                    break;
                case '09':
                    var label = 'Septiembre';
                    break;
                case '10':
                    var label = 'Octubre';
                    break;
                case '11':
                    var label = 'Noviembre';
                    break;
                case '12':
                    var label = 'Diciembre';
                    break;
            }
            if (a == 0) {
                fElement.options[0] = new Option('--', '');
                a++;
            }
            fElement.options[a] = new Option(label, value);
            a++;
        }
    } catch (e) {
        alert(e.message);
    }
}

//alert( ArrayMonths( 4, 10 ) );


function BlurNumeric(fInput, fOperator) {
    if (!fInput.value) {
        return false;
    }
    var fVal = StringReplace('.', StringReplace(',', fInput.value));
    if (fOperator == '-') {
        if (!IsNumeric(fVal.slice(1, fVal.length)) && (!IsNumeric(fVal.slice(0, 1)) || fVal.slice(0, 1) != '-')) {
            fInput.value = '';
            fInput.focus();
            return false;
        } else {
            return true;
        }
    } else {
        if (!IsNumeric(fVal)) {
            fInput.value = '';
            fInput.focus();
            return false;
        } else {
            return true;
        }
    }
}

function VerifyNum(nit, inputId) {
    var input = document.getElementById(inputId);

    var ceros = nit;

    while (ceros.length < 15) {
        ceros = "0" + ceros;
    }

    li_peso = new Array();
    li_peso[0] = 71;
    li_peso[1] = 67;
    li_peso[2] = 59;
    li_peso[3] = 53;
    li_peso[4] = 47;
    li_peso[5] = 43;
    li_peso[6] = 41;
    li_peso[7] = 37;
    li_peso[8] = 29;
    li_peso[9] = 23;
    li_peso[10] = 19;
    li_peso[11] = 17;
    li_peso[12] = 13;
    li_peso[13] = 7;
    li_peso[14] = 3;

    ls_str_nit = ceros + nit.value;
    li_suma = 0;
    for (i = 0; i < 15; i++) {
        li_suma += ls_str_nit.substring(i, i + 1) * li_peso[i];
    }
    digito_chequeo = li_suma % 11;


    if (digito_chequeo >= 2)
        digito_chequeo = 11 - digito_chequeo;

    input.value = digito_chequeo;

}
/*
VerifyNum( "3155985", "prueba1ID" );
VerifyNum( "14699878", "prueba2ID" );
VerifyNum( "94070312", "prueba3ID" );
VerifyNum( "52885912", "prueba4ID" );
VerifyNum( "79939977", "prueba5ID" );
*/
//--------------------------------------------------------------------------------------------
//@EXPORTA A EXCEL UNA TABLA HTML.
function exportToXL(eSrc) {
    eSrc = document.getElementById(eSrc);

    var oExcel;
    var oExcelSheet;
    var oWkBooks;
    var cols;
    oExcel = new ActiveXObject('Excel.Application');
    oWkBooks = oExcel.Workbooks.Add;
    oExcelSheet = oWkBooks.Worksheets(1);
    oExcelSheet.Activate();

    if (eSrc.tagName.toLowerCase() != 'table') {
        alert('No ha sido posible exportar la tabla a Excell');
        return false;
    }
    cols = Math.ceil(eSrc.cells.length / eSrc.rows.length);
    for (var i = 0; i < eSrc.cells.length; i++) {
        var c, r;
        r = Math.ceil((i + 1) / cols);
        c = (i + 1) - ((r - 1) * cols);
        if (eSrc.cells(i).tagName == 'TH') {
            oExcel.ActiveSheet.Cells(r, c).Font.Bold = true;
            oExcel.ActiveSheet.Cells(r, c).Interior.Color = 14474460;
        }
        if (eSrc.cells(i).childNodes.length > 0 && eSrc.cells(i).childNodes(0).tagName == "B")
            oExcel.ActiveSheet.Cells(r, c).Font.Bold = true;
        oExcel.ActiveSheet.Cells(r, c).Value = eSrc.cells(i).innerText;
    }
    oExcelSheet.Application.Visible = true;
}


/*
 *@Función que Oculta y Muestra un Frame
 */
function DisplayFrame(fFrameID, fCols) {
    var fFrameset = parent.document.getElementById(frameID);
    if (fFrameset.cols == fCols)
        fFrameset.cols = "0,*";
    else
        fFrameset.cols = fCols;
}


//-------------------------------------------------------------------------------------------------
function AjaxLoader(fDisplay, fParent) {
    if (typeof(fParent) == 'string') {
        var fAjax = parent.document.getElementById("AjaxLoaderID");
        var fFrameset = parent.parent.document.getElementById("id_frameset2");
    } else {
        var fAjax = document.getElementById("AjaxLoaderID");
        var fFrameset = parent.document.getElementById("id_frameset2");
    }
    var left = fFrameset.cols == '0,*' || fFrameset.cols == '0, *' ? Math.round(screen.width / 2.2) : Math.round(screen.width / 3);
    var top = Math.round(screen.height / 3);
    fAjax.style.left = String(left) + "px";
    fAjax.style.top = String(top) + "px";
    fAjax.style.display = fDisplay;
}
//------------------------------------------------------------------------------------------------------

/*
 *@Función fija la clase CSS de un Tag HTML
 */
function SetClassName(fTag, fClass) {
    fTag.className = fClass;
}

/*
 *@Función fija el foco sobre un Elemento HTML
 */
function FocusTag(fTag) {
    fTag.focus();
}


function Money(value) {
    value = String(value);
    var size = value.length;
    if (size > 3) {
        var str = "";
        var rev = StrRev(value);
        var n = 0;
        while (n < size) {
            str += rev.slice(n, n + 1);
            if ((n + 1) % 3 == 0 && (n + 1) < size) {
                str += ".";
            }
            n++;
        }
        return StrRev(str);
    } else
        return value;
}



function Moneda(value) {
    value = String(value);
    var size = value.length;
    if (size > 3) {
        var str = "";
        var rev = StrRev(value);
        var n = 0;
        while (n < size) {
            str += rev.slice(n, n + 1);
            if ((n + 1) % 3 == 0 && (n + 1) < size) {
                str += ",";
            }
            n++;
        }
        return StrRev(str);
    } else
        return value;
}

/*
 *@Función que restringe el ingreso de caracteres a sólo númericos
 */
function NumericInput(fEvent) {
    var fKeyPressed = (fEvent.which) ? fEvent.which : fEvent.keyCode;
    return !(fKeyPressed > 31 && (fKeyPressed < 48 || fKeyPressed > 57));
}

/*
 *@Función que determina si una cadena es númerica. Retorno Booleano.
 */
function IsNumeric(fText) {
    var fChars = "0123456789.";
    var fBool = true;
    var Char;
    for (i = 0; i < fText.length && fBool == true; i++) {
        Char = fText.charAt(i);
        if (fChars.indexOf(Char) == -1) {
            fBool = false;
        }
    }
    return fBool;
}

/*
 *@Función que limpia el valor de un Elemento HTML en caso de no ser númerico.
 */
function FormatNumericInput(fInput) {
    if (!fInput.value)
        return false;
    else {
        if (!IsNumeric(fInput.value) || fInput.value.slice(fInput.value.length - 1, fInput.value.length) == ".")
            fInput.value = "";
    }
}

/*
 *@Función que restringe el ingreso de caracteres a sólo decimales
 */
function DecimalInput(fEvent) {
    var fKeyPressed = (fEvent.which) ? fEvent.which : fEvent.keyCode;
    return !(fKeyPressed != 46 && (fKeyPressed > 31 && (fKeyPressed < 48 || fKeyPressed > 57)));
}

/*
 *@Función que restringe el ingreso de varios puntos en una cadena. Invocada en el evento HTML onkeydown
 */
function DecimalFormat(fInput) {
    var fStr = fInput.value;
    var fChar = fStr.substr(fStr.length - 1, fStr.length);
    if (fStr.length == 1) {
        if (fStr.slice(0, 1) == ".")
            return fInput.value = "";
    }
    if (fStr.length > 1) {
        if ((fStr.slice(fStr.length - 1, fStr.length) == "." && fStr.slice(fStr.length - 2, fStr.length - 1) == ".") ||
            (fStr.substr(0, fStr.length - 1).indexOf(".") != -1 && fStr.substr(fStr.length - 1, fStr.length) == ".")) {
            return fInput.value = fInput.value.slice(0, fInput.value.length - 1);
        }
    }
}

/*
 *@Función que restringe el ingreso de caracteres a formato placa Colombiana (AAA000) o Venezolana (AA0000)
 */
function PlacaInput(fInput) {
    fInput.value = fInput.value.toUpperCase();
    var fPlaca = fInput.value;
    var fSize = fPlaca.length;
    var fChar = fPlaca.substring(fSize - 1, fSize);
    if (fChar == 0 || fChar == 1 || fChar == 2 || fChar == 3 || fChar == 4 || fChar == 5 || fChar == 6 || fChar == 7 || fChar == 8 || fChar == 9) {
        if (fSize < 3) {
            fPlaca = fPlaca.substring(0, fSize - 1);
            fInput.value = fPlaca;
        }
    } else {
        if (fSize > 3) {
            fPlaca = fPlaca.substring(0, fSize - 1);
            fInput.value = fPlaca;
        }
    }
    return false;
}

/*
 *@Función que limpia el valor de un Elemento HTML en caso de no estar en formato Placa.
 */
function FormatPlacaInput(fInput) {
    var fPlaca = fInput.value;
    if (!fPlaca) return false;
    var fFormat1 = /[a-zA-Z]{3}[0-9]{3}/;
    var fFormat2 = /[a-zA-Z]{2}[0-9]{4}/;
    if (!fFormat1.test(fPlaca) && !fFormat2.test(fPlaca)) {
        fInput.value = "";
    }
    return false;
}

function CalendarFormat(input) {
    var size = input.value.length;
    var now = new Date();
    var day = now.getDate();
    if (day < 10) day = "0" + day;
    var month = now.getMonth() + 1;
    if (month < 10) month = "0" + month;
    var year = now.getFullYear();
    if (size == 4 || size == 7 || size == 10) {
        if (size == 4 && input.value < (year - 3))
            input.value = year;
        if (size == 7 && input.value.substring(5, 7) == "00")
            input.value = input.value.substring(0, 4) + "-" + month;
        if (size == 10 && input.value.substring(8, 10) == "00")
            input.value = input.value.substring(0, 4) + "-" + input.value.substring(5, 7) + "-" + day;
        if (size != 10)
            input.value += "-";
    }
    var nen = input.value.substring(0, 4);
    if (nen > (year + 3))
        input.value = year + "-";
    var mes = input.value.substring(5, 7);
    if (mes > 12) {
        input.value = input.value.substring(0, 5) + month + "-";
    } else {
        var limit = "";
        switch (mes) {
            case "01":
                limit = 31;
                break;
            case "02":
                if (nen % 4 == 0) limit = "29";
                else limit = "28";
                break;
            case "03":
                limit = "31";
                break;
            case "04":
                limit = "30";
                break;
            case "05":
                limit = "31";
                break;
            case "06":
                limit = "30";
                break;
            case "07":
                limit = "31";
                break;
            case "08":
                limit = "31";
                break;
            case "09":
                limit = "30";
                break;
            case "10":
                limit = "31";
                break;
            case "11":
                limit = "30";
                break;
            case "12":
                limit = "31";
                break;
        }
    }
    if (input.value.length == 10)
        if (input.value.substring(8, 10) > limit)
            input.value = input.value.substring(0, 8) + day;



}

function DateInput(fInput) {
    var fSize = fInput.value.length;
    var fNow = new Date();
    var fDay = fNow.getDate();
    if (fDay < 10) fDay = "0" + fDay;
    var fMonth = fNow.getMonth() + 1;
    if (fMonth < 10) fMonth = "0" + fMonth;
    var fYear = fNow.getFullYear();
    if (fSize == 4 || fSize == 7 || fSize == 10) {
        if (fSize == 4 && fInput.value < (fYear - 3))
            fInput.value = fYear;
        if (fSize == 7 && fInput.value.substring(5, 7) == "00")
            fInput.value = fInput.value.substring(0, 4) + "-" + fMonth;
        if (fSize == 10 && fInput.value.substring(8, 10) == "00")
            fInput.value = fInput.value.substring(0, 4) + "-" + fInput.value.substring(5, 7) + "-" + fDay;
        if (fSize != 10)
            fInput.value += "-";
    }
    var fNen = fInput.value.substring(0, 4);
    if (fNen > (fYear + 5))
        fInput.value = fYear + "-";
    var fMes = fInput.value.substring(5, 7);
    if (fMes > 12) {
        fInput.value = fInput.value.substring(0, 5) + fMonth + "-";
    } else {
        var fLimit = "";
        switch (fMes) {
            case "01":
                fLimit = 31;
                break;
            case "02":
                if (fNen % 4 == 0) fLimit = "29";
                else fLimit = "28";
                break;
            case "03":
                fLimit = "31";
                break;
            case "04":
                fLimit = "30";
                break;
            case "05":
                fLimit = "31";
                break;
            case "06":
                fLimit = "30";
                break;
            case "07":
                fLimit = "31";
                break;
            case "08":
                fLimit = "31";
                break;
            case "09":
                fLimit = "30";
                break;
            case "10":
                fLimit = "31";
                break;
            case "11":
                fLimit = "30";
                break;
            case "12":
                fLimit = "31";
                break;
        }
    }

    //     fInput.value = fInput.value.substring( 0, 5 )+fMonth+"-"+fDay;

    if (fInput.value.length == 10)
        if (fInput.value.substring(8, 10) > fLimit)
            fInput.value = fInput.value.substring(0, 8) + fDay;
}


function IsDate(fString) {
    if (!fString)
        return false;
    var fSize = fString.length;
    if (fSize != 10)
        return false;
    else {
        if (!IsNumeric(fString.substring(0, 4)))
            return false;
        if (fString.substring(4, 5) != "-")
            return false;
        if (!IsNumeric(fString.substring(5, 7)))
            return false;
        if (fString.substring(7, 8) != "-")
            return false;
        if (!IsNumeric(fString.substring(8, 10)))
            return false;
        return true;
    }
}

function StrRev(fStr) {
    var i = fStr.length;
    var fRev = "";
    while (i >= 0) {
        fRev += fStr.slice(i - 1, i);
        i--;
    }
    return fRev;
}

/*
function MoneyInput( fInput )    
{
    var fSize = fInput.value.length;
    if ( fSize > 3 )
    {
        var fStr = "";
        var fRev = StrRev( value );
        var n = 0;
        while( n < fSize )
        {
            fStr += fRev.slice( n, n + 1 );
            if ( ( n + 1 ) % 3 == 0 && ( n + 1 ) < fSize )
            {
                fStr += ".";
            }
            n++;
        }
        return StrRev( fStr );
    }
    else
       return fInput.value;
}
*/

/*
function NegativeMoneyInput( fInput )    
{
    var fSize = fInput.value.length;
    if ( fSize > 3 )
    {
        var fStr = "";
        var fRev = StrRev( value );
        var n = 0;
        while( n < fSize )
        {
            fStr += fRev.slice( n, n + 1 );
            if ( ( n + 1 ) % 3 == 0 && ( n + 1 ) < fSize )
            {
                fStr += ".";
            }
            n++;
        }
        return StrRev( fStr );
    }
    else
       return fInput.value;
}
*/


/*
 *@Función que limpia el valor de un Elemento HTML en caso de no ser númerico.
 */
function FormatDateInput(fInput) {
    if (!fInput.value)
        return false;
    else {
        if (!IsDate(fInput.value))
            fInput.value = "";
    }
}


//---------------------------------------------------------------------------------------------------

function MoneyInput(fInput, fChar) {
    var fModel = /[\*,\+,\(,\),\?,\\,\$,\[,\],\^]/;
    var fValue = fInput.value;
    var fSize = fValue.length;
    var fBool = true;
    if (isNaN(fChar) || fModel.test(fChar) == true) {
        if (fModel.test(fChar) == true) {
            fChar = "\\" + fChar;
        }
        var fCarcter = new RegExp(fChar, "g");
        fValue = fValue.replace(fCarcter, "");
        fInput.value = fValue;
        fBool = false;
    } else {
        var fNums = new Array();
        fCont = 0;
        for (m = 0; m < fSize; m++) {
            if (fValue.charAt(m) == "." || fValue.charAt(m) == " ") {
                continue;
            } else {
                fNums[fCont] = fValue.charAt(m);
                fCont++;
            }
        }
    }
    var fCad1 = "",
        fCad2 = "",
        fThree = 0;
    if (fSize > 3 && fBool == true) {
        for (var k = fNums.length - 1; k >= 0; k--) {
            fCad1 = fNums[k];
            fCad2 = fCad1 + fCad2;
            fThree++;
            if ((fThree % 3) == 0) {
                if (k != 0) {
                    fCad2 = "." + fCad2;
                }
            }
        }
        fInput.value = fCad2;
    }
}

/*
function ReplaceAlphas( fString )
{
  var fSize  = fString.length;
  for ( var i = 0; i < fSize; i++ )
  {
    var fChar = length < 2 ? fStringfString.slice( i - 1, i );
    alert( fChar );
  }
} 

alert( ReplaceAlphas( '356u89' ) );
*/


function StringReplace(spliter, string) {
    var array = string.split(spliter);
    if (array.length > 0) {
        var size = array.length;
        var str = "";
        for (var i = 0; i < size; i++) {
            str += array[i];
        }
        return str;
    } else
        return string;
}

//----------------------------------------------------------
//@REEMPLAZA EN UNA CADENA LAS APARICIONES DE fOld por fNew
function string_replace(fString, fOld, fNew) {
    while (fString.indexOf(fOld) != -1) {
        fString = fString.replace(fOld, fNew);
    }
    return fString;
}

//alert( string_replace( 'fdgsdrtdwe54343435tergf3211343245efseq3464yge5345645terw42536345', '5', 'KATY' ) );


function SetPoints(fString) {
    fString = String(fString);
    if (fString.charAt(0) == '-') {
        fString = fString.slice(1, fString.length);
        var fWay = '-';
    } else {
        var fWay = '';
    }
    fString = StrRev(StringReplace('.', fString));
    var fSize = fString.length;
    var fTemp = '';

    var p = 0;
    for (var c = 0; c < fSize; c++) {
        /*
        if ( p == 0 )
        {
          fTemp += c % 2 == 0 ? '.'+fString.charAt( c ) : fString.charAt( c );
          p++;
        }
        else
        {
        */
        fTemp += c % 3 == 0 ? '.' + fString.charAt(c) : fString.charAt(c);
        //}
    }

    fTemp = fTemp.charAt(fTemp.length - 1) == '.' ? fTemp.slice(0, fTemp.length - 1) : fTemp;

    fTemp = StrRev(fTemp);

    return fWay += fTemp.charAt(fTemp.length - 1) == '.' ? fTemp.slice(0, fTemp.length - 1) : fTemp;
}

//alert( SetPoints( '1200000598006' ) );

/*
var exp = /^-([0-9][0-9][0-9]\.)*([0-9][0-9][0-9])$/;
*/


function NegativeMoneyInput(fEvent, fInput) {
    //alert( fInput.value );

    var fString = fInput.value;
    var fSize = fString.length;

    var fKeyPressed = (fEvent.which) ? fEvent.which : fEvent.keyCode;

    //alert( fKeyPressed ); 

    //alert( 'antes: '+fInput.value );
    fInput.value = SetPoints(fInput.value);
    //alert( 'despues: '+fInput.value );  
    return ((fKeyPressed == 45 && fSize < 1) || (fKeyPressed >= 48 && fKeyPressed <= 57));

    //return !( fKeyPressed > 31 && ( fKeyPressed < 48 || fKeyPressed > 57 ) );   

    /*
    try
    {
      var fString  = fInput.value;
      var fSize  = fString.length;
      var negative = false;  
      var fdSize = fSize;
      var maxlength = Number( fInput.getAttribute("maxlength") );
          
      for(var j = 0 ; j < fSize ; j++)
      {
        if( j == 0 && fString.charAt(j) == "-")
        {
          negative = true;
          fString = fString.slice( 1 , fSize );
          fdSize--;
        }  
        if(fString.charAt(j) == ".")
          {
            fdSize--;
          }
      }  
      
      if( fdSize!= 0 && fdSize % 3 == 0 && fSize < maxlength )
      {
         fString += ".";
      }         
      
      if(negative)
      {
        fString = "-"+fString;
      } 
      return fString;
    }
    catch( e )
    {
      alert( e.message );
    }
    */
}


function CharsInput(fTag, fSize) {
    if (fTag.value.length != fSize) {
        fTag.value = "";
        //alert( "Este campo debe tener "+String( fSize )+" carácteres exáctamente." );
    } else
        return false;
}

//---------------------------------------------------------------------------------------------------

function LockAplication(action, message = "") {
    try {
        var objCentral = parent.centralFrame.document.getElementById("TransparencyDIV");
        if (objCentral == null) {

            var div = document.createElement("div");
            div.setAttribute("id", "TransparencyDIV");
            div.innerHTML = "<div style='position: absolute; transform: translate(-50%, -50%); top: 50%; left: 50%; border: 1px solid #45930b; background: #4ca20b; color: white; font-weight: bold; padding: 10px; border-radius: 15px; display: flex;'>" + message + "</div>";
            parent.centralFrame.document.getElementsByTagName("body")[0].appendChild(div);

            objCentral = parent.centralFrame.document.getElementById("TransparencyDIV");

        }
        //var objMenu    = parent.menuFrame.document.getElementById("TransparencyDIV");
        //var objHeader = parent.headerFrame.document.getElementById("TransparencyDIV");
        if (action == "lock") {
            objCentral.style.left = "0";
            objCentral.style.top = "0";
            objCentral.style.width = "100%";
            objCentral.style.height = "100%";
            objCentral.style.display = "block";
            objCentral.style.position = "fixed";
            objCentral.style.backgroundColor = "#cccc";
            objCentral.style.zIndex = "2000";
            //objMenu.style.visibility = "visible";
            //7  objHeader.style.visibility = "visible";
        } else {
            objCentral.style.display = "none";
            // objMenu.style.visibility = "hidden";
            //  objHeader.style.visibility = "hidden";
        }
    } catch (e) {
        console.log(e);
    }

}


function ProgressBar(action) {
    var objCentral = document.getElementById("TransparencyDIV");
    var objMenu = parent.menuFrame.document.getElementById("TransparencyDIV");
    var objHeader = parent.headerFrame.document.getElementById("TransparencyDIV");
    if (action == "lock") {
        objCentral.style.height = "100%";
        objCentral.style.visibility = "visible";
        objMenu.style.visibility = "visible";
        objHeader.style.visibility = "visible";

        /*
        var html  = "";
        html += '<script type="text/javascript">';
        html += 'var bar=createBar(160,15,"white",1,"black","#333366",85,7,3,"");';
        html += 'bar.showBar();';
        html += '</script>';
            
            
        objCentral.innerHTML = html;
        */

        //createBar(160,15,"white",1,"black","#333366",85,7,3,"");

        bar.showBar();

    } else {
        objCentral.style.visibility = "hidden";
        objMenu.style.visibility = "hidden";
        objHeader.style.visibility = "hidden";

        bar.hideBar();
    }
}


function DisplayPopup(display) {
    var div = document.getElementById("PopupDIV");
    if (display != "none") {
        LockAplication("lock");

        /*
        var width = Math.round( screen.width / 1.5 );
          var height = Math.round( screen.height / 2 ); 
        var left = "100";
        var top = "100";
        */

        var width = Math.round(screen.width / 1.4);
        var height = Math.round(screen.height / 2);
        var left = "50";
        var top = "50";

        div.style.left = String(left) + "px";
        div.style.top = String(top) + "px";
        div.style.width = String(width) + "px";
        div.style.height = String(height) + "px";
        div.style.display = "block";
    } else {
        div.innerHTML = "";
        div.style.left = "0px";
        div.style.top = "0px";
        div.style.width = "0px";
        div.style.height = "0px";
        div.style.display = "none";
        LockAplication("unlock");
    }
}


function trim(cadena) {
    for (i = 0; i < cadena.length;) {
        if (cadena.charAt(i) == " ")
            cadena = cadena.substring(i + 1, cadena.length);
        else
            break;
    }

    for (i = cadena.length - 1; i >= 0; i = cadena.length - 1) {
        if (cadena.charAt(i) == " ")
            cadena = cadena.substring(0, i);
        else
            break;
    }

    return cadena;
}


function EnableForm(id, ids) {
    ids = trim(ids);
    if (ids)
        var ids = ids.split(",");
    var elements = document.getElementById(id).elements;
    var size = elements.length;
    for (var i = 0; i < size; i++) {
        if (!in_array(elements[i].id, ids))
            elements[i].disabled = false;
    }
}


function DisableForm(id, ids) {
    try {
        ids = trim(ids);
        if (ids)
            var ids = ids.split(",");
        var elements = document.getElementById(id).elements;
        var size = elements.length;
        for (var i = 0; i < size; i++) {
            if (elements[i].type != 'hidden' && !in_array(elements[i].id, ids))
                elements[i].disabled = true;
            /*
            for ( var j=0; j<ids.length; j++ )  
            {
      
                if ( elements[i].id == ids[j] ) 
                {
                    elements[i].disabled = false;
                }  
            }    
            */
        }
    } catch (exception) {
        alert("Error FormDisable " + exception);
    }
}

/*
function ReadonlyForm( id, bool ) 
{
  var elements = document.getElementById( id ).elements;
  var size = elements.length;
  for( var i=0; i< size; i++ )   
  {
    elements[i].readOnly = bool == 'true' ? true : else;  
  }
}
*/

function ClearForm(id, ids) {
    ids = trim(ids);
    if (ids)
        var ids = ids.split(",");
    var elements = document.getElementById(id).elements;
    var size = elements.length;
    for (var i = 0; i < size; i++) {
        //alert( elements[i].name );
        if (elements[i].type != 'button' && elements[i].type != 'submit' && elements[i].type != 'hidden' && !in_array(elements[i].value, ids))
            elements[i].value = '';
    }
}


function CheckForm(id, ids) {
    ids = trim(ids);
    if (ids)
        var ids = ids.split(",");
    var elements = document.getElementById(id).elements;
    var size = elements.length;
    for (var i = 0; i < size; i++) {
        if (elements[i].type == 'radio' || elements[i].type == 'checkbox')
            elements[i].checked = true;
    }
}

function UncheckForm(id, ids) {
    ids = trim(ids);
    if (ids)
        var ids = ids.split(",");
    var elements = document.getElementById(id).elements;
    var size = elements.length;
    for (var i = 0; i < size; i++) {
        if (elements[i].type == 'radio' || elements[i].type == 'checkbox') {
            //alert( elements[i].type );
            elements[i].checked = false;
        }
    }
}


function in_array(fItem, fArray) {
    for (var i = 0; i < fArray.length; i++) {
        if (fItem == fArray[i])
            return true;
    }
    return false;
}


//-----------------------------------------------------------------------------------------
//Nota: Debe enviarse un campo "OFFSET" oculto para poder leer el límite offset
function LimitDays(YearID, MonthID, DayID, Offset) {
    if (!Offset) {
        Offset = 1;
    } else {
        Offset = document.getElementById(Offset).value;
    }
    var fLimit = 0;
    var fYear = document.getElementById(YearID);
    var fMonth = document.getElementById(MonthID);
    var fDay = document.getElementById(DayID);

    if (!fYear.value || !fMonth.value) {
        fDay.value = '';
        fDay.disabled = true;
        return false;
    }

    if (!fDay.value) {
        return false;
    }

    fLimit = GetMonthDays(fYear.value, fMonth.value);

    if (fDay.value == '00') {
        fDay.value = '0';
        return false;
    }
    if ((Number(fDay.value) < Offset && fDay.value.length == 2) || Number(fDay.value) > fLimit || !IsNumeric(fDay.value)) {
        fDay.value = '';
        return false;
    }
}



function GetMonthDays(fYear, fMonth) {
    var fLimit = 0;
    switch (String(fMonth)) {
        case '01':
            fLimit = 31;
            break;
        case '02':
            fLimit = Number(fYear) % 4 == 0 ? 29 : 28;
            break;
        case '03':
            fLimit = 31;
            break;
        case '04':
            fLimit = 30;
            break;
        case '05':
            fLimit = 31;
            break;
        case '06':
            fLimit = 30;
            break;
        case '07':
            fLimit = 31;
            break;
        case '08':
            fLimit = 31;
            break;
        case '09':
            fLimit = 30;
            break;
        case '10':
            fLimit = 31;
            break;
        case '11':
            fLimit = 30;
            break;
        case '12':
            fLimit = 31;
            break;
    }
    //alert( "fLimit = "+fLimit );
    return fLimit;
}


function GetLabel(fId) {
    try {
        var fObject = document.getElementById(fId);
        if (fObject.length > 0) {
            var fIndex = fObject.selectedIndex;
            var fLabel = fObject.options.item(fIndex).innerHTML;
            return fLabel;
        } else
            return '';
    } catch (e) {
        alert("Error GetLabel( " + fId + " ) " + e.message);
    }
}

//------------------------------------------------------------
//@FUNCION PARA EL DIGITO DE VERIFICACION DE UN TERCERO
function VerifyNum(nit, inputId) {
    try {
        var input = document.getElementById(inputId);
        var ceros = nit;
        while (ceros.length < 15) {
            ceros = "0" + ceros;
        }
        var li_peso = new Array();
        li_peso[0] = 71;
        li_peso[1] = 67;
        li_peso[2] = 59;
        li_peso[3] = 53;
        li_peso[4] = 47;
        li_peso[5] = 43;
        li_peso[6] = 41;
        li_peso[7] = 37;
        li_peso[8] = 29;
        li_peso[9] = 23;
        li_peso[10] = 19;
        li_peso[11] = 17;
        li_peso[12] = 13;
        li_peso[13] = 7;
        li_peso[14] = 3;

        ls_str_nit = ceros + nit.value;
        li_suma = 0;
        for (i = 0; i < 15; i++) {
            li_suma += ls_str_nit.substring(i, i + 1) * li_peso[i];
        }
        digito_chequeo = li_suma % 11;

        if (digito_chequeo >= 2)
            digito_chequeo = 11 - digito_chequeo;

        input.value = digito_chequeo;
    } catch (e) {
        alert('Error Función VerifyNum : ' + e.message);
    }
}


function OnchangeHiddens(fInput, fTarget) {
    try {
        var i = fInput.selectedIndex;
        if (document.getElementById('hidden_' + String(fInput.name) + '_' + String(i) + 'ID')) {
            var fHidden = document.getElementById('hidden_' + String(fInput.name) + '_' + String(i) + 'ID');
            if (document.getElementById(fTarget)) {
                var fTarget = document.getElementById(fTarget);
                if (fTarget.tagName.toLowerCase() == 'input') {
                    fTarget.value = fHidden.value;
                } else {
                    fTarget.innerHTML = fHidden.value;
                }
            }
        }
    } catch (e) {
        alert('Error Function OnchangeHiddens : ' + e.message);
    }
}



function ResizeInput(fId, fLimit) {
    try {
        if (typeof(fId) == 'string') {
            var fInput = document.getElementById(fId);
        } else {
            var fInput = fId;
        }
        fLimit = !fLimit ? fInput.value.length : fLimit;
        var fSize = fLimit < fInput.value.length ? fLimit : fInput.value.length;
        fInput.size = fInput.value ? fSize : 1;
    } catch (e) {
        alert('Error Function ResizeInput : ' + e.message);
    }
}



function rev(fString) {
    var text = String(fString);
    var backwards = '';
    for (var count = text.length; count >= 0; count--)
        backwards += text.substring(count, count - 1);
    return String(backwards);
}


function MapValue(fString, fValue, fPos) {
    var result = '';
    var c = 0;
    for (var i = 0; i < fString.length; i++) {
        result += String(fString.slice(i, i + 1));
        c++;
        if (c == Number(fPos)) {
            result += String(fValue);
            c = 0;
        }
    }
    return String(result);
}



function EvalMoney(fEvent, fInput, fSeparator, fDecimals) {
    try {
        var fSpliter = fSeparator == '.' ? ',' : '.';
        var fEvent = fEvent || window.event;
        if (Number(fEvent.keyCode) == 46 && fInput.value.indexOf(fSpliter) != -1)
            return false;
        if (!fInput.value && Number(fEvent.keyCode) == 46)
            return false;
        if (Number(fEvent.keyCode) == 8 || Number(fEvent.keyCode) == 9 || Number(fEvent.keyCode) == 37 || Number(fEvent.keyCode) == 39 || Number(fEvent.keyCode) == 46)
            return;
        var fCode = navigator.appName == 'Netscape' ? fEvent.charCode : fEvent.keyCode;
        var fChar = String.fromCharCode(fCode);
        var fValue = String(fInput.value) + String(fChar);

        fSeparator = !fSeparator ? '.' : String(fSeparator);
        fDecimals = !fDecimals ? 2 : Number(fDecimals);



        if ((fInput.value.indexOf(fSpliter) == -1 && fChar == fSpliter) || fChar == '0' || fChar == '1' || fChar == '2' || fChar == '3' || fChar == '4' || fChar == '5' || fChar == '6' || fChar == '7' || fChar == '8' || fChar == '9') {
            var fArray = fValue.split(fSpliter);
            if (fArray[1] && fArray[1].length > fDecimals || fValue.length > fInput.maxLength) {
                return false;
            }
            fArray[0] = fSeparator == '.' ? fArray[0].replace(/./gi, '') : fArray[0].replace(/,/gi, '');

            var fFirst = rev(MapValue(rev(fArray[0]), fSeparator, 3));

            fFirst = fFirst.slice(0, 1) == fSeparator ? fFirst.slice(1, fFirst.length) : fFirst;

            var fSecond = fArray[1] ? fSpliter + fArray[1] : '';

            var fFinal = fFirst;


            if (fChar == fSpliter) {
                fFinal += fChar;
            }

            fInput.value = fFinal + fSecond;

            return false;

            //fInput.value = String( fFirst ) + String( fSecond );

        } else {
            return false;
        }
    } catch (e) {
        alert("Alert EvalMoney : " + e.message);
        return false;
    }
}


function MoneyDecimal(fValue, fSeparator, fDecimal) {
    try {
        fSeparator = !fSeparator ? '.' : fSeparator;
        fDecimal = !fDecimal ? 2 : Number(fDecimal);
        var fSpliter = fSeparator == '.' ? ',' : '.';
        var fArray = String(Number(fValue).toFixed(fDecimal)).split(fSpliter);
        var fRev = rev(fArray[0]);

        var fFirst = rev(MapValue(fRev, fSeparator, 3));
        fFirst = fFirst.slice(0, 1) == fSeparator ? fFirst.slice(1, fFirst.length) : fFirst;

        if (fArray[1]) {
            var fValue = String(fFirst) + String(fSpliter) + String(fArray[1]);
        } else {
            var fValue = String(fFirst);
        }
        return string_replace(fValue, '-,', '-');
    } catch (e) {
        alert('Alert Function MoneyDecimal: ' + e.message);
    }
}


function show_sections(n) {
    try {
        n = n ? n : 50;
        for (var i = 0; i < n; i++) {
            if (document.getElementById('section' + String(i) + 'ID')) {
                var fSection = document.getElementById('section' + String(i) + 'ID');
                fSection.style.display = "none";
                ShowSection(String(i));
            }
        }
        return true;
    } catch (e) {
        alert('Alert Function show_sections: ' + e.message);
    }
}
//alert( MoneyDecimal( '354678.346', ',' ) );


function onBlurDecim(thisInput) {
    try {
        if (thisInput.value) {
            var valor = Number(StringReplace(',', thisInput.value));
            thisInput.value = MoneyDecimal(valor, ',');
        }
    } catch (e) {
        alert('Error Función onBlurDecim : ' + e.message);
    }
}

function cerrarPopup() {
    try {
        var fondo = document.getElementById("blokerDIV");
        var popup = document.getElementById("formularioDIV");

        popup.innerHTML = '';

        popup.style.display = 'none';
        fondo.style.display = 'none';
        document.body.style.overflow = "auto";
    } catch (e) {
        return false;
    }
}

function ClosePopup() {
    try {
        //LockAplication("unlock");
        var objPopup = document.getElementById("popupDIV");
        objPopup.style.width = "0px";
        objPopup.style.height = "0px";
        objPopup.style.left = "0px";
        objPopup.style.top = "0px";
        objPopup.innerHTML = "";
        objPopup.style.visibility = "hidden";
    } catch (e) {
        return false;
    }
}

function LoadPopup() {
    try {
        if (parent.document.getElementById("AplicationEndDIV")) {
            var objEnd = parent.document.getElementById("AplicationEndDIV");
        } else {
            var objEnd = document.getElementById("AplicationEndDIV");
        }
        //LockAplication("lock");
        if (parent.document.getElementById("popupDIV")) {
            var objPopup = parent.document.getElementById("popupDIV");
        } else {
            var objPopup = document.getElementById("popupDIV");
        }
        var width = Math.round(screen.width / 1.4);
        var height = Math.round(screen.height / 1.6);

        var left = Math.round(screen.width / 20);
        var top = Math.round(screen.height / 20);

        objPopup.style.width = String(width) + "px";
        objPopup.style.height = String(height) + "px";
        objPopup.style.left = String(left) + "px";
        objPopup.style.top = String(top) + "px";
        objPopup.style.visibility = "visible";
        objPopup.style.padding = "10px";
    } catch (e) {
        alert("Error " + e.message);
    }

}

/*********************************************************************
 * Metodo Publico que valida alphanumeric js.             *
 * HTML indentada a 2 espacios.                                      *
 * @fn AlphaInput                                                        *
 * @brief retorna el identificador del resultado                     *
 * @return cIdResultel id del resultado                              *
 *********************************************************************/
function AlphaInput(evt) {
    var k;
    document.all ? k = evt.keyCode : k = evt.which;
    //  alert(k);
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || (k == 8) || (k == 32) || (k == 0));
}

/*************************************************************************
 *Metodo que recoge todos los campos obligatorios dentro de un formulario*
 *y realiza la validación.                                               *
 *
 *@return retorna un booleano
 *
 *************************************************************************/
function validaciones() {
    try {
        var datos = [];
        var i = 0;
        $('input[validate]').each(function(index) {
            var obj = ""; // id del campo; si es radio es el name
            var tipo = ""; // tipo de dato a validar. Consultar tipos en : validator.js
            var min = ""; // cantidad minima de caracteres
            var max = ""; // cantidad maxima de carcteres
            //var com = ""; // Validar con confirmar contraseña
            var obl = ""; // obligatorio booleano

            if ($(this).attr("type") == "radio") {
                obj = $(this).attr("name");
            } else {
                obj = $(this).attr("id");
            }

            tipo = $(this).attr("validate");
            if (tipo == "placa") {
                if ($(this).attr("obl") === '1') {
                    datos[i] = [obj, tipo, true];
                } else {
                    datos[i] = [obj, tipo];
                }
                /*} else if(tipo == "password"){
      	com = $(this).attr("compass");

      	if ($(this).attr("minlength")) {
          min = $(this).attr("minlength");
        } else {
          min = 1;
        }
        if ($(this).attr("maxlength")) {
          max = $(this).attr("maxlength");
        } else {
          max = 50;
        }

        if ($(this).attr("obl") === '1') {
	      datos[i] = [obj, tipo, min, max, com, true];
	    } else {
	      datos[i] = [obj, tipo, min, max, com];
	    }*/

            } else {
                if ($(this).attr("minlength")) {
                    min = $(this).attr("minlength");
                } else {
                    min = 1;
                }
                if ($(this).attr("maxlength")) {
                    max = $(this).attr("maxlength");
                } else {
                    max = 50;
                }
                if ($(this).attr("type") != "file") {
                    if ($(this).attr("obl") === '1') {
                        datos[i] = [obj, tipo, min, max, true];

                    } else {
                        datos[i] = [obj, tipo, min, max];
                    }
                } else {
                    min = $(this).attr("format");
                    arreglo = min.split(",");
                    if ($(this).attr("obl") === '1') {
                        obl = true;
                    } else {
                        obl = false;
                    }
                    datos[i] = [obj, tipo, arreglo, obl];
                }
            }
            i++;
        });
        $('textarea[validate]').each(function(index) {
            var obj = ""; // id del campo; si es radio es el name
            var tipo = ""; // tipo de dato a validar. Consultar tipos en : validator.js
            var obl = ""; // obligatorio booleano

            obj = $(this).attr("id");
            if ($(this).attr("minlength")) {
                min = $(this).attr("minlength");
            } else {
                min = 1;
            }
            if ($(this).attr("maxlength")) {
                max = $(this).attr("maxlength");
            } else {
                max = 50;
            }
            tipo = $(this).attr("validate");
            if ($(this).attr("obl") == 1) {
                datos[i] = [obj, tipo, min, max, true];
            } else {
                datos[i] = [obj, tipo, min, max];
            }
            i++;
        });

        $('select[validate]').each(function(index) {
            var obj = ""; // id del campo; si es radio es el name
            var tipo = ""; // tipo de dato a validar. Consultar tipos en : validator.js
            var obl = ""; // obligatorio booleano

            obj = $(this).attr("id");
            tipo = $(this).attr("validate");
            if ($(this).attr("obl") == 1) {
                datos[i] = [obj, tipo, true];
            } else {
                datos[i] = [obj, tipo];
            }
            i++;

        });

        var validacion = inc_validar(datos);

        return validacion;
    } catch (e) {
        console.log("Error Fuction validaciones: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: LoadPopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 * \date: 24/06/2015
 * \date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function LoadPopupJQ(opcion, titulo, alto, ancho, redimen, dragg, lockBack, idPopup) {
    try {
        var id = 'popID';
        if (idPopup)
            id = idPopup;

        if (opcion == 'close') {
            $("#" + id).dialog("destroy").remove();
        } else {
            $("<div id='" + id + "' name='pop' />").dialog({
                height: alto,
                width: ancho,
                modal: lockBack,
                title: titulo,
                closeOnEscape: false,
                resizable: redimen,
                draggable: dragg,
                buttons: {
                    Cerrar: function() {
                        closePopUp(id);
                    }
                }
            });
        }
    } catch (e) {
        console.log("Error Fuction LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: LoadPopupJQNoButton
 *  \brief: Crea o destruye PopUp similar al LoadPopupJQ pero el boton se inyecta por html de acuerdo a la operación realizada
 *  \author: Ing. Alexander Correa
 * \date: 04/09/2015
 * \date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \param: idPopup  String   Id del POPUP
 *  \return: 
 */
function LoadPopupJQNoButton(opcion, titulo, alto, ancho, redimen, dragg, lockBack, idPopup) {
    try {
        var id = 'popID';
        if (idPopup)
            id = idPopup;

        if (opcion == 'close') {
            $("#" + id).dialog("destroy").remove();
        } else {

            $("<div id='" + id + "' name='pop' />").dialog({
                height: alto,
                width: ancho,
                modal: lockBack,
                title: titulo,
                closeOnEscape: false,
                resizable: redimen,
                draggable: dragg,
                /*buttons: {
                  //Cerrar: function(){ LoadPopupJQ('close') }
                }*/
            });
        }
    } catch (e) {
        console.log("Error Fuction LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

//funcion para traer solo la informacion consignada en el formulario
function getDataForm() {
    try {

        var parametros;
        var file = $("#imagen").val();
        if (!file) {
            $("input").each(function(index) {
                if ($(this).attr("type") != 'checkbox' && $(this).attr("type") != 'radio') {
                    if ($(this).val() != "" && $(this).val() != null) {
                        parametros += "&" + $(this).attr("name");
                        parametros += "=" + $(this).val();
                    }
                } else if ($(this).attr("checked")) {
                    parametros += "&" + $(this).attr("name");
                    parametros += "=" + $(this).val();
                }
            });
            $("select").each(function(index) {
                if ($(this).val() != "" || $(this).val() != null) {
                    parametros += "&" + $(this).attr("name");
                    parametros += "=" + $(this).val();
                }
            });
            $("textarea").each(function(index) {
                if ($(this).val() != "" || $(this).val() != null) {
                    parametros += "&" + $(this).attr("name");
                    parametros += "=" + $(this).val();
                }
            });
            return parametros;
        }
    } catch (e) {
        console.log("Error Fuction getDataForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

//funcion para recargar la pagina actual y actualizar a lista
function closed() {
    LoadPopupJQNoButton('close');
    $("#opcionID").val("");
    var cod = $("#cod_servicID").val();
    location.href = '?window=central&cod_servic=' + cod + '&opcion=99';
}

//funcion para cargar el formulario de registro
function formulario() {
    $("#form_searchID")[0].reset();
    $("#opcionID").val(1);
    document.form_search.submit();
}

function imprimir() {
    $("#opcionID").val(2);
    document.form_search.submit();
}

$(document).ready(function() {

    $(".accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        icons: {
            "header": "ui-icon-circle-arrow-e",
            "activeHeader": "ui-icon-circle-arrow-s"
        }
    }).click(function() {
        $("body").removeAttr("class");
    });
    if ($(document).hasClass('date')) {
        $(".date").datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }
    if ($(document).hasClass('time')) {
        $(".time").timepicker({
            timeFormat: "hh:mm",
            showSecond: false
        });
    }

});

function checkConnection() {
    return navigator.onLine;
}

/*! \fn: confirmGL
 *  \brief: Confirmador para GL
 *  \author: Ing. Fabian Salinas
 *  \date:  01/02/2015
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: txt           String  Mensaje de Confirmación
 *  \param: nameFunction  String  Nombre de la función y sus parametros nameFunction(var1, var2, ...); si presionan SI
 *  \runExample:  confirmGL(txt, "editActivi2(\'" + ind_estado + "\', \'" + obj.val() + "\');" );
 *  \return: 
 */
function confirmGL(txt, nameFunction) {
    try {
        closePopUp('popupConfirmID');
        LoadPopupJQNoButton('open', 'Confirmar Operaci\u00F3n', 'auto', 'auto', false, false, false, 'popupConfirmID');
        var popup = $("#popupConfirmID");
        popup.parent().children().children('.ui-dialog-titlebar-close').hide();

        var msj = '<div style="text-align:center">' + txt + '<br><br><br><br>';
        msj += '<input type="button" name="si" id="siID" value="Si" style="cursor:pointer" onclick="closePopUp(\'popupConfirmID\'); ' + nameFunction + '" class="crmButton small save"/> &nbsp;&nbsp;&nbsp;&nbsp';
        msj += '<input type="button" name="no" id="noID" value="No" style="cursor:pointer" onclick="closePopUp(\'popupConfirmID\');" class="crmButton small save"/><div>';

        popup.append(msj);
    } catch (e) {
        console.log("Error Function confirmGL: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: closePopUp
 *  \brief: funcion para cerrar solo el popUp sin alterar formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 
 *  \date modified: dd/mm/aaaa
 *  \param: idPopup  String  ID del div del popup 
 *  \return: 
 */
function closePopUp(idPopup) {
    var id = 'popID';
    if (idPopup)
        id = idPopup;

    $("#" + id).dialog("destroy").remove();
}

/*! \fn: BlocK
 *  \brief: Funcion para bloquear pantalla mientra carga ajax requiere jquery.blockUI.js
 *  \author: Ing. Fabian Salinas
 *  \date: 
 *  \date modified: dd/mm/aaaa
 *  \param: txt  String   Texto Contenido del mensaje
 *  \param: ind  Boolean  Indica si se crea o se destrulle
 *  \return: 
 */
function BlocK(txt, ind) {
    try {
        if (ind == true) {
            $.blockUI({
                message: '<h1> ' + txt + '</h1><img src="../satt_standa/images/puntos.gif">',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#105587',
                    '-webkit-border-radius': '20px',
                    '-moz-border-radius': '20px',
                    opacity: .8,
                    color: '#fff'
                }
            });
        } else {
            $.unblockUI();
        }
    } catch (e) {
        console.log("Error Function blocK: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/* ! \fn: removeStyle
 *  \brief: elimina el ancho del selector de hora para su correcta visualizacion
 *  \author: Ing. Alexander Correa
 *  \date: 19/04/2016
 *  \date modified: dia/mes/año
 *  \param: id => int => id del campo que hace de selector de hora     
 *  \return 
 */

function removeStyle(id) {
    $("#ui-timepicker-div-" + id).removeAttr('style');
}

/*! \fn: cargarPopupAlert
 *  \brief: Crea un popUp y lo coloca sobre los iframe
 *  \author: Ing. Nelson Liberato, modified by Ing. David Rincón
 *  \date modified: 01/08/2016
 *  \param: NINGUNO
 *  \return No devuelve valores pero prepara los divs contenedores
 */

function cargarPopupAlert() {
    try {
        var fondo = document.getElementById("blokerDIV");
        var popup = document.getElementById("formularioDIV");

        var x = document.body.scrollWidth;
        var y = getViewportYAlert();
        //    var y = document.body.scrollHeight;
        //  var y = window.innerHeight;

        var width = Math.round(x / 1.2);
        var height = Math.round(y / 1.8);

        var left = Math.round(x / 12);
        var top = Math.round(y / 20);

        document.body.style.overflow = "hidden";

        //popup.style.width = String(width) + "px";
        //popup.style.height = String(height) + "px";
        //popup.style.left = String(left) + "px";
        fondo.style.display = "block";
        fondo.style.zIndex = "9999";

        popup.style.width = "100%";
        popup.style.height = "100%";
        popup.style.left = "0px";

        popup.style.top = "0px";

        popup.style.display = "block";
        popup.style.padding = "10px";
        popup.style.zIndex = "10000";
        popup.style.position = "fixed";

        //popup.style.position = "absolute";

        /*fondo.style.display = "block";
        fondo.style.width = "100%";
        fondo.style.height = (y + 50) + "px";*/
        popup.focus();
        //window.pageYOffset es la posición del scroll en la pantalla
        //popup.style.top = (top+window.pageYOffset) + "px";
        popup.style.top = "0px";

    } catch (e) {
        //return false;
        alert("Error cargarPopupAlert " + e.message + e.lineNumber);
    }
}

/*! \fn: colocarPopupAlert
 *  \brief: Carga un javascript sobre los iframe, ya que el objeto que busca está por dentro de uno de los iframe
 *  \author: Ing. Nelson Liberato, modified by Ing. David Rincón
 *  \date modified: 01/08/2016
 *  \param: NINGUNO
 *  \return No devuelve valores pero coloca el popUp que quedará listo para su impresión
 */

function colocarPopupAlert() {
    try {
        var minuevoscript = parent.document.createElement("script");
        minuevoscript.src = '../satt_standa/js/ajax_alerta_llamad.js'; // Ruta del JS donde estan las funciones que necesita
        minuevoscript.setAttribute('type', 'text/javascript');

        cargarPopupAlert(); // Llama la funcion que mustra el div que bloquea la pantalla
        //AjaxGetDataLocked(params, "formularioDIV", "post" );  // Hace ajax, donde coloca HTML en el popup (DIV) desplegado con la funcion: cargarPopup(); 

        // Esta linea carga el ***.js sobre los iframe, para poder usar las funciones
        var head = parent.document.getElementsByTagName('head').item(0);
        head.appendChild(minuevoscript);
    } catch (e) {
        //return false;
        alert("Error colocarPopupAlert " + e.message + e.lineNumber);
    }
}

/*! \fn: getViewportYAlert
 *  \brief: Devuelve el valor de Y en la pantalla
 *  \author: Ing. Nelson Liberato, modified by Ing. David Rincón
 *  \date modified: 01/08/2016
 *  \param: NINGUNO
 *  \return No devuelve valores pero retorna un número con el valor de Y
 */
function getViewportYAlert() {
    var viewportheight;

    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight

    if (typeof window.innerWidth != 'undefined') {
        viewportheight = window.innerHeight
    }

    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    else if (typeof document.documentElement != 'undefined' &&
        typeof document.documentElement.clientWidth !=
        'undefined' && document.documentElement.clientWidth != 0) {
        viewportheight = document.documentElement.clientHeight
    }

    // older versions of IE
    else {
        viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }
    return viewportheight;
}

/*! \fn: exportExcel
 *  \brief: redireccion exportar dentro de la misma clase
 *  \author: Edward Serrano
 *  \date modified: 05/04/2017
 *  \param: opcion: opcion de la clase donde se encuntra la funcion de exportar
 *  \return No devuelve valores pero retorna un número con el valor de Y
 */
function exportExcel(opcion) {
    try {
        var cod_servic = $("input[name=cod_servic]").val();
        window.open("index.php?window=central&cod_servic=" + cod_servic + "&menant=" + cod_servic + "&" + opcion)

    } catch (e) {
        console.log("Error Fuction pintar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/************************************************************************************
 * @desc Public function that creates interface and events of personalized alerts.  *
 * @author Felipe Clavijo.                                                          *
 * @date 22/05/2019                                                                 *
 * @fn personalizedAlert                                                            *
 * @param string type - Define alert type (danger, warning, info, success)          *
 * @param string title - Alert title to show                                        *
 * @param string message - Message to show                                          *
 * @param boolean automaticClosing - Specify automatic closing of alert             *
 * @param object globalContainer - Element instance where alert has will created    *
 * @brief Create alert                                                              *
 * @return                                                                          *
 ************************************************************************************/
var positionAlert = 0;

function personalizedAlert(type, title, message, automaticClosing, globalContainer) {

    //Create Objects
    var div = $("<div>");
    var a = $("<a>");
    var strong = $("<strong>");
    var divText = $("<div>");

    //Assign Attributes
    div.attr("class", "alerts alert alert-" + type + " alert-dismissible fade in");
    div.hide();
    a.attr({ "href": "#", "class": "close", "data-dismiss": "alert", "aria-label": "close" });
    a.html("&times;");
    strong.html(title);
    divText.html(message);

    //Add Objects
    div.append(strong, a, divText);

    //Add styles
    div.css({
        "position": "fixed",
        "bottom": positionAlert,
        "width": "100%"
    });
    positionAlert += 80

    //Show Object
    globalContainer.append(div);
    div.slideDown(300);

    //Validate automatic closing
    if (automaticClosing) {
        //Close
        setTimeout(function() {
            div.fadeOut("slow", function() {
                this.remove();
                positionAlert -= 80;
            });
        }, 4000);
    }
}

/************************************************************************************
 * @desc Public function that return count object length.                           *
 * @author Felipe Clavijo.                                                          *
 * @date 22/05/2019                                                                 *
 * @fn objectLength                                                                 *
 * @param string object - Object instance that will be evaluated                    *
 * @brief Count inner elements                                                      *
 * @return int                                                                      *
 ************************************************************************************/
function objectLength(object) {
    var length = 0;
    for (var key in object) {
        if (object.hasOwnProperty(key)) {
            ++length;
        }
    }
    return length;
};

/************************************************************************************
 * @desc Public function that create and return random color code HSL.              *
 * @author Felipe Clavijo.                                                          *
 * @date 22/05/2019                                                                 *
 * @fn randomColor                                                                  *
 * @param string type - Color type to return                                        *
 * @brief Create random color                                                       *
 * @return string - HSL code (hue, saturation, lightness)                           *
 ************************************************************************************/
function randomColor(type = "pastel") {
    switch (type) {
        case "pastel":

            return "hsl(" + 360 * Math.random() + ',' +
                (25 + 25 * Math.random()) + '%,' +
                (75 + 0 * Math.random()) + '%)';

        case "opaque":

            return "hsl(" + 360 * Math.random() + ',' +
                20 + '%,' +
                30 + '%)';

        case "neutral":

            return "hsl(" + 360 * Math.random() + ',' +
                50 + '%,' +
                40 + '%)';

        case "grandRange":

            return "hsl(" + 360 * Math.random() + ',' +
                (60 + 20 * Math.random()) + '%,' +
                (25 + 17 * Math.random()) + '%)';

        default:

            return "hsl(0, 0%, 0%)";
    }
}

/************************************************************************************
 * @desc Public function that block UI depending on a request.                      *
 * @author Felipe Clavijo.                                                          *
 * @date 22/05/2019                                                                 *
 * @fn loadAjax                                                                     *
 * @param string type - Type of state (start, finish)                               *
 * @brief Block UI                                                                  *
 * @return                                                                          *
 ************************************************************************************/
function loadAjax(type) {
    if (type == "start") {
        $.blockUI({ message: '<div class="bg-primary">Espere un momento</div>' });
    } else {
        $.unblockUI();
    }
}

/************************************************************************************
 * @desc Public function that valid ajax request errors.                            *
 * @author Felipe Clavijo.                                                          *
 * @date 22/05/2019                                                                 *
 * @fn loadAjax                                                                     *
 * @param string jqXHR - Error code that return ajax                                *
 * @param string exception - Exception that return ajax                             *
 * @param string title - Title to show in alert                                     *
 * @param string elementMessage - Container instance where alert will be showed     *
 *                                  - if empty - Create a personalized alert in body*
 * @param string typeMessage - :                                                    *
 *                               table - Create row in container to create alert    *
 *                               HTML - Modify HTML container to create alert       *
 *                               alert - Create a personalized alert                *
 * @brief Block UI                                                                  *
 * @return boolean                                                                  *
 ************************************************************************************/
function errorAjax(jqXHR, exception, title, elementMessage, typeMessage) {

    console.log(jqXHR, exception);

    //Format error
    if (jqXHR.status === 0) {
        message = 'Sin conexi?.\n Verifique su red.';
    } else if (jqXHR.status == 404) {
        message = 'P?ina solicitada no encontrada. [404]';
    } else if (jqXHR.status == 500) {
        message = 'Error del servidor [500].';
    } else if (exception === 'parsererror') {
        message = 'Ha ocurrido un error con la solicitud.';
    } else if (exception === 'timeout') {
        message = 'Tiempo agotado para esta solicitud.';
    } else if (exception === 'abort') {
        message = 'Ha ocurrido un error con la solicitud.';
    } else {
        message = 'Error desconocido.\n' + jqXHR.responseText;
    }

    //validate the ways to show the error
    if (typeMessage == "table") {
        $(elementMessage).html(`
            <tr>
                <td colspan='100' class='btn-danger'>` + title + "<br>" + message + `</td>
            </tr>
        `);
        return true;
    } else if (typeMessage == "HTML") {
        $(elementMessage).html("<div class='btn-danger'>" + title + "<br>" + message + "</div>");
        return true;
    } else if (typeMessage == "alert") {
        personalizedAlert("danger", title, message, true, elementMessage);
        return true;
    } else if (elementMessage == "" || elementMessage.length == 0 || elementMessage == undefined || elementMessage == null) {
        personalizedAlert("danger", title, message, true, "body");
        return true;
    } else {
        $(elementMessage).html("<div class='btn-danger'>" + title + "<br>" + message + "</div>");
        return true;
    }

}

/************************************************************************************
 * @desc Public function that converts a string to camel case format.               *
 * @author Felipe Clavijo.                                                          *
 * @date 23/05/2019                                                                 *
 * @fn camelCaseFormatter                                                           *
 * @param string string - String to convert                                         *
 * @brief Camel case formatter                                                      *
 * @return                                                                          *
 ************************************************************************************/
function camelCaseFormatter(string) {
    //Create necessary variables
    let cutString = string.split(/\W/g);
    let convertedString = "";

    //Go through cut string
    $.each(cutString, function(index, value) {
        convertedString += (index == 0 ? value.toLowerCase() : value.charAt(0).toUpperCase() + value.slice(1));
    });

    return convertedString
}
