
/*
 -------------------------------------------------------------------------------------------------------
 @File    : ajax.js
 @Authors : Ing. Christiam Barrera A.
 Ing. Joan Voltaire Quintero C.
 @Date    : 2008-11-12, Bogot? - Colombia
 @Company : Intrared LTDA
 @Descrip : Efectua peticiones as?ncronas al servidor para esperar respuesta en texto y en XML.
 -------------------------------------------------------------------------------------------------------
 */
/*
 * @Method:Ajax
 * @Funtion: Funcion para inicializar el objeto de AJAX
 **/
function Ajax(){
    var fAjaxObject = false;
    try {
        fAjaxObject = new ActiveXObject("Msxml2.XMLHTTP");
    } 
    catch (e) {
        try {
            fAjaxObject = new ActiveXObject("Microsoft.XMLHTTP");
        } 
        catch (E) {
            fAjaxObject = false;
        }
    }
    if (!fAjaxObject && typeof XMLHttpRequest != "undefined") 
    {
        fAjaxObject = new XMLHttpRequest();
    }
    return fAjaxObject;
}

/*
 * @Method: AjaxGetData
 * @Funtion: Obtiene datos por medio de AJAX y te recarga un componente tipo AJAX
 **/
function AjaxGetData(fUrl, fData, fLayer, fMethod, fFunctions)//recibe la url, una cadena con los dato, el id del layer, y el metodo a utilizar
{
    var fAjaxObject = Ajax();//creo un objeto AJAX
    var fHtmlObject = document.getElementById(fLayer);//obtengo  el layer del documento
    fData = "Ajax=on&" + fData;//envio los datos con una variable ajax activada
    try {
        if (fMethod.toLowerCase() == "post") {
            fAjaxObject.open(fMethod, fUrl + "?", true);
            fAjaxObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1");
			fAjaxObject.send(fData);

            //
                   

        }
        else
		{
            fAjaxObject.open(fMethod, fUrl + "?" + fData, true);
            fAjaxObject.send(null);
        }
    } 
    catch (exception) {
        alert("Error: Method AjaxGetData - " + exception);
    }
    fAjaxObject.onreadystatechange = function(){
        if (fAjaxObject.readyState == 4 && fAjaxObject.status == 200) 
		{
			
        	//alert(unescape(fAjaxObject.responseText));
			
			try
			{
				fHtmlObject.innerHTML = unescape( fAjaxObject.responseText );
                // Si el servicio presente es de listar usuarios APP movil, se ejecuta el desifrado de base 64 ya que el dinamic list no permite hacer basse64_decode()
                if($("#cod_servicID").val()    == '20151230')
                {
                   fFunctions = "unCyfre();";
                }

                if (typeof(fFunctions) == 'string') 
				{
					fFunctions = fFunctions.split(';');
					if (fFunctions.length > 0) 
					{
						for (var i = 0; i < fFunctions.length; i++) 
						{
							var fFunc = trim(fFunctions[i]);
							setTimeout(fFunc, 100);
						}
					}


				}
			}
			catch( e )
			{
				alert( "Error al Llenar  DIV " + e.message );
			}
                
        }
    };
}

/*
 * @Method: AjaxGetXml
 * @Funtion: Obtiene Datos en XML
 **/
function AjaxGetXml(fUrl, fData, fMethod){
    document.getElementById("name")
    
    var fAjaxObject = Ajax();
    fData = "Ajax=on&" + fData;
    try {
        if (fMethod.toLowerCase() == "post") {
            fAjaxObject.open(fMethod, fUrl + "?", true);
            fAjaxObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            fAjaxObject.send(fData);
        }
        else {
            fAjaxObject.open(fMethod, fUrl + "?" + fData, true);
            fAjaxObject.send(null);
        }
    } 
    catch (exception) {
        alert("Error: Method AjaxGetData - " + exception);
    }
    fAjaxObject.onreadystatechange = function(){
        if (fAjaxObject.readyState == 4 && fAjaxObject.status == 200) {
            try 
			{
				//alert(unescape(fAjaxObject.responseText));
                ReadXml(fAjaxObject);
                
            } 
            catch (e) {
                alert("Error " + e)
            }
        }
    };
}

/*
 * @Method: ReadXml
 * @Funtion: Obtiene Datos en XML y los carga en sus respectivos componentes
 **/
function ReadXml(fAjaxObject){
    try {
        var fXmlDocument    = fAjaxObject.responseXML; //convierto la respuesta del AJAX en XML
        var fSelects        = fXmlDocument.getElementsByTagName("select"); //Obtengo los selects
        var fTexts          = fXmlDocument.getElementsByTagName("text"); //Obtengo los texts
        var fRadios         = fXmlDocument.getElementsByTagName("radio"); //Obtengo los radios
        var fChecks         = fXmlDocument.getElementsByTagName("check"); //Obtengo los checks
        var fLayers         = fXmlDocument.getElementsByTagName("layer"); //Obtengo los layers
        var fOptions        = fXmlDocument.getElementsByTagName("selection"); //Obtengo los selections
        var fMultiples      = fXmlDocument.getElementsByTagName("multiple"); //Obtengo los selections
        var fForm           = fXmlDocument.getElementsByTagName("form");
        var fFocus          = fXmlDocument.getElementsByTagName("focus");
        var fImages         = fXmlDocument.getElementsByTagName("image");
        var fReads          = fXmlDocument.getElementsByTagName("read");
        var fDisables       = fXmlDocument.getElementsByTagName("disable");
        var fAlerts         = fXmlDocument.getElementsByTagName("alert");
        var fClass          = fXmlDocument.getElementsByTagName("class");
        var fScript         = fXmlDocument.getElementsByTagName("script");
        
        if (fScript != null && fScript.length != 0)
        {
            LoadScript(fScript);//cargos los fMultiples obtenidos en el XMl
        }
        if (fTexts != null)//verifica que el array de texts no este vacia
        {
            LoadTexts(fTexts);//cargos los textos obtenidos en el XMl
        }
        
        if (fSelects != null && fSelects.length != 0)//verifica que el array de selects no este vacia
        {
            LoadSelects(fSelects);//cargos los  selects obtenidos en el XMl
        }
        
        if (fRadios != null && fRadios.length != 0)//verifica que el array de radios no este vacia
        {
            LoadRadios(fRadios);//cargos los radios obtenidos en el XMl
        }
        
        if (fChecks != null && fChecks.length != 0)//verifica que el array de checks no este vacia
        {
            LoadChecks(fChecks);//cargos los checks obtenidos en el XMl
        }
        if (fLayers != null && fLayers.length != 0)//verifica que el array de layers no este vacia
        {
            LoadLayers(fLayers);//cargos los layers obtenidos en el XMl
        }
        if (fOptions != null && fOptions.length != 0)//verifica que el array de fOptions no este vacia
        {
            SelectOption(fOptions);//cargos los fOptions obtenidos en el XMl
        }
        if (fMultiples != null && fMultiples.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadMultiples(fMultiples);//cargos los fMultiples obtenidos en el XMl
        }
        
        if (fForm != null && fForm.length != 0)//verifica que el array de fMultiples no este vacia
        {
            ChangeForms(fForm);//cargos los fMultiples obtenidos en el XMl
        }
        
        if (fDisables != null && fDisables.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadDisables(fDisables);//cargos los fMultiples obtenidos en el XMl
        }
        
        if (fFocus != null && fFocus.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadFocus(fFocus);//cargos los fMultiples obtenidos en el XMl
        }
        
        if (fImages != null && fImages.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadImages(fImages);//cargos los fMultiples obtenidos en el XMl
        }
        if (fReads != null && fReads.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadReads(fReads);//cargos los fMultiples obtenidos en el XMl
        }       
        if (fAlerts != null && fAlerts.length != 0)//verifica que el array de fMultiples no este vacia
        {
            LoadAlerts(fAlerts);//cargos los fMultiples obtenidos en el XMl
        }
        if (fClass != null && fClass.length != 0)
        {
            LoadClass(fClass);//cargos los fMultiples obtenidos en el XMl
        }
        
        
        //-------------------------------------------------------------------------------------------------------------------------------------------
        //NOTA: DEBE SER SIEMPRE EL ULTIMO METODO EN EJECUTARSE PUESTO UNA FUNCION JAVASCRIPT PUEDE REQUERIR DE ELEMENTOS HTML MODIFICADOS POR AJAX
        
        //-------------------------------------------------------------------------------------------------------------------------------------------
        
    } 
    catch (Exception) {
        alert("Error Method ReadXml " + Exception);
    }
}

/*
 * @Method: LoadTexts
 * @Funtion: Carga los campos del formulario
 **/
function LoadTexts(fTexts){
    var fSize = fTexts.length;//tama;o del array de textos
    for (var i = 0; i < fSize; i++)//recorrido del array de textos
    {
        var xmlObject = fTexts[i];//selecciono un texto del array segun el indice i
        var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del texto seleccionado
        var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del texto segun el id seleccionado
        fJsObject.disabled = false;//habilito el componente
        fJsObject.value = unescape( xmlObject.getAttribute("value") );//cambio el valor del componente por el obtenido en el tag de xml
    }
}

/*
 * @Method: LoadLayers
 * @Funtion: Carga los campos del formulario
 **/
function LoadLayers(fLayers){
    try {
        var fSize = fLayers.length;//tama;o del array de layers
        for (var i = 0; i < fSize; i++)//recorrido del array de layers
        {
            var xmlObject = fLayers[i];//selecciono un layer del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del layer seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            fJsObject.disabled = false;//habilito el componente
            fJsObject.innerHTML = unescape( xmlObject.getAttribute("innerHTML") );//cambio el innerHTML componente por el obtenido en el tag de xml
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert("Error Method LoadLayers " + Exception);//muestro la exepcion si existe
    }
    
}

/*
 * @Method: LoadRadios
 * @Funtion: Carga los radios  button del formulario
 **/
function LoadRadios(fRadios){
    try {
        var fSize = fRadios.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fRadios[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape( xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            if (fValue == "1")//compara q el valor de value obtneido del xml sea igual a 1
                fJsObject.checked = true;//habilito el radio lo chekeo.
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert(Exception);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadReads
 * @Funtion: Cambia el estado de lectura de los campos en el formulario
 **/
function LoadReads(fReads){
    try {
        var fSize = fReads.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fReads[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape( xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            if (fValue == "false")//compara q el valor de value obtneido del xml sea igual a 1
                fJsObject.readOnly = true;//habilito el radio lo chekeo.
            else
                fJsObject.readOnly = false;
                
                
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert(Exception);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadDisables
 * @Funtion: Cambia el estado de lectura de los campos en el formulario
 **/
function LoadDisables(fDisables){
    try {
        var fSize = fDisables.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fDisables[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape( xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            if (fValue == "true")//compara q el valor de value obtneido del xml sea igual a 1
                fJsObject.disabled = true;//habilito el radio lo chekeo.
            else
                fJsObject.disabled = false;
                
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert(Exception);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadChecks
 * @Funtion: Carga los check sbutton del formulario
 **/
function LoadChecks(fChecks){
    try {
        var fSize = fChecks.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorro el array de los checks
        {
            var xmlObject = fChecks[i];//selecciono el check indicado por el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//capturo el atributo id del check seleccionado
            var fValue = unescape( xmlObject.getAttribute("value") );//capturo el atributo value del check seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//capturo el objeto del documento con el id obtenido del check
            if (fValue == "1")//si el valor obtenido del xml es igual a 1 entonces
                fJsObject.checked = true;//chekeo el check
        }
    } 
    catch (Exception)//campturo la excepcion si existe para  mostrarla
    {
        alert(Exception);//se muestra la excepcion
    }
}

/*
 * @Method: LoadSelects
 * @Funtion: Carga los Selects del formulario
 **/
function LoadSelects(selects){
    var size = selects.length;//obtengo el tama;o del array de selects
    for (var i = 0; i < size; i++)//recorro el array de selects
    {
        var xmlObject = selects[i];//selecciono el select q el indice i me indique
        var xmlObjectID = xmlObject.getAttribute("id");//obtengo el atributo id del select obtenido
        var jsObject = document.getElementById(xmlObjectID);//obtengo el componente del documento con el id obtenido
        jsObject.disabled = false;//habilito el componente
        var options = xmlObject.getElementsByTagName("option");//obtengo el array de opciones
        if (options.length == 0)//verifico si el array es vacio entonces
        {
            jsObject.options.length = 0;//coloco el tama;o en 0
            continue;
        }
        jsObject.options.length = 0;//pongo el tama;o del select en 0
        for (j = 0; j < options.length; j++)//recorro las opciones del select
        {
            option = options[j];//capturo una opcion que la cual la indique el indice j
            jsObject.options[j] = new Option(unescape(option.firstChild.data), unescape(option.getAttribute("value")));//le agrego una opcion al componente segun la opcion optenida en el xml
            if (option.getAttribute("selected") == "selected")//verifico si la opcion actual es igual a la seleccionada
                jsObject.options[j].selected = true;//pongo en estado seleccionada la opcion
        }
    }
}

/*
 * @Method: SelectOption
 * @Funtion: Carga los Selects del formulario
 **/
function SelectOption(options){
    try {
        var fSize = options.length;
        for (var i = 0; i < fSize; i++) {
            var xmlObject = options[i];
            var xmlObjectID = xmlObject.getAttribute("id");
            var fValue = unescape( xmlObject.getAttribute("value") );
            var fJsObject = document.getElementById(xmlObjectID);
            var fOptions = fJsObject.options.length;
            
            for (var k = 0; k < fOptions; k++) {
                if (fJsObject.options[k].getAttribute("value") == fValue) {
                    fJsObject.options[k].selected = true;
                }
            }
        }
    } 
    catch (Exception) {
        alert("Error Method SelectOption " + Exception);
    }
    
}

/*
 * @Method: LoadMultiples
 * @Funtion: Carga los Selects del formulario
 **/
function LoadMultiples(fMultiples){
    try {
        var size = fMultiples.length;//obtengo el tama;o del array de fMultiples
        for (var i = 0; i < size; i++)//recorro el array de fMultiples
        {
            var xmlObject = fMultiples[i];//selecciono el multiple q el indice i me indique
            var xmlObjectID = xmlObject.getAttribute("id");//obtengo el atributo id del multiple obtenido
            var jsObject = document.getElementById(xmlObjectID);//obtengo el componente del documento con el id obtenido
            jsObject.disabled = false;//habilito el componente
            var options = xmlObject.getElementsByTagName("option");//obtengo el array de opciones
            if (options.length == 0)//verifico si el array es vacio entonces
            {
                jsObject.options.length = 0;//coloco el tama;o en 0
                continue;
            }
            jsObject.options.length = 0;//pongo el tama;o del multiple en 0
            for (j = 0; j < options.length; j++)//recorro las opciones del multiple
            {
                option = options[j];//capturo una opcion que la cual la indique el indice j
                jsObject.options[j] = new Option(unescape(option.firstChild.data), unescape(option.getAttribute("value")));//le agrego una opcion al componente segun la opcion optenida en el xml
                if (option.getAttribute("selected") == "selected")//verifico si la opcion actual es igual a la seleccionada
                    jsObject.options[j].selected = true;//pongo en estado seleccionada la opcion
            }
        }
    } 
    catch (Exception) {
        alert("Error Method LoadMultiples" + Exception);
    }
}

/*
 * @Method: ChangeForms
 * @Funtion: Carga los radios  button del formulario
 **/
function ChangeForms(fForms){
    try {
        var fSize = fForms.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fForms[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape(  xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            var fExcepciones = xmlObject.getAttribute("ids");
            
            switch(fValue)
            {
                case "true":
                FormDisable( xmlObjectID , fExcepciones );
                break;
                
                case "false":
                FormEnable( xmlObjectID, fExcepciones );
                break;
                
                case "reset":
                fJsObject.reset();
                break;
                
                case "submit":
                fJsObject.submit();
                break;
            }
            
        }
        
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert("Error ChangeForms "+Exception);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadImages
 * @Funtion: Carga los radios  button del formulario
 **/
function LoadImages(fImages){
    try {
        var fSize = fImages.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fImages[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute( 'id' );//le capturo el id del radio seleccionado
            var fValue = unescape( xmlObject.getAttribute( 'value' ) );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            
            fJsObject.src = fValue;//habilito el radio lo chekeo.
            //alert( fValue );
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert(Exception);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadFocus
 * @Funtion: Carga los radios  button del formulario
 **/
function LoadFocus(fRFocus){
    try {
        var fSize = fRFocus.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fRFocus[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape(  xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
           
            fJsObject.focus();
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert("Error LoadFocus "+Exception.name+" "+Exception.message);//muestro la exepcion si existe
    }
}

/*
 * @Method: LoadAlerts
 * @Funtion: Carga los radios  button del formulario
 **/
function LoadAlerts(fAlerts){
    
    try {
        var fSize = fAlerts.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fAlerts[i];//selecciono un radio del array segun el indice i
            var fValue = unescape(  xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            alert(unescape(fValue));
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert("Error LoadAlerts "+Exception);//muestro la exepcion si existe
    }
}


function LoadClass(fClass)
{ 
    try {
        var fSize = fClass.length;
        for (var i = 0; i < fSize; i++)
        {
            var xmlObject = fClass[i];//selecciono un radio del array segun el indice i
            var xmlObjectID = xmlObject.getAttribute("id");//le capturo el id del radio seleccionado
            var fValue = unescape(  xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            var fJsObject = document.getElementById(xmlObjectID);//obtengo el componente del layer segun el id seleccionado
            
            fJsObject.className = fValue;
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert(Exception);//muestro la exepcion si existe
    }
}


function LoadScript(fScript){
    
    try {
        var fSize = fScript.length;//tama;o del array de radios
        for (var i = 0; i < fSize; i++)//recorrido del array de radios
        {
            var xmlObject = fScript[i];//selecciono un radio del array segun el indice i
            var fValue = unescape( xmlObject.getAttribute("value") );//le capturo el atributo de value del radio seleccionado
            
            setTimeout( unescape(fValue), 0 );
            
                        //alert(unescape(fValue));
        }
    } 
    catch (Exception)//campturo la excepcion para manejarla
    {
        alert("Error LoadScript "+Exception);//muestro la exepcion si existe
    }
}



function trim( s )  {
    s = s.replace( /\s+/gi, '' ); //sacar espacios repetidos dejando solo uno
    s = s.replace( /^\s+|\s+$/gi, '' ); //sacar espacios blanco principio y final
    return s;
}

function FormEnable( id, ids ) {
  ids = trim( ids );
  if ( ids )
    var ids = ids.split( "," );
  var elements = document.getElementById( id ).elements;
  var size = elements.length;
  for( var i=0; i<size; i++ )   
  {
    elements[i].disabled = false;
    for ( var j=0; j<ids.length; j++ )  
    {
        if ( elements[i].id == ids[j] ) 
        {
            elements[i].disabled = true;
        }  
    }        
  }
}


function FormDisable( id ) {
  var elements = document.getElementById( id ).elements;
  var size = elements.length;
  for( var i=0; i<size; i++ )
    elements[i].disabled = true;
}


//----------------------------------------------------------------------------------------------------------------------

function AjaxGetDataLocked(fUrl, fData, fLayer, fMethod )//recibe la url, una cadena con los dato, el id del layer, y el metodo a utilizar
{
    LockAplication( "lock" );
    var fAjaxObject = Ajax();//creo un objeto AJAX
    var fHtmlObject = document.getElementById(fLayer);//obtengo  el layer del documento
    fData = "Ajax=on&" + fData;//envio los datos con una variable ajax activada
    try {
        if (fMethod.toLowerCase() == "post") {
            fAjaxObject.open(fMethod, fUrl + "?", true);
            fAjaxObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            fAjaxObject.send(fData);
        }
        else {
            fAjaxObject.open(fMethod, fUrl + "?" + fData, true);
            fAjaxObject.send(null);
        }
    } 
    catch (exception) {
        alert("Error: Method AjaxGetData - " + exception);
    }
    fAjaxObject.onreadystatechange = function(){
        if (fAjaxObject.readyState == 4 && fAjaxObject.status == 200) {
            fHtmlObject.innerHTML = unescape(fAjaxObject.responseText);
            LockAplication( "unlock" );
            //alert(unescape(fAjaxObject.responseText));      
        }
    };
}

/*
 * @Method: AjaxGetXml
 * @Funtion: Obtiene Datos en XML
 **/
function AjaxGetXmlLocked( fUrl, fData, fMethod )
{
    LockAplication( "lock" );
    document.getElementById("name")
    var fAjaxObject = Ajax();
    fData = "Ajax=on&" + fData;
    try {
        if (fMethod.toLowerCase() == "post") {
            fAjaxObject.open(fMethod, fUrl + "?", true);
            fAjaxObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            fAjaxObject.send(fData);
        }
        else {
            fAjaxObject.open(fMethod, fUrl + "?" + fData, true);
            fAjaxObject.send(null);
        }
    } 
    catch (exception) {
        alert("Error: Method AjaxGetData - " + exception);
    }
    fAjaxObject.onreadystatechange = function(){
        if (fAjaxObject.readyState == 4 && fAjaxObject.status == 200) {
            try {
                ReadXml(fAjaxObject);
                //LockAplication( "lock" );
                LockAplication( "unlock" );
                //alert(unescape(fAjaxObject.responseText));            
            } 
            catch (e) {
                alert("Error " + e)
            }
        }
    };
}