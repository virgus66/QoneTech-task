function getHttpObject() {
    var xmlHttp=null;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }
    catch (e) {
        // Internet Explorer
        try {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}

function settingsChangeListener(id) {
    var control=document.getElementById(id);

    if (control.innerHTML == 'Delete') confirm('Are you sure you want to delete it?')

    xmlHttp=getHttpObject();
    if (xmlHttp==null) {
        alert ("Your browser does not support AJAX!");
        return;
    }
    
    // console.log(control)
    if ( control.hasAttribute('value') && (control.value == '' || control.value == null) ) {
        alert('You need to provide name');
        control.value = control.getAttribute('oldvalue')
        return;
    }
    if ( checkForDuplicates(control) ) return;

    newValue=encodeURIComponent(control.value);
    var url = location.origin + '/TestWebApp/' + "ajaxsettingschanger.php"+"?elementID="+id+"&newValue="+newValue;
    xmlHttp.onreadystatechange=settingsChangeResult;
    
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function settingsChangeListenerMultiInput(id,inputsDivID) {
    let stopFunction = false;
    xmlHttp=getHttpObject();
    if (xmlHttp==null) {
        alert ("Your browser does not support AJAX!");
        return;
    }

    var url = location.origin + '/TestWebApp/' + "ajaxsettingschanger.php";  
    console.log(url)
    var parameters="elementID="+id;
    $('#'+inputsDivID).find('input[type=text],input[type=password],input[type=hidden],input[type=date],textarea,select').each( function(i,e) { 
        if (e.value == '' || e.value == null) {
            alert('You need to provide name for new task');
            stopFunction = true;
            return false;
        }
        parameters=parameters+"&"+$(this).attr('id')+"="+encodeURIComponent($(this).val());        
    });

    if ( stopFunction ) return;
    if ( checkForDuplicates() ) return;

    $('#taskName').val('').removeClass('have-digit');

    url=url + "?" + parameters    
    xmlHttp.onreadystatechange=settingsChangeResult
        // .then( res => console.log(res) )
        // .catch(err=> console.log(err));
    
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function settingsChangeResult()
{    
    // return new Promise( (res,rej) => {
        if (xmlHttp.readyState==4) { 
            response=xmlHttp.responseText.toString();
            console.log(response)
            eval(response);
            // res(response)
        }
    // })
}