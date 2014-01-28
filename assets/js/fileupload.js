jQuery(document).ready(function($){
	jQuery('span#agregar').click(function(){
		agregarArchivo();
	});

	jQuery('input#iniciarCarga').click(function(){
		procesoCargar();
	});
});

//variables comunes
var iBytesUploaded = 0;
var iBytesTotal = 0;
var iPreviousBytesLoaded = 0;
var iMaxFileSize = 1048576; //1mb oooooojoooooo LIMITE PESO DEL ARCHIVO BYTES actualmente no se valida
var oTimer = 0;
var sResultFileSize = 0;
var fs = 0;


function removeDiv(div){ jQuery('div#'+div+'').remove(); }


function secondsToTime(secs){
	var hr = Math.floor(secs / 3600);
	var min = Math.floor((secs - (hr * 3600))/60);
	var sec = Math.floor(secs - (hr * 3600) - (min *60));

	if(hr < 10){hr = "0" + hr;}
	if(min < 10){min = "0" + min;}
	if(sec < 10){sec = "0" + sec;}
	if(hr){hr = "00";}

return hr + ':' + min + ':' +sec;
}


function bytesToSize(bytes){
	var sizes = ['Bytes','KB','MB'];
	if(bytes ==0){return 'n/a';}
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' +sizes[i];
}


function agregarArchivo(){
	var size = jQuery('div#listaarchivos').children().size();
	var contador = 0;
	
		if(size == 0){
			contador = 1;
			jQuery('span#contador').text(contador);
		}else{
				var contTemp = jQuery('span#contador').text();
				var contTemp1 = parseInt(contTemp);
				contador = contTemp1 + 1;
				jQuery('span#contador').text(contador);
		}
	var appendText = "<div id="+contador+"> <span class='fileInput'><label for='File"+contador+"' class='long_desc'>Archivo: </label><input type='file' id='File"+contador+"' /></span> <span class='fileDescription'><label for='Desc"+contador+"' class='long_desc'>Nombre del archivo: </label><input type='text' id="+contador+" /></span> <span class='removeDiv'><input type='button' value='(-)' onclick='removeDiv("+contador+");' /></span></div>";
	jQuery('div#listaarchivos').append(appendText);
}

function procesoCargar(){
	var filetitle = "";
	filetitle += "<div class='fileDetalle'>";
	filetitle += "<div class='fileTNomb'>Nombre</div>";
	filetitle += "<div class='fileTTipo'>Tipo</div>";
	filetitle += "<div class='fileTDetalle'>Detalle proceso</div>";
	filetitle += "<div class='fileTAcciones'>Acciones</div>";
	filetitle += "</div>";
	jQuery('div#fileinformation').append(filetitle);
	
	jQuery('div#listaarchivos').children().each(function(n, i){
		  var id = this.id;
		  fileSelectedMultiple(id);
	});
}


function fileSelectedMultiple(id){
	var oFile = document.getElementById('File'+id).files;
	var fileproperties = ""; var fn = ""; var ft = ""; //var fs = "";
	var fileEmpty = oFile.length;
	
	if(fileEmpty != 0){
		var oFileInner = document.getElementById('File'+id).files[0];
				
		fn = oFileInner.name;
		ft = oFileInner.type;
		fs = bytesToSize(oFileInner.size);
		
		fileproperties += "<div class='fileDetalle'>";
		fileproperties += "<div class='fileUNomb'>"+fn+"</div>";
		fileproperties += "<div class='fileUTipo'>"+ft+"</div>";
		fileproperties += "<div class='fileUProc'>&nbsp; ";
			fileproperties += "<div id='progreso"+id+"' class='progreso'></div>  ";
			fileproperties += "<div id='porcentaje"+id+"' class='porcentaje'></div> ";
			fileproperties += "<div id='velocidad"+id+"' class='velocidad'></div> ";
			fileproperties += "<div id='tiempoResta"+id+"' class='tiempo_resta'></div>  ";
			fileproperties += "<div id='bytesTransferidos"+id+"' class='bytes_transferidos'></div> ";
		fileproperties += "</div>";
		fileproperties += "<div class='fileUAcci'><div id='acciones'>&nbsp;</div></div>";
		fileproperties += "</div>";
		
		jQuery('div#fileinformation').append(fileproperties);
		
		startUploading(id);
	}else{
			fileproperties += "<div class='fileDetale'><div style='font-weight:bold;padding: 0px 50px 0px 0px;'></div></div>";
			console.log(fileproperties);
						
			jQuery('div#fileinformation').append(fileproperties);
	}
}



function getReqObjPost(url, params, func){
	var xmlhttp = false;
	
	if(window.XMLHttpRequest){
		try{
			xmlhttp = new XMLHttpRequest();
		}catch(e){
			xmlhttp = false;
		}
	}else if(window.ActiveXObject){
		try{
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){
				xmlhttp = false;
			}
		}
	}
	
	if(xmlhttp){
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4){	
				if(xmlhttp.status==200){
					var str = xmlhttp.responseText;
					var xml = xmlhttp.responseXML;
					//func(xml,str);
					//console.log("El STR "+str+ " XML "+xml );
				}else{
					//handleErrFullPage(xmlhttp.responseText);
					//console.log("ERROR");
				}
			}
		};
		//var now = new Date();
		//params = params + "&cdate"+now.getSeconds()+"=" + encodeURIComponent(now);
		//params = params;
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		xmlhttp.send(params);
	}
}



function startUploading(id){
	iPreviousBytesLoaded = 0;
	
	var oFile = document.getElementById('File'+id);
	var file = oFile.files[0];
	
	var formData = new FormData();
	formData.append('file', file);
	
	var oXUR = new XMLHttpRequest();
	oXUR.id = id;
	oXUR.upload.id = id;
	oXUR.upload.addEventListener('progress', function(e){
		if(e.lengthComputable){
			iBytesUploaded = e.loaded;
			iBytesTotal = e.total;
			var iPercentComplete = Math.round(e.loaded * 100 / e.total);
			var iBytesTransfered = bytesToSize(iBytesUploaded);
			
			console.log("Percent Completed: "+iPercentComplete);	//contador
			console.log("Bytes Transfered: "+iBytesTransfered);	//contador
	
			var progreso = document.getElementById('progreso'+this.id);
			progreso.style.display = 'block';
			progreso.style.width = '0px';
			
			document.getElementById('porcentaje'+this.id).innerHTML = iPercentComplete.toString() + '%';
			document.getElementById('bytesTransferidos'+this.id).innerHTML = iBytesTransfered;
			document.getElementById('progreso'+this.id).style.width = (iPercentComplete * 1).toString() + 'px';
		}else{
				console.log("No se puede calcular el peso del archivo");
		}
	}, false);
	
	
	oXUR.addEventListener('load', function(e){
		console.log("oXUR terminado: "+oXUR.id);
		/*
		var progreso = document.getElementById('progreso'+this.id);
		progreso.style.display = 'block';
		progreso.style.width = '0px';
		*/
		document.getElementById('porcentaje'+this.id).innerHTML = iPercentComplete.toString() + '%';
		document.getElementById('bytesTransferidos'+this.id).innerHTML = iBytesTransfered;
		document.getElementById('progreso'+this.id).style.width = (iPercentComplete * 1).toString() + 'px';
	},false);
	
	//oXUR.addEventListener('error', finalizaCarga, false);
	//oXUR.addEventListener('abort', finalizaCarga, false);
	
	oXUR.open('POST', '../cbc_intranet/wp-content/plugins/wp-filebase/classes/FileUpload.php', 'false');
	oXUR.send(formData);
	
}

function finalizaCarga(e){
	/*
	var oUploadResponse = document.getElementById('upload_response');
	oUploadResponse.innerHTML = e.target.responseText;
	oUploadResponse.style.display = 'block';

	document.getElementById('progress_percent').innerHTML = '100%';
	document.getElementById('progress').style.width = '400px';
	document.getElementById('filesize').innerHTML = sResultFileSize;
	document.getElementById('remaining').innerHTML = '| 00:00:00';
	*/
	
}

function uploadError(e){
	//document.getElementById('error2').style.display = 'block';
	clearInterval(oTimer);
}

function uploadAbort(e){
	//document.getElementById('abort').style.display = 'block';
	clearInterval(oTimer);
}



function doInnerUpdates(){
	var iCB = iBytesUploaded;
	var iDiff = iCB - iPreviousBytesLoaded;

	if(iDiff == 0){	return;	}

	iPreviousBytesLoaded = iCB;
	iDiff = iDiff * 2;
	var iBytesRem = iBytesTotal - iPreviousBytesLoaded;
	var secondsRemaining = iBytesRem / iDiff;

	var iSpeed = iDiff.toString() + 'B/s';
	if(iDiff > 1048576){
		iSpeed = (Math.round(iDiff * 100/(1048576))/100).toString() + 'MB/s';
	}else if(iDiff > 1024){
			iSpeed = (Math.round(iDiff * 100/(1024))/100).toString() + 'MB/s';
	}
	
	document.getElementById('speed').innerHTML = iSpeed;
	document.getElementById('remaining').innerHTML = '| '+secondsToTime(secondsRemaining);
}
