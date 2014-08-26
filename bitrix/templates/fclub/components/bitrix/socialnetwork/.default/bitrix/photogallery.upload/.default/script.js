var temp = [], bShowApplet = false;
if (typeof oText != "object")
var oText = {};
function __Browser(){
	var a=navigator.userAgent.toLowerCase();
	this.isOpera=(a.indexOf("opera")!=-1);
	this.isKonq=(a.indexOf('konqueror')!=-1);
	this.isSafari=(a.indexOf('safari')!=-1)&&(a.indexOf('mac')!=-1);
	this.isKhtml=this.isSafari||this.isKonq;
	this.isIE=(a.indexOf("msie")!=-1)&&!this.isOpera;
	this.isWinIE=this.isIE&&(a.indexOf("win")!=-1);
	this.isCSS1Compat=(!this.isIE)||(document.compatMode&&document.compatMode=="CSS1Compat");
}
var __browser=new __Browser();

//Create set/get expando methods for ActiveX
function _createExpandoMethods(id){
	var o=document.getElementById(id);
	var props=new Array();
	var hasPaneItemApiElements=false;
	for (propName in o){
		var c=propName.charAt(0);
		if (c==c.toUpperCase()){
			props.push(propName);
			if (propName=="PaneItemDesign"){
				hasPaneItemApiElements=true;
			}
		}
	}
	for (i=0;i<props.length;i++){
		//Check whether property is indexed
		if (typeof(o[props[i]])=="unknown"){
			eval("o.set"+props[i]+"=function(i,v){this."+props[i]+"(i)=v;};");
			eval("o.get"+props[i]+"=function(i){return this."+props[i]+"(i);};");
		}
		else{
			eval("o.set"+props[i]+"=function(v){this."+props[i]+"=v};");
			eval("o.get"+props[i]+"=function(){return this."+props[i]+"};");
		}
	}
	if (hasPaneItemApiElements){
		eval("o.setPaneItemDesign = function(Pane, Index, Value){this.PaneItemDesign(Pane, Index) = Value;};");
		eval("o.getPaneItemDesign = function(Pane, Index){return this.PaneItemDesign(Pane, Index);};");
		//eval("o.getPaneItemCount = function(Pane){return this.PaneItemCount(Pane);};");
		//eval("o.getPaneItemChecked = function(Index){return this.PaneItemChecked(Index);};");
		//eval("o.getPaneItemCanBeUploaded = function(Index){return this.PaneItemCanBeUploaded(Index);};");
		eval("o.getPaneItemSelected = function(Pane, Index){return this.PaneItemSelected(Pane, Index);};");
		eval("o.setPaneItemEnabled = function(Pane, Index, Value){this.PaneItemEnabled(Pane, Index) = Value;};");
		eval("o.getPaneItemEnabled = function(Pane, Index){return this.PaneItemEnabled(Pane, Index);};");
	}
}

//Installation instructions
function _addInstructions(obj){
	obj.instructionsEnabled=true;
}

function ControlWriter(id,width,height){
	//Private
	this._params=new Array();
	this._events=new Array();

	_addInstructions(this);

	this._getObjectParamHtml=function(name,value){
		return "<param name=\""+name+"\" value=\""+value+"\" />";
	}

	this._getObjectParamsHtml=function(){
		var r="";
		var p=this._params;
		var i;
		for (i=0;i<p.length;i++){
			r+=this._getObjectParamHtml(p[i].name,p[i].value);
		}
		return r;
	}

	this._getObjectEventsHtml=function(){
		var r="";
		var e=this._events;
		for (i=0;i<e.length;i++){
			r+=this._getObjectParamHtml(e[i].name+"Listener",e[i].listener);
		}
		return r;
	}

	this._getEmbedParamHtml=function(name,value){
		return " "+name+"=\""+value+"\"";
	}

	this._getEmbedParamsHtml=function(){
		var r="";
		var p=this._params;
		var i;
		for (i=0;i<p.length;i++){
			r+=this._getEmbedParamHtml(p[i].name,p[i].value);
		}
		return r;
	}

	this._getEmbedEventsHtml=function(){
		var r="";
		var e=this._events;
		for (i=0;i<e.length;i++){
			r+=this._getEmbedParamHtml(e[i].name+"Listener",e[i].listener);
		}
		return r;
	}

	//Public

	//Properties
	this.id=id;
	this.width=width;
	this.height=height;

	this.activeXControlEnabled=true;
	this.activeXControlVersion="";

	this.javaAppletEnabled=navigator.javaEnabled();
	this.javaAppletCodeBase="./";
	this.javaAppletCached=true;
	this.javaAppletVersion="";

	this.fullPageLoadListenerName=null;

	//Methods
	this.addParam=function(paramName,paramValue){
		var p=new Object();
		p.name=paramName;
		p.value=paramValue;
		this._params.push(p);
	}

	this.addEventListener=function(eventName,eventListener){
		var p=new Object();
		p.name=eventName;
		p.listener=eventListener;
		this._events.push(p);
	}

	this.getActiveXInstalled=function(){
		if (this.activeXProgId){
			try{
				var a=new ActiveXObject(this.activeXProgId);
				return true;
			}
			catch(e){
				return false;
			}
		}
		return false;
	}

	this.getHtml=function(){
		var r="";
		if (this.fullPageLoadListenerName){
			r+="<" + "script type=\"text/javascript\">";
			r+="var __"+this.id+"_pageLoaded=false;";
			r+="var __"+this.id+"_controlLoaded=false;";
			r+="function __fire_"+this.id+"_fullPageLoad(){";
			r+="if (__"+this.id+"_pageLoaded&&__"+this.id+"_controlLoaded){";
			r+=this.fullPageLoadListenerName + "();";
			r+="}";
			r+="}";
			var pageLoadCode="new Function(\"__"+this.id+"_pageLoaded=true;__fire_"+this.id+"_fullPageLoad();\")";
			if (__browser.isWinIE){
				r+="window.attachEvent(\"onload\","+pageLoadCode+");";
			}
			else{
				r+="var r=window.addEventListener?window:document.addEventListener?document:null;";
				r+="if (r){r.addEventListener(\"load\","+pageLoadCode+",false);}";
			}
			r+="<"+"/script>";
		}

		//ActiveX control
		if(__browser.isWinIE&&this.activeXControlEnabled){
			var v=this.activeXControlVersion.replace(/\./g,",");
			var cb=this.activeXControlCodeBase+(v==""?"":"#version="+v);

			r+="<" + "script for=\""+this.id+"\" event=\"InitComplete()\">";
			r+="_createExpandoMethods(\""+this.id+"\");";
			if (this.fullPageLoadListenerName){
				r+="__"+this.id+"_controlLoaded=true;";
				r+="__fire_"+this.id+"_fullPageLoad();";
			}
			r+="<"+"/script>";

			r+="<object id=\""+this.id+"\" name=\""+this.id+"\" classid=\"clsid:"+this.activeXClassId+"\" codebase=\""+cb+"\" width=\""+this.width+"\" height=\""+this.height+"\">";
			if (this.instructionsEnabled){
				r+=this.instructionsCommon;
				var isXPSP2 = (window.navigator.userAgent.indexOf("SV1") != -1) || (window.navigator.userAgent.indexOf("MSIE 7.0") != -1);
				if (isXPSP2){
					r+=this.instructionsWinXPSP2;
				}
				else{
					r+=this.instructionsNotWinXPSP2;
				}
			}
			r+=this._getObjectParamsHtml();
			r+="</object>";

			//Event handlers
			var e=this._events;
			var eventParams;
			for (i=0;i<e.length;i++){
				if (this.controlClass=="FileDownloader"){
					switch (e[i].name){
						case "DownloadComplete":
						eventParams="Value";
						break;
						case "DownloadItemComplete":
						eventParams="Result, ErrorPage, Url, FileName, ContentType, FileSize";
						break;
						case "DownloadStep":
						eventParams="Step";
						break;
						case "Progress":
						eventParams="PercentTotal, PercentCurrent, Index";
						break;
						case "Error":
						eventParams="ErrorCode, HttpErrorCode, ErrorPage, Url, Index";
						break;
						default:
						eventParams="";
					}
					r+="<script for=\""+this.id+"\" event=\""+e[i].name+"("+eventParams+")\">";
					r+=e[i].listener+"("+eventParams+");";
					r+="<"+"/script>";
				}
				else {
					switch (e[i].name){
						case "Progress":
						eventParams="Status, Progress, ValueMax, Value, StatusText";
						break;
						case "InnerComplete":
						eventParams="Status, StatusText";
						break;
						case "AfterUpload":
						eventParams="htmlPage";
						break;
						case "ViewChange":
						case "SortModeChange":
						eventParams="Pane";
						break;
						case "Error":
						eventParams="ErrorCode, HttpResponseCode, ErrorPage, AdditionalInfo";
						break;
						case "PackageBeforeUpload":
						eventParams = "PackageIndex";
						break;
						case "PackageError":
						eventParams = "PackageIndex, ErrorCode, HttpResponseCode, ErrorPage, AdditionalInfo";
						break;
						case "PackageComplete":
						eventParams = "PackageIndex";
						break;
						case "PackageProgress":
						eventParams = "PackageIndex, Status, Progress, ValueMax, Value, StatusText";
						break;
						default:
						eventParams="";
					}
				}
				r+="<" + "script for=\""+this.id+"\" event=\""+e[i].name+"("+eventParams+")\">";
				if (e[i].name=="BeforeUpload"){
					r+="return ";
				}
				r+=e[i].listener+"("+eventParams+");";
				r+="<"+"/script>";
			}

		}
		else
		//Java appplet
		if(this.javaAppletEnabled){
			if (this.fullPageLoadListenerName){
				r+="<" + "script type=\"text/javascript\">";
				r+="function __"+this.id+"_InitComplete(){";
				r+="__"+this.id+"_controlLoaded=true;";
				r+="__fire_"+this.id+"_fullPageLoad();";
				r+="}";
				r+="<"+"/script>";
			}

			//<object> for IE and <applet> for Safari
			if (__browser.isWinIE||__browser.isKhtml){
				if (__browser.isWinIE){
					r+="<object id=\""+this.id+"\" classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" codebase=\""+window.location.protocol+"//java.sun.com/update/1.4.2/jinstall-1_4-windows-i586.cab#Version=1,4,0,0\" width=\""+this.width+"\" height=\""+this.height+"\">";
				}
				else{
					r+="<applet id=\""+this.id+"\" code=\""+this.javaAppletClassName+"\" java_codebase=\"../\" align=\"baseline\" archive=\""+this.javaAppletJarFileName+"\" mayscript=\"true\" scriptable=\"true\" width=\""+this.width+"\" height=\""+this.height+"\">";
				}

				if (this.javaAppletCached&&this.javaAppletVersion!=""){
					r+=this._getObjectParamHtml("cache_archive",this.javaAppletJarFileName);
					var v=this.javaAppletVersion.replace(/\,/g,".");
					//r+=this._getObjectParamHtml("cache_version",v+","+v);
					r+=this._getObjectParamHtml("cache_version",v);
				}

				r+=this._getObjectParamHtml("type","application/x-java-applet;version=1.4");
				r+=this._getObjectParamHtml("codebase",this.javaAppletCodeBase);
				r+=this._getObjectParamHtml("archive",this.javaAppletJarFileName);
				r+=this._getObjectParamHtml("code",this.javaAppletClassName);
				r+=this._getObjectParamHtml("scriptable","true");
				r+=this._getObjectParamHtml("mayscript","true");

				r+=this._getObjectParamsHtml();

				r+=this._getObjectEventsHtml();

				if (this.fullPageLoadListenerName){
					r+=this._getObjectParamHtml("InitCompleteListener","__"+this.id+"_InitComplete");
				}
				if (__browser.isWinIE){
					r+="</object>";
				}
				else{
					r+="</applet>";
				}
			}
			//<embed> for all other browsers
			else{
				r+="<embed id=\""+this.id+"\" type=\"application/x-java-applet;version=1.4\" codebase=\""+this.javaAppletCodeBase+"\" code=\""+this.javaAppletClassName+"\" archive=\""+this.javaAppletJarFileName+"\" width=\""+this.width+"\" height=\""+this.height+"\" scriptable=\"true\" mayscript=\"true\" pluginspage=\""+window.location.protocol+"//java.sun.com/products/plugin/index.html#download\"";

				if (this.javaAppletCached&&this.javaAppletVersion!=""){
					r+=this._getEmbedParamHtml("cache_archive",this.javaAppletJarFileName);
					var v=this.javaAppletVersion.replace(/\,/g,".");
					//r+=this._getEmbedParamHtml("cache_version",v+","+v);
					r+=this._getEmbedParamHtml("cache_version",v);
				}

				r+=this._getEmbedParamsHtml();

				r+=this._getEmbedEventsHtml();

				if (this.fullPageLoadListenerName){
					r+=this._getEmbedParamHtml("InitCompleteListener","__"+this.id+"_InitComplete");
				}
				r+=">";
				r+="</embed>";
			}
		}
		else
		{
			r+="Your browser is not supported.";
		}

		//For backward compatibility
		this.controlType=this.getControlType();

		return r;
	}

	this.getControlType=function(){
		return (__browser.isWinIE&&this.activeXControlEnabled)?"ActiveX":(this.javaAppletEnabled?"Java":"None");
	}

	this.writeHtml=function(){
		document.write(this.getHtml());
	}
}

function ImageUploaderWriter(id,width,height){
	this._base=ControlWriter;
	this._base(id,width,height);
	//These properties should be modified for private-label versions only
	this.activeXControlCodeBase="ImageUploader.cab";
	this.activeXClassId="6E5E167B-1566-4316-B27F-0DDAB3484CF7";
	this.activeXProgId="Aurigma.ImageUploader.5";
	this.javaAppletJarFileName="ImageUploader.jar";
	this.javaAppletClassName="com.aurigma.imageuploader.ImageUploader.class";

	//Extend
	this.showNonemptyResponse="off";
	this._getHtml=this.getHtml;
	this.getHtml=function(){
		var r="";
		if (this.showNonemptyResponse!="off"){
			r+="<" + "script type=\"text/javascript\">";
			r+="function __"+this.id+"_InnerComplete(Status,StatusText){";
			r+="if (new String(Status)==\"COMPLETE\" && new String(StatusText).replace(/\\s*/g,\"\")!=\"\"){";
			if (this.showNonemptyResponse=="dump"){
				r+="var f=document.createElement(\"fieldset\");";
				r+="var l=f.appendChild(document.createElement(\"legend\"));";
				r+="l.appendChild(document.createTextNode(\"Server Response\"));";
				r+="var d=f.appendChild(document.createElement(\"div\"));";
				r+="d.innerHTML=StatusText;";
				r+="var b=f.appendChild(document.createElement(\"button\"));";
				r+="b.appendChild(document.createTextNode(\"Clear Server Response\"));";
				r+="b.onclick=function(){var f=this.parentNode;f.parentNode.removeChild(f)};";
				r+="document.body.appendChild(f);";
			}
			else{
				var s="";
				for (var i=0;i<80;i++){s+="-";}
				r+="alert(\""+s+"\\r\\nServer Response\\r\\n"+s+"\\r\\n\"+StatusText);";
			}
			r+="}";
			r+="}";
			r+="<"+"/script>";
			this.addEventListener("InnerComplete","__"+this.id+"_InnerComplete");
		}
		return r+this._getHtml();
	}
}

function ThumbnailWriter(id,width,height){
	this._base=ControlWriter;
	this._base(id,width,height);
	//These properties should be modified for private label versions only
	this.activeXControlCodeBase="ImageUploader.cab";
	this.activeXClassId="27BE1679-6AEE-4CE0-9748-7773EA94C3AF";
	this.activeXProgId="Aurigma.Thumbnail.5";
	this.javaAppletJarFileName="ImageUploader.jar";
	this.javaAppletClassName="com.aurigma.imageuploader.Thumbnail.class";
}

function ShellComboBoxWriter(id,width,height){
	this._base=ControlWriter;
	this._base(id,width,height);
	//These properties should be modified for private label versions only
	this.activeXControlCodeBase="ImageUploader.cab";
	this.activeXClassId="8B1A14AF-E603-4356-B687-1F7D46522DD3";
	this.activeXProgId="Aurigma.ShellCombo.5";
	this.javaAppletJarFileName="ImageUploader.jar";
	this.javaAppletClassName="com.aurigma.imageuploader.ShellComboBox.class";
}

function UploadPaneWriter(id,width,height){
	this._base=ControlWriter;
	this._base(id,width,height);
	//These properties should be modified for private label versions only
	this.activeXControlCodeBase="ImageUploader.cab";
	this.activeXClassId="6D9D27EE-675E-4BDE-84B1-1C9A94F98555";
	this.activeXProgId="Aurigma.UploadPane.5";
	this.javaAppletJarFileName="ImageUploader.jar";
	this.javaAppletClassName="com.aurigma.imageuploader.UploadPane.class";
}

function FileDownloaderWriter(id,width,height){
	this._base=ControlWriter;
	this._base(id,width,height);
	//These properties should be modified for private label versions only
	this.activeXControlCodeBase="FileDownloader.cab";
	this.activeXClassId="E1A26BBF-26C0-401D-B82B-5C4CC67457E0";
	this.activeXProgId="Aurigma.FileDownloader.1";
	this.javaAppletEnabled=false;
	this.controlClass="FileDownloader";
}

function getControlObject(id){
	if (__browser.isSafari){
		return document[id];
	}
	else{
		return document.getElementById(id);
	}
}

function getImageUploader(id){
	return getControlObject(id);
}

function getFileDownloader(id){
	return getControlObject(id);
}

// User functions
var PhotoClass =
{
	Uploader: null,
	FileCount: 0,
	Flags: {},
	oFile: {},
	_this: this,
	active: {},
	photo_album_id : 0,
	Temp:{},
	WaterMarkInfo : {},
	Init: function()
	{
		this.Uploader = getImageUploader("ImageUploader1");
		PCloseWaitMessage("waitwindow", true);
	},

	ChangeFileCount: function()
	{
		if (this.Uploader)
		{
			var guid = 0;
			this.FileCount = this.Uploader.getUploadFileCount();
			this.FileCount = parseInt(this.FileCount);

			for (var i = 1; i <= this.FileCount; i++)
			{
				guid = this.Uploader.getUploadFileGuid(i);
				if (typeof(this.oFile[guid]) != "object" || (!this.oFile[guid]) || (this.oFile[guid] == null))
				{
					var sFileName = this.Uploader.getUploadFileName(i);
					if (!sFileName || sFileName == 'undefined')
						sFileName = 'noname';
					sFileName = "" + sFileName;
					if (sFileName.search(/\\/) > 0)
						sFileName = sFileName.replace(/\\/g, "/");
					var aFileName = sFileName.split("/");
					if (aFileName && aFileName.length > 0)
					{
						sFileName = aFileName[aFileName.length-1];
					}
					this.oFile[guid] = {"Title":sFileName, "Public" : "N", "Tag":"", "Description":""};
				}
			}

			if (this.FileCount <= 0)
			{
				document.getElementById("photo_count_to_upload").innerHTML = oText["NoPhoto"];
				if (document.getElementById("Send"))
				{
					document.getElementById("Send").onclick = function(){return false;};
					document.getElementById("SendColor").style.color = "gray";
				}
				this.Flags["SetButtonFunction"] = "N";
			}
			else
			{
				if (this.Flags["SetButtonFunction"] != "Y")
				{
					if (document.getElementById("Send"))
					{
						document.getElementById("Send").onclick = function()
						{
							if (getImageUploader("ImageUploader1"))
							getImageUploader("ImageUploader1").Send();
							return;
						}
						document.getElementById("SendColor").style.color = "#4E4EA5";
					}
					this.Flags["SetButtonFunction"] = "Y";
				}
				document.getElementById("photo_count_to_upload").innerHTML = this.FileCount;
			}
		}
	},

	ChangeSelection: function()
	{
		var thumbnail1 = getImageUploader("Thumbnail1");
		if (this.Uploader && thumbnail1 && this.Uploader.getUploadFileSelected)
		{
			this.HideDescription();
			for (var i = 1; i <= this.FileCount; i++)
			{
				try
				{
					if (this.Uploader.getUploadFileSelected(i))
					{
						this.active.push(this.Uploader.getUploadFileGuid(i));
					}
				}
				catch(e)
				{
//					alert('From ChangeSelection i=' + i + '\n\n' + e);
				}
			}
			this.ShowDescription();
		}
	},

	ShowDescription: function()
	{
		var thumbnail1 = getImageUploader("Thumbnail1");
		var bEmptyFields = {"Title" : false, "Public" : false, "Tag" : false, "Description" : false};
		var sFirstFields = {"Title" : false, "Public" : false, "Tag" : false, "Description" : false};
		if (!document.getElementById("PhotoTag"))
			bEmptyFields["Tag"] = true;

		this.Temp["Fields"] = {
			"Title" : "",
			"Public" : "",
			"Tag" : "",
			"Description" : ""};

		if (this.Uploader && thumbnail1 && this.active.length > 0)
		{
			sFirstFields["Title"] = this.oFile[this.active[0]]["Title"];
			sFirstFields["Public"] = (this.oFile[this.active[0]]["Public"] == "Y" ? "Y" : "N");
			sFirstFields["Tag"] = this.oFile[this.active[0]]["Tag"];
			sFirstFields["Description"] = this.oFile[this.active[0]]["Description"];

			for (var ii = 0; ii < this.active.length; ii++)
			{
				if (sFirstFields["Title"] != this.oFile[this.active[ii]]["Title"])
					bEmptyFields["Title"] = true;
				if (sFirstFields["Public"] != this.oFile[this.active[ii]]["Public"])
					bEmptyFields["Public"] = true;
				if (sFirstFields["Tag"] != this.oFile[this.active[ii]]["Tag"])
					bEmptyFields["Tag"] = true;
				if (sFirstFields["Description"] != this.oFile[this.active[ii]]["Description"])
					bEmptyFields["Description"] = true;

				if (bEmptyFields["Title"] && bEmptyFields["Public"] && bEmptyFields["Tags"] && bEmptyFields["Description"])
					break;
			}

			document.getElementById("PhotoTitle").disabled = false;
			if (document.getElementById("PhotoPublic"))
			{
				document.getElementById("PhotoPublic").disabled = true;
				if (!bEmptyFields["Public"] && sFirstFields["Public"] == "Y" || this.active.length == 1)
					document.getElementById("PhotoPublic").disabled = false;
			}
			if (document.getElementById("PhotoTag"))
				document.getElementById("PhotoTag").disabled = false;
			document.getElementById("PhotoDescription").disabled = false;

			document.getElementById("PhotoTitle").value = (bEmptyFields["Title"] ? "" : sFirstFields["Title"]);
			if (document.getElementById("PhotoPublic"))
				document.getElementById("PhotoPublic").checked = ((!bEmptyFields["Public"] && sFirstFields["Public"] == "Y") ? true : false);
			if (document.getElementById("PhotoTag"))
				document.getElementById("PhotoTag").value = (bEmptyFields["Tag"] ? "" : sFirstFields["Tag"]);
			document.getElementById("PhotoDescription").value = (bEmptyFields["Description"] ? "" : sFirstFields["Description"]);
			thumbnail1.setGuid(this.active[0]);
			this.Temp["Fields"] = {
				"Title" : (bEmptyFields["Title"] ? "" : sFirstFields["Title"]),
				"Public" : (bEmptyFields["Public"] ? "" : sFirstFields["Public"]),
				"Tag" : (bEmptyFields["Tag"] ? "" : sFirstFields["Tag"]),
				"Description" : (bEmptyFields["Description"] ? "" : sFirstFields["Description"])};
		}
	},

	HideDescription: function()
	{
		var thumbnail1 = getImageUploader("Thumbnail1");
		if (this.Uploader && thumbnail1)
		{
			var arValue = {
				"Title" : document.getElementById("PhotoTitle").value,
				"Public" : "N",
				"Description" : document.getElementById("PhotoDescription").value,
				"bTag" : false};
			if (document.getElementById("PhotoTag"))
			{
				arValue["Tag"] = document.getElementById("PhotoTag").value;
				arValue["bTag"] = true;
			}
			if (document.getElementById("PhotoPublic"))
			{
				if (document.getElementById("PhotoPublic").checked)
					arValue["Public"] = "Y";
			}
			for (var ii = 0; ii < this.active.length; ii++)
			{
				
				if (this.Temp["Fields"]["Title"] != arValue["Title"])
					this.oFile[this.active[ii]]["Title"] = arValue["Title"];
				if (this.Temp["Fields"]["Public"] != "")
					this.oFile[this.active[ii]]["Public"] = arValue["Public"];
				if (this.Temp["Fields"]["Description"] != arValue["Description"])
					this.oFile[this.active[ii]]["Description"] = arValue["Description"];
				if (arValue["bTag"] && (this.Temp["Fields"]["Tag"] != arValue["Tag"]))
					this.oFile[this.active[ii]]["Tag"] = arValue["Tag"];
			}
			
			this.active = [];
			document.getElementById("PhotoTitle").disabled = true;
			document.getElementById("PhotoTitle").value = "";
			if (document.getElementById("PhotoPublic"))
			{
				document.getElementById("PhotoPublic").disabled = true;
				document.getElementById("PhotoPublic").checked = false;
			}
			if (document.getElementById("PhotoTag"))
			{
				document.getElementById("PhotoTag").disabled = true;
				document.getElementById("PhotoTag").value = "";
			}
			document.getElementById("PhotoDescription").disabled = true;
			document.getElementById("PhotoDescription").value = "";
			thumbnail1.setGuid("");
		}
	},

	PackageBeforeUpload: function(PackageIndex)
	{
		oWidth = {'count' : 0, 'width_all' : 0, 'width' : 0, 'height_all' : 0, 'height' : 0};
		iSize = 0;
		iHeight = 0;
		iKoeff = 0;
//		try
//		{
		if (!(!this.WaterMarkInfo['text'] || this.WaterMarkInfo['text'].length <= 0))
		{
			var FileCount = this.Uploader.getUploadFileCount();
			if (FileCount > this.Uploader.getFilesPerOnePackageCount())
				FileCount = this.Uploader.getFilesPerOnePackageCount();
			
			for (var ii = 1; ii <= FileCount; ii++)
			{
				if (this.Uploader.getUploadFileWidth(ii) < oParams["min_size_picture"] ||
					this.Uploader.getUploadFileHeight(ii) < oParams["min_size_picture"])
					continue;
				oWidth['count']++;
				oWidth['width_all'] += this.Uploader.getUploadFileWidth(ii);
				oWidth['height_all'] += this.Uploader.getUploadFileHeight(ii);
			}
			

			if (oWidth['count'] > 0 && oWidth['width_all'] > 0 && oWidth['height_all'] > 0)
			{
				oWidth['width'] = parseInt(oWidth['width_all'] / oWidth['count']);
				oWidth['height'] = parseInt(oWidth['height_all'] / oWidth['count']);

				for (var ii = 1; ii <= this.Uploader.getUploadThumbnailCount(); ii++)
				{
					if (this.Uploader.getUploadThumbnailWidth(ii) < oParams["min_size_picture"] && (ii != 3))
					{
						this.Uploader.setUploadThumbnailWatermark(ii, "");
					}
					else
					{
						// Big - 10%
						// Middle - 5%
						// Small - 2%
						iKoeff = oWidth['height'];
						if (oWidth['width'] > oWidth['height'])
						iKoeff = oWidth['width'];
						iKoeff = (this.Uploader.getUploadThumbnailWidth(ii) / iKoeff);
						if (this.Uploader.getUploadThumbnailFitMode(ii) == 5)
							iKoeff = 1;

						if (parseInt(oWidth['width'] * iKoeff + 1) < oParams["min_size_picture"])
						{
							this.Uploader.setUploadThumbnailWatermark(ii, "");
						}
						else
						{
							var iWidth = this.Uploader.getUploadThumbnailWidth(ii);
							if (this.Uploader.getUploadThumbnailFitMode(ii) == 5)
								iWidth = oWidth['width'];
							if (this.WaterMarkInfo['size'] == 'big')
							{
								iSize = parseInt(iWidth * 0.11);
								if (iSize > 75)
								iSize = 75;
							}
							else if (this.WaterMarkInfo['size'] == 'small')
							{
								iSize = parseInt(iWidth * 0.05);
								if (iSize > 35)
								iSize = 35;
							}
							else
							{
								iSize = parseInt(iWidth * 0.07);
								if (iSize > 55)
								iSize = 55;
							}


							if (parseInt(iSize * this.WaterMarkInfo['text'].length*0.6) > parseInt(oWidth['width'] * iKoeff+1))
							{
								iSize = parseInt((oWidth['width']*iKoeff+1)/(this.WaterMarkInfo['text'].length*0.6));
							}

							if (iSize < 9)
							iSize = 9;

							this.Uploader.setUploadThumbnailWatermark(ii, "opacity=100;Font=Arial;size=" + iSize +
							";FillColor=#" + this.WaterMarkInfo["color"] + ";Position=" + this.WaterMarkInfo["position"] +
							";text='" + this.WaterMarkInfo['text'].split("'").join("''") + "'");
						}
					}
				}
				return;
			}
		}
//		}
//		catch(e){}
		

		for (var ii = 1; ii <= this.Uploader.getUploadThumbnailCount(); ii++)
		{
			if (this.Uploader.getUploadThumbnailWidth(ii) < oParams["min_size_picture"])
				continue;
			this.Uploader.setUploadThumbnailWatermark(ii, "");
		}
		return;
	},

	BeforeUpload: function()
	{
		var thumbnail1 = getImageUploader("Thumbnail1");
		if (thumbnail1)
		thumbnail1.setGuid("");
		if (this.Uploader)
		{
			this.HideDescription();
			//			try
			//			{
			var guid = 0;
			this.FileCount = this.Uploader.getUploadFileCount();

			for (var i = 1; i <= this.FileCount; i++)
			{
				guid = this.Uploader.getUploadFileGuid(i);
				this.Uploader.setUploadFileDescription(i, this.oFile[guid]["Description"]);
				this.Uploader.AddField('Title_'+i, this.oFile[guid]["Title"]);
				this.Uploader.AddField('Public_'+i, this.oFile[guid]["Public"]);
				if (this.oFile[guid]["Tag"])
					this.Uploader.AddField('Tags_'+i, this.oFile[guid]["Tag"]);
			}
			// Additional sights
			if (oAppletInfo && typeof oAppletInfo == "object")
			{
				for (var ii in oAppletInfo)
				{
					this.Uploader.UploadThumbnailAdd("Fit", oAppletInfo[ii]['size'], oAppletInfo[ii]['size']);
					this.Uploader.setUploadThumbnailJpegQuality(this.Uploader.getUploadThumbnailCount(), oAppletInfo[ii]['quality']);
				}
				oAppletInfo = false;
			}
			sWatermarkText = "";
			if (document.getElementById("watermark"))
				sWatermarkText = document.getElementById("watermark").value;
			// resize
			if (document.getElementById("photo_resize_size") && document.getElementById("photo_resize_size").value > 0)
			{
				this.Uploader.setUploadThumbnail3FitMode("Fit");
				if (document.getElementById("photo_resize_size").value == 1)
				{
					this.Uploader.setUploadThumbnail3Width(1024);
					this.Uploader.setUploadThumbnail3Height(768);
				}
				else if (document.getElementById("photo_resize_size").value == 2)
				{
					this.Uploader.setUploadThumbnail3Width(800);
					this.Uploader.setUploadThumbnail3Height(600);
				}
				else if (document.getElementById("photo_resize_size").value == 3)
				{
					this.Uploader.setUploadThumbnail3Width(640);
					this.Uploader.setUploadThumbnail3Height(480);
				}
			}
			else if (sWatermarkText.length <= 0)
			{
				this.Uploader.setUploadThumbnail3FitMode("Off");
				this.Uploader.setUploadSourceFile("true");
			}

			// watermark
			if (sWatermarkText.length > 0)
			{
				if (document.getElementById('watermark_copyright') && document.getElementById('watermark_copyright').value == 'hide')
				{
					sWatermarkText = sWatermarkText;
				}
				else
				{
					sWatermarkText = String.fromCharCode(169)+sWatermarkText;
				}
				var watermark = {'color' : 'ffffff',
				'size': 'middle',
				'position': 'TopLeft'};

				if (document.getElementById("watermark_color"))
				watermark["color"] = document.getElementById("watermark_color").value;

				if (document.getElementById("watermark_size"))
				watermark["size"] = document.getElementById("watermark_size").value;

				if (document.getElementById("watermark_position"))
				{
					watermark["position"] = "Top";
					if (document.getElementById("watermark_position").value.substr(0, 1) == "m")
					watermark["position"] = "Center";
					else if (document.getElementById("watermark_position").value.substr(0, 1) == "b")
					watermark["position"] = "Bottom";

					if (document.getElementById("watermark_position").value.substr(1, 1) == "c")
					watermark["position"] += "Center";
					else if (document.getElementById("watermark_position").value.substr(1, 1) == "r")
					watermark["position"] += "Right";
					else
					watermark["position"] += "Left";
					if (watermark["position"] == "CenterCenter")
						watermark["position"] = "Center";
				}

				this.WaterMarkInfo = {
				'text' : sWatermarkText,
				'color' : watermark["color"],
				'size' : watermark["size"],
				'position' : watermark["position"]};
			}

			// constant params
			//				this.Uploader.AddField("UploadThumbnailCount", this.Uploader.getUploadThumbnailCount());
			if (document.getElementById("sessid"))
			this.Uploader.AddField("sessid", document.getElementById("sessid").value);
			if (document.getElementById("photo_album_id"))
			{
				this.Uploader.AddField("photo_album_id", document.getElementById("photo_album_id").value);
				this.photo_album_id = document.getElementById("photo_album_id").value;
			}
			
			this.Uploader.AddField("save_upload", "Y");
			this.Uploader.AddField("AJAX_CALL", "Y");
			this.Uploader.AddField("CACHE_RESULT", "Y");
			if (__browser.isWinIE)
			this.Uploader.AddField("CONVERT", "Y");
			//			}
			//			catch(e){}
		}
	},

	AfterUpload: function(htmlPage)
	{
		var result = {};
		var error = false;
		if (!document.getElementById("photo_error"))
		return;
		document.getElementById("photo_error").innerHTML = "";
		try
		{
			eval("result="+htmlPage);
		}
		catch(e)
		{}

		if (typeof result != "object")
		result = {};

		if (result["status"] == "success")
		{
			for (var key in result["files"])
			{
				if (result["files"][key] && result["files"][key]["status"] != "success")
				{
					if (result["files"][key]["error"])
					{
						document.getElementById("photo_error").innerHTML += result["files"][key]["error"] + " (" + key +  ")<br />";
						error = true;
					}

				}
			}
		}
		else
		{
			if (result["error"])
			{
				document.getElementById("photo_error").innerHTML = result["error"];
				error = true;
			}
		}
		
		if (!error)
		{
			
			if (result['section_id'] > 0)
				jsUtils.Redirect([], window.urlRedirect.replace('#SECTION_ID#', result['section_id']));
			else if (parseInt(this.photo_album_id) > 0)
				jsUtils.Redirect([], window.urlRedirect.replace('#SECTION_ID#', this.photo_album_id));
			else
				jsUtils.Redirect([], window.urlRedirectThis);
		}
	},

	SendTags: function(oObj)
	{
		try
		{
			if (TcLoadTI)
			{
				if (typeof window.oObject[oObj.id] != 'object')
				window.oObject[oObj.id] = new JsTc(oObj);
				return;
			}
			setTimeout(PhotoClass.SendTags(oObj), 10);
		}
		catch(e)
		{
			setTimeout(PhotoClass.SendTags(oObj), 10);
		}
	},

	ChangeMode: function(url)
	{
		if (window.PhotoUserID > 0)
		{
			var TID = CPHttpRequest.InitThread();
			CPHttpRequest.SetAction(TID, function(data){window.location.reload(true);})
			CPHttpRequest.Send(TID, '/bitrix/components/bitrix/photogallery.upload/user_settings.php', {"save":'view_mode', "position":'change', "sessid":document.getElementById('sessid').value});
		}
		else
		{
			return true;
		}
		return false;
	}
}
function PackageBeforeUploadLink()
{
	PhotoClass.PackageBeforeUpload();
}
function BeforeUploadLink()
{
	PhotoClass.BeforeUpload();
}
function AfterUploadLink(htmlPage)
{
	PhotoClass.AfterUpload(htmlPage);
}
function ChangeSelectionLink()
{
	PhotoClass.ChangeSelection();
}
function ChangeFileCountLink()
{
	PhotoClass.ChangeFileCount();
}
function InitLink()
{
	PhotoClass.Init();
}

var WaterMark = {
	oBody : false,
	oSwitcher : false,
	bShowed : false,
	active : "",

	ShowMenu : function(id)
	{
		if (id.length <= 0)
		return false;

		this.Close()

		this.active = id;
		this.oBody = document.getElementById('watermark_' + id + '_container');
		this.oSwitcher = document.getElementById('watermark_' + id + '_switcher');

		this.Show();
		return;
	},

	Show : function()
	{
		if (typeof WaterMark.oBody != "object")
		return false;

		WaterMark.oBody.style.display = 'block';
//		alert(WaterMark.oBody.style.display);

		jsUtils.addEvent(document, "keypress", WaterMark.CheckKeyPress);
		jsUtils.addEvent(document, "click", WaterMark.CheckClickMouse);
	},

	Close : function()
	{
		if (typeof WaterMark.oBody != "object")
		return false;

		WaterMark.oBody.style.display = 'none';

		jsUtils.removeEvent(document, "keypress", WaterMark.CheckKeyPress);
		jsUtils.removeEvent(document, "click", WaterMark.CheckClickMouse);
		return true;
	},

	CheckKeyPress : function(e)
	{
		if(!e) e = window.event
		if(!e) return;
		if(e.keyCode == 27)
		WaterMark.Close();
	},

	CheckClickMouse : function(e)
	{
		if (typeof WaterMark.oBody != "object")
		return false;

        var windowSize = jsUtils.GetWindowSize();
        var x = e.clientX + windowSize.scrollLeft;
        var y = e.clientY + windowSize.scrollTop;
		
		//		/*switch region*/
		var pos1 = jsUtils.GetRealPos(WaterMark.oSwitcher);
		if(x >= pos1['left'] && x <= pos1['right'] && y >= pos1['top'] && y <= pos1['bottom'])
		return;
		/*menu region*/
		var pos = jsUtils.GetRealPos(WaterMark.oBody);
		if(x >= pos['left'] && x <= pos['right'] && y >= pos['top'] && y <= pos['bottom'])
		return;
		WaterMark.Close();
	},

	ChangeData : function(value)
	{
		var val = document.getElementById('watermark_' + this.active).value;
		document.getElementById(this.active + '_' + val).style.borderWidth = '1px';
		document.getElementById(this.active + '_' + val).className = document.getElementById(this.active + '_' + val).className.replace(' active', '');

		if (this.active == 'color')
		document.getElementById('watermark_' + this.active + '_switcher').style.backgroundColor='#' + value;
		else
		document.getElementById('watermark_' + this.active + '_switcher').className = value;

		if (window.PhotoUserID > 0)
		{
			var TID = CPHttpRequest.InitThread();
			CPHttpRequest.Send(TID, '/bitrix/components/bitrix/photogallery.upload/user_settings.php', {"save":this.active, "position":value, "sessid":document.getElementById('sessid').value});
		}

		document.getElementById(this.active + '_' + value).style.borderWidth = '2px';
		document.getElementById(this.active + '_' + value).className += ' active';
		
		document.getElementById('watermark_' + this.active).value = value;

	},

	ChangeText : function(obj)
	{
		if (typeof obj != "object")
		return;
		if (window.PhotoUserID > 0)
		{
			var TID = CPHttpRequest.InitThread();
			var text = (obj.id == 'photo_resize_size' ? 'resize' : 'text');
			CPHttpRequest.Send(TID, '/bitrix/components/bitrix/photogallery.upload/user_settings.php', {"save":text, "position":obj.value, "sessid":document.getElementById('sessid').value});
		}
	} 
}
PUtilsIsLoaded = true;
