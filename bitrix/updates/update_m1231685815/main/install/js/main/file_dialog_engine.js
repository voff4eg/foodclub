if (typeof window._arTreeItems != 'object') window._arTreeItems = {};
if (typeof window._arFiles != 'object') window._arFiles = {};
if (typeof window._arRootFileList != 'object') window._arRootFileList = [];
if (typeof window._arFileList != 'object') window._arFileList = {};
oBXFDContextMenu = false;

function BXDialogTree(){};
BXDialogTree = window.BXDialogTree;

BXDialogTree.prototype.Init = function()
{
	this.arTreeItems = this.CreateFolderArray();
	this.arIconList = {
		folder : '/bitrix/images/main/file_dialog/icons/folder.gif',
		folderopen : '/bitrix/images/main/file_dialog/icons/folderopen.gif',
		plus : '/bitrix/images/main/file_dialog/icons/plus.gif',
		minus : '/bitrix/images/main/file_dialog/icons/minus.gif',
		dot : '/bitrix/images/main/file_dialog/icons/dot.gif'
	};
	this.arDirConts = {};
}

BXDialogTree.prototype.CreateFolderArray = function()
{
	return arTreeItems;
}

BXDialogTree.prototype.BuildTree = function(arTreeItems)
{
	this.DisplayTree(arTreeItems);
}

BXDialogTree.prototype.DisplayTree = function(arTreeItems)
{
	var oCont = document.getElementById("__bx_treeContainer");
	oCont.style.height = "100%";
	oCont.style.width = "100%";
	oCont.style.overflow = "auto";
	oCont.innerHTML = '';
	if (!window.bxfdTreeTable)
	{
		window.bxfdTreeTable = document.createElement("TABLE");
		var oRow,oCell, len,i;
		len = arTreeItems.length;
		for (i = 0; i < len; i++)
		{
			oRow = window.bxfdTreeTable.insertRow(-1);
			oCell = oRow.insertCell(-1);
			this.DisplayElement(arTreeItems[i], oCell);
		}
	}
	oCont.appendChild(window.bxfdTreeTable);
}

BXDialogTree.prototype.oPlusOnClick = function(el)
{
	var paramsCont = el.parentNode.parentNode;
	var path = paramsCont.getAttribute('__bxpath');
	var oCont = this.arDirConts[path];
	var bOpen = paramsCont.getAttribute('__bx_bOpen');
	bOpen = (bOpen == 'true' || bOpen === true);
	var arTables = oCont.getElementsByTagName("TABLE");
	var arImages = arTables[0].getElementsByTagName("IMG");
	var oPlus = arImages[0];
	var oIcon = arImages[1];

	if (!bOpen)
	{
		if (!window._arTreeItems[path])
			this.loadTree(path,false,oCont);
		else
			this.DisplaySubTree(oCont,window._arTreeItems[path]);
		paramsCont.setAttribute('__bx_bOpen',true);
		oPlus.src = this.arIconList.minus;
		oIcon.src = this.arIconList.folderopen;
	}
	else
	{
		oIcon.src = this.arIconList.folder;
		oPlus.src = this.arIconList.plus;
		var subTreeTable = arTables[1];
		subTreeTable.style.display = "none";
		paramsCont.setAttribute('__bx_bOpen',false);
	}
	//}catch(e){}
}


BXDialogTree.prototype.oElementOnClick = function(el)
{
	var paramsCont = el.parentNode.parentNode;
	var path = paramsCont.getAttribute('__bxpath');
	var oCont = this.arDirConts[path];
	var arTables = oCont.getElementsByTagName("TABLE");
	var arSpans = arTables[0].getElementsByTagName("SPAN");
	var arImages = arTables[0].getElementsByTagName("IMG");
	var oTitle = arSpans[0];
	var oPlus = arImages[0];
	var oIcon = arImages[1];

	//var oldSelectedTitle = document.getElementById('__bx_SelectedTitle');
	var oldSelectedTitle = window.fd_tree_selected_element;
	if (oldSelectedTitle)
		this._UnHighlightElement(oldSelectedTitle);

	this._HighlightElement(oTitle);
	oBXDialogControls.dirPath.Set(path);
	oBXDialogWindow.loadFolderContent(path);

	if (oPlus.src.substr(oPlus.src.length-8).toLowerCase() != 'plus.gif')
		return;

	if (!window._arTreeItems[path])
		this.loadTree(path,false,oCont);
	else
		this.DisplaySubTree(oCont,window._arTreeItems[path]);
	paramsCont.setAttribute('__bx_bOpen',true);
	oPlus.src = this.arIconList.minus;
	oIcon.src = this.arIconList.folderopen;
}

BXDialogTree.prototype.DisplayElement = function(oItem, oCont)
{
	this.arDirConts[oItem.path] = oCont;
	var innerHTML = '<table cellPadding="0" cellSpacing="0">' +
	'<tr __bxpath="' + oItem.path + '" __bx_bOpen=false>' +
	'<td class="tree-node-folding" >';
	if (oItem.subfolders)
		innerHTML += '<img onclick="oBXDialogTree.oPlusOnClick(this);" src="' + this.arIconList.plus + '" />';
	else
		innerHTML += '<img onclick="oBXDialogTree.oElementOnClick(this);" src="' + this.arIconList.dot + '" />';
	innerHTML += '</td><td class="tree-node-icon" >' +
	'<img onclick="oBXDialogTree.oElementOnClick(this);" src="' + this.arIconList.folder + '" />' +
	'</td><td class="tree-node-name" >' +
	'<span onclick="oBXDialogTree.oElementOnClick(this);" class="treeStandartElement" unselectable="on">' + oItem.name + '</span>' +
	'</td></tr></table>';
	oCont.innerHTML = innerHTML;
}

BXDialogTree.prototype.loadTree = function(path, hardRefresh, oCont)
{
	oWaitWindow.Show();
	var _this = this;
	var q = new JCHttpRequest();
	q.Action = function(result)
	{
		try{
			setTimeout(function ()
				{
					oWaitWindow.Hide();
					_this.DisplaySubTree(oCont, window._arTreeItems[path]);
				}, 5
			);
		}catch(e){alert('error: loadTree');}
	}

	var _menu = (oBXFileDialog.oConfig.operation == 'S') ? 'Y' : 'N';
	q.Send('/bitrix/admin/file_dialog_load.php?lang='+BXLang+'&site='+getSite()+'&path='+jsUtils.urlencode(path)+'&mode=D&subfolders=Y&get_menu='+_menu);
}


BXDialogTree.prototype._HighlightElement = function(El)
{
	El.id = '__bx_SelectedTitle';
	El.className = 'treeSelectedElement';
	window.fd_tree_selected_element = El;
}

BXDialogTree.prototype.focusOnSelectedElment = function()
{
	return;
	setTimeout(
	function()
	{
		var El = window.fd_tree_selected_element;
		//try{
			var tmpInp = El.parentNode.insertBefore(document.createElement("INPUT"), El);
			tmpInp.style.visibility = 'hidden';
			tmpInp.focus();
			tmpInp.parentNode.removeChild(tmpInp);
		//}catch(e){};
	}, 30);
}

BXDialogTree.prototype._UnHighlightElement = function(El)
{
	El.id = '';
	El.className = 'treeStandartElement';
}

BXDialogTree.prototype.openSection = function(path)
{
	var oCont = this.arDirConts[path];
	var arTables = oCont.getElementsByTagName("TABLE");
	var paramsCont = arTables[0].rows[0];
	var bOpen = paramsCont.getAttribute('__bx_bOpen');
	bOpen = (bOpen == 'true' || bOpen === true);

	if (bOpen)
		return;

	var arImages = arTables[0].getElementsByTagName("IMG");
	var oPlus = arImages[0];
	var oIcon = arImages[1];

	if (!window._arTreeItems[path])
		this.loadTree(path, false, oCont);
	else
		this.DisplaySubTree(oCont,window._arTreeItems[path]);

	paramsCont.setAttribute('__bx_bOpen',true);
	oPlus.src = this.arIconList.minus;
	oIcon.src = this.arIconList.folderopen;
}


BXDialogTree.prototype.__HighlightElement = function(path)
{
	var oCont = this.arDirConts[path];
	if (!oCont)
	{
		setTimeout(function ()
			{
				oBXDialogTree.HighlightPath(path);
			}, 100
		);
		return;
	}
	var arTables = oCont.getElementsByTagName("TABLE");
	var arSpans = arTables[0].getElementsByTagName("SPAN");
	var oTitle = arSpans[0];

	var oldSelectedTitle = window.fd_tree_selected_element;
	if (oldSelectedTitle)
		this._UnHighlightElement(oldSelectedTitle);

	this._HighlightElement(oTitle);
}


BXDialogTree.prototype.HighlightPath = function(path)
{
	try
	{
		if (path == "")
		{
			var oldSelectedTitle = window.fd_tree_selected_element;
			if (oldSelectedTitle)
				this._UnHighlightElement(oldSelectedTitle);
			return;
		}

		path = path.replace(/\\/ig,"/");
		var arPath = path.split("/");
		var basePath = "";

		var _len = arPath.length;
		for (var i = 1; i < _len-1; i++)
		{
			basePath = basePath + "/" + arPath[i];
			this.openSection(basePath);
		}
		this.__HighlightElement(basePath + "/" + arPath[_len-1]);
	}
	catch(e) {setTimeout(function () {oBXDialogTree.HighlightPath(path);}, 100);}
	oBXDialogTree.focusOnSelectedElment();
}


BXDialogTree.prototype.DisplaySubTree = function(oCont, arSubTreeItems)
{
	if (arSubTreeItems === false)
		return;
	try
	{
		var subTreeTable = oCont.getElementsByTagName("TABLE")[1];
		subTreeTable.style.display = "block";
	}
	catch(e)
	{
		var contTable = document.createElement("TABLE");
		oCont.appendChild(contTable);
		contTable.style.marginLeft = "15px";

		var oRow,oCell, len,i;
		len = arSubTreeItems.length;
		for (i = 0;i < len; i++)
		{
			oRow = contTable.insertRow(-1);
			oCell = oRow.insertCell(-1);
			this.DisplayElement(arSubTreeItems[i], oCell);
		}
	}
}

BXDialogTree.prototype.Append = function()
{
	this.Init();
	this.arTree = this.BuildTree(this.arTreeItems);
}



// *****************************************************************************
//                               BXDialogWindow
// *****************************************************************************

function BXDialogWindow()
{
	this.Init();
}

BXDialogWindow.prototype.Init = function(path)
{
	this.pWnd = document.getElementById('__bx_windowContainer');
	this.pWnd.style.overflow = 'auto';
	this.pWnd.style.height = '100%';
	this.pWnd.style.width = (parseInt(this.pWnd.parentNode.offsetWidth) || 540) + 'px';
	//this.pWnd.style.width = '100%';
	this.view = oBXFileDialog.UserConfig.view;
	this.last_correct_path = "";
	this.sort = oBXFileDialog.UserConfig.sort;
	this.sort_order = oBXFileDialog.UserConfig.sort_order;
	this.filter = oBXDialogControls.Filter.curentFilter;
	oBXDialogControls.ViewSelector.Set(this.view, false);
	oBXDialogControls.SortSelector.Set(this.sort,this.sort_order);

	var __title = document.getElementById('BX_file_dialog_title');
	this.cancelRename_innerHTML = '';

	if (oBXFileDialog.oConfig.operation == 'S')
		__title.innerHTML = FD_MESS.FD_SAVE_TAB_TITLE;
	else if (oBXFileDialog.oConfig.operation == 'O' && oBXFileDialog.oConfig.select == 'D')
		__title.innerHTML = FD_MESS.FD_OPEN_DIR;
	else
		__title.innerHTML = FD_MESS.FD_OPEN_TAB_TITLE;

	document.getElementById('BX_file_dialog_close').title = FD_MESS.FD_CLOSE;

	this.iconsPath = '/bitrix/images/main/file_dialog/icons/types/';
	this.arIcons =
	{
		css : {small:'css.gif', big:'css_big.gif', type:'CSS File'},
		csv : {small:'csv.gif', big:'csv_big.gif', type:'CSV File'},
		file : {small:'file.gif', big:'file_big.gif', type:'File'},
		flash : {small:'flash.gif', big:'flash_big.gif', type:'Adobe Macromedia Flash File'},
		folder : {small:'folder.gif', big:'folder_big.gif', type:'Folder'},
		gif : {small:'gif.gif', big:'gif_big.gif', type:'Image GIF'},
		htaccess : {small:'htaccess.gif', big:'htaccess_big.gif', type:'htaccess file'},
		html : {small:'html.gif', big:'html_big.gif', type:'HTML File'},
		jpg : {small:'jpeg.gif', big:'jpeg_big.gif', type:'Image JPG'},
		jpeg : {small:'jpeg.gif', big:'jpeg_big.gif', type:'Image JPEG'},
		js : {small:'js.gif', big:'js_big.gif', type:'Javascript File'},
		php : {small:'php.gif', big:'php_big.gif', type:'PHP File'},
		png : {small:'png.gif', big:'png_big.gif', type:'Image PNG'},
		txt : {small:'txt.gif', big:'txt_big.gif', type:'Text File'},
		xml : {small:'xml.gif', big:'xml_big.gif', type:'XML File'}
	};

	if (!path)
		path = oBXFileDialog.UserConfig.path;

	this.loadFolderContent(path);
	oBXDialogControls.dirPath.Set(path);
	setTimeout(function ()
		{
			oBXDialogTree.HighlightPath(path);
		}, 10
	);

	// *  *  *  *  *  *  CONTEXT MENU INIT *  *  *  *  *  *  *  *  *  *
	oBXFDContextMenu = new BXFDContextMenu();
}


BXDialogWindow.prototype.loadFolderContent = function(path, hard_refresh, refreshTree)
{
	var last_correct_path = this.last_correct_path;
	oWaitWindow.Show();
	if (!hard_refresh)
		hard_refresh = false;

	if (oBXFileDialog.oConfig.operation == 'O')
		oBXDialogControls.filePath.Set(oBXFileDialog.oConfig.select == 'F' ? "" : path);
	else if (oBXDialogControls.filePath.Get() == "")
		oBXDialogControls.filePath.Set(oBXDialogControls.filePath.defaultName);

	oBXDialogControls.Preview.Clear();
	var onResult = function()
	{
		setTimeout(function ()
		{
			oWaitWindow.Hide();

			var oEl = window._arPermissions[(path == '') ? '/' : path];
			if (oBXFileDialog.oConfig.operation == 'O' && oBXFileDialog.oConfig.showUploadTab && oEl)
				oBXDialogTabs.DisableTab("tab2", !oEl.upload);

			if (window._arTreeItems[path] === false && window._arFiles[path] === false)
			{
				oBXDialogControls.History.RemoveLast();
				var _p = oBXDialogWindow.last_correct_path;
				if (!window.window._arFiles[_p] || !window._arTreeItems[_p])
					oBXDialogWindow.loadFolderContent(_p);
				oBXDialogControls.dirPath.Set(_p);
			}
			else
			{
				oBXDialogWindow.DisplayElementsList(window._arTreeItems[path], window._arFiles[path], oBXDialogWindow.view, oBXDialogWindow.filter, oBXDialogWindow.sort, oBXDialogWindow.sort_order);
				oBXDialogWindow.last_correct_path = path;
			}

			//refresh menu types
			if (oBXFileDialog.oConfig.operation == 'S' && oBXFileDialog.oConfig.showAddToMenuTab)
				oBXMenuHandling.ChangeMenuType();

			if (refreshTree)
				oBXDialogTree.DisplayTree (window._arTreeItems['']);
		}, 5
		);
	}

	if (window._arTreeItems[path] && window._arFiles[path] && hard_refresh !== true)
		return onResult();

	var q = new JCHttpRequest();
	q.Action = function(result){try{onResult();}catch(e){alert('error: loadTree');}};
	var _mode = (oBXFileDialog.oConfig.select == 'F') ? 'DF' : oBXFileDialog.oConfig.select;
	var _menu = (oBXFileDialog.oConfig.operation == 'S') ? 'Y' : 'N';
	q.Send('/bitrix/admin/file_dialog_load.php?lang='+BXLang+'&site='+getSite()+'&path='+jsUtils.urlencode(path)+'&mode='+_mode+'&subfolders=Y&get_menu='+_menu);
}


BXDialogWindow.prototype.DisplayElementsList = function(arDirs, arFiles, view, filter, sort, sort_order)
{
	_this = this;
	//Folder doesn't exists
	if (arDirs === false && arFiles === false)
		return;

	oBXDialogWindow.view = view;

	// ##################  MOUSE EVENTS FOR FILE & FOLDERS  ###################
	BXDialogWindow.prototype.DirOnClick = function(sPath)
	{
		if (oBXFileDialog.oConfig.select == 'D' || oBXFileDialog.oConfig.select == 'DF')
		{
			oBXDialogControls.Preview.Clear();
			oBXDialogControls.filePath.Set(sPath);
		}
	}

	BXDialogWindow.prototype.FileOnClick = function(sPath)
	{
		if (oBXFileDialog.oConfig.select == 'D')
			return;

		oBXDialogControls.filePath.Set(getFileName(sPath));
		oBXDialogControls.Preview.Display(sPath);
	}

	BXDialogWindow.prototype.DirOnDblClick = function(sPath)
	{
	}
	// # # #  # # #  # # #  # # #  # # #  # # #  # # #  # # #  # # #  # # #  # # # #

	var len1 = arDirs.length;
	var len2 = arFiles.length;
	var arElements = [];
	var oDir, oFile, lenS, ext, icon;
	//Push directories to Elements array
	for (var i = 0; i<len1; i++)
	{
		oDir = arDirs[i];
		arElements.push(
			{
				name : oDir.name,
				icon : 'folder',
				path : oDir.path,
				permission : oDir.permission,
				date : oDir.date,
				timestamp : oDir.timestamp,
				size : oDir.size
			}
		);
	}

	//Push files to Elements array
	var arFilter = (filter === '' || filter === false) ? '*' : oBXDialogControls.Filter.arFilters[filter];
	var l,j,add;
	for (var i = 0; i<len2; i++)
	{
		add = false;
		oFile = arFiles[i];
		ext = oFile.name.substr(oFile.name.lastIndexOf(".")+1).toLowerCase();
		icon = (!this.arIcons[ext]) ? 'file' : ext;
		if (arFilter != '*')
		{
			l = arFilter.length;
			for (j = 0; j < l; j++)
			{
				if (ext == arFilter[j])
				{
					add = true;
					break;
				}
			}
		}
		else
			add = true;

		if (add)
		{
			arElements.push(
				{
					name : oFile.name,
					icon : icon,
					ext : ext,
					path : oFile.abs_path,
					permission : oFile.permission,
					date : oFile.date,
					timestamp : oFile.timestamp,
					size : oFile.size
				}
			);
		}
	}

	oWaitWindow.Show();
	setTimeout(function ()
	{
		__BXSort(arElements,oBXDialogWindow.sort,oBXDialogWindow.sort_order);

		//Select view mode
		switch(view)
		{
			case 'list':
			_this.__DisplayElList_list(arElements);
			break;
			case 'detail':
			_this.__DisplayElList_detail(arElements);
			break;
			case 'preview':
			_this.__DisplayElList_preview(arElements);
			break;
		}
		oBXDialogControls.NewDirButtonChange();
		oWaitWindow.Hide();
	}, 3);
}


BXDialogWindow.prototype.__DisplayElList_list = function(arElements)
{
	var addSubCont = function(oTable,oRow)
	{
		var w = 220;
		oTable.style.width = (parseInt(oTable.style.width)+w)+"px";
		var oSC = oRow.insertCell(-1);
		oSC.className = 'bx-valign-top';
		oSC.style.width = w+'px';
		return oSC;
	};

	var addElement_list = function(oTable, oEl)
	{
		if (!oEl)
			return;

		var OnClick_dir = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.DirOnClick(path);
			_this.HigthLightElement(path,paramsCont);
		}

		var OnClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.FileOnClick(path);
			_this.HigthLightElement(path,paramsCont);
		}

		var OnDblClick = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.loadFolderContent(path);
			oBXDialogTree.HighlightPath(path);
			oBXDialogControls.dirPath.Set(path);
		}

		var OnDblClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.FileOnClick(path);
			oBXFileDialog.SubmitFileDialog();
		}

		oR = oTable.insertRow(-1);
		if (oEl.icon == 'folder')
		{
			oR.onclick = OnClick_dir;
			oR.ondblclick = OnDblClick;
		}
		else
		{
			oR.onclick = OnClick_file;
			oR.ondblclick = OnDblClick_file;
		}
		oR.oncontextmenu = oBXDialogWindow.OnContextMenu;

		window._arFileList[oEl.path] = oEl;
		oR.setAttribute('__bxpath', oEl.path);
		var oIconCell = oR.insertCell(-1);
		var src = _this.iconsPath + _this.arIcons[oEl.icon].small;
		var _size = (oEl.icon != 'folder') ? getFileSize(oEl.size) : '';
		var _title = (jsUtils.IsIE()) ? (oEl.name + (oEl.icon != 'folder' ? "\n"+FD_MESS.FD_SORT_SIZE+": " + _size : "") + "\n"+FD_MESS.FD_SORT_DATE+": "+oEl.date) : (oEl.name);
		oIconCell.innerHTML = '<img src="'+src+'" title="'+_title+'" />';
		oIconCell.style.width = '0%';

		oTitleCell = oR.insertCell(-1);
		oTitleCell.unselectable = "on";
		oTitleCell.style.cursor = "default";
		oTitleCell.style.width = '100%';
		oTitleCell.style.textAlign = 'left';
		oTitleCell.title = _title;
		oTitleCell.className = "windowStandartElement";
		oTitleCell.innerHTML = "<span class='title'>"+oBXDialogWindow.checkNameLength(oEl.name,210)+"</span>";
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	if (oSubContTable)
		oSubContTable.parentNode.removeChild(oSubContTable);

	oSubContTable = document.createElement('TABLE');
	oSubContTable.id = "__bx_oSubContTable";
	this.pWnd.appendChild(oSubContTable);
	oSubContTable.style.height = '228px';
	oSubContTable.style.width = '0px';
	var oRow = oSubContTable.insertRow(-1);


	lenS = arElements.length;
	for (var i = 0; i<lenS; i++)
	{
		if (i%12 == 0)
		{
			oSubCont = addSubCont(oSubContTable,oRow);
			var oSSContTable = oSubCont.appendChild(document.createElement('TABLE'));
			oSSContTable.style.width = "100%";
		}
		addElement_list(oSSContTable,arElements[i]);
	}

	this.Last_ElList_len = lenS;
}


BXDialogWindow.prototype.__DisplayElList_detail = function(arElements)
{
	var addElement_detail = function(oTable,oEl)
	{
		if (!oEl)
			return;

		var OnClick_dir = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.DirOnClick(path);
			_this.HigthLightElement(path,paramsCont);
		}

		var OnClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			var arPath = path.split("/");
			var _len = arPath.length;

			var file_name = arPath[arPath.length-1];
			oBXDialogControls.filePath.Set(getFileName(path));
			_this.HigthLightElement(path,paramsCont);
			oBXDialogControls.Preview.Display(path);
		}

		var OnDblClick = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			oBXDialogWindow.loadFolderContent(path);
			oBXDialogTree.HighlightPath(path);
			oBXDialogControls.dirPath.Set(path);
		}

		var OnDblClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			var arPath = path.split("/");
			var _len = arPath.length;

			var file_name = arPath[arPath.length-1];
			oBXDialogControls.filePath.Set(getFileName(path));
			oBXFileDialog.SubmitFileDialog();
		}


		oR = oTable.insertRow(-1);
		if (oEl.icon == 'folder')
		{
			oR.onclick = OnClick_dir;
			oR.ondblclick = OnDblClick;
		}
		else
		{
			oR.onclick = OnClick_file;
			oR.ondblclick = OnDblClick_file;
		}
		oR.oncontextmenu = oBXDialogWindow.OnContextMenu;

		window._arFileList[oEl.path] = oEl;
		oR.setAttribute('__bxpath',oEl.path);
		oIconCell = oR.insertCell(-1);
		var src = _this.iconsPath + _this.arIcons[oEl.icon].small;

		var _size = (oEl.icon != 'folder') ? getFileSize(oEl.size) : '';
		var _title = (jsUtils.IsIE()) ? (oEl.name + (oEl.icon != 'folder' ? "\n"+FD_MESS.FD_SORT_SIZE+": " + _size : "") + "\n"+FD_MESS.FD_SORT_DATE+": "+oEl.date) : (oEl.name);
		var _type = oBXDialogWindow.arIcons[oEl.icon].type;
		_date = oEl.date;

		oIconCell.innerHTML = '<img src="'+src+'" title="'+_title+'" />';
		oIconCell.style.width = '10px';
		oNameCell = oR.insertCell(-1);
		oNameCell.unselectable = "on";
		oNameCell.style.cursor = "default";
		oNameCell.style.textAlign = 'left';
		oNameCell.title = _title;
		oNameCell.innerHTML = "<span class='title'>"+oBXDialogWindow.checkNameLength(oEl.name,210)+"</span>";
		oNameCell.className = "windowStandartElement";
		oSizeCell = oR.insertCell(-1);
		oSizeCell.style.textAlign = "right";
		oSizeCell.style.paddingRight = "5px";
		oSizeCell.innerHTML = _size;
		oTypeCell = oR.insertCell(-1);
		oTypeCell.innerHTML = _type;
		oDateCell = oR.insertCell(-1);
		oDateCell.innerHTML = _date;
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	if (oSubContTable)
		oSubContTable.parentNode.removeChild(oSubContTable);

	oSubContTable = document.createElement('TABLE');
	oSubContTable.id = "__bx_oSubContTable";
	this.pWnd.appendChild(oSubContTable);
	oSubContTable.style.height = '0%';
	oSubContTable.style.width = '100%';
	var oRow = oSubContTable.insertRow(-1);

	var fill_innerHTML = function()
	{
		nameCell.innerHTML = FD_MESS.FD_SORT_NAME;
		sizeCell.innerHTML = FD_MESS.FD_SORT_SIZE;
		typeCell.innerHTML = FD_MESS.FD_SORT_TYPE;
		dateCell.innerHTML = FD_MESS.FD_SORT_DATE;
	}

	// Detail table header
	var iconCell = oRow.insertCell(-1);
	iconCell.className = 'fd_det_view_header';
	iconCell.style.width = "15px";
	var nameCell = oRow.insertCell(-1);
	nameCell.className = 'fd_det_view_header';
	nameCell.style.width = "45%";
	var sizeCell = oRow.insertCell(-1);
	sizeCell.className = 'fd_det_view_header';
	var typeCell = oRow.insertCell(-1);
	typeCell.className = 'fd_det_view_header';
	var dateCell = oRow.insertCell(-1);
	dateCell.className = 'fd_det_view_header';
	fill_innerHTML();

	nameCell.style.padding = sizeCell.style.padding = typeCell.style.padding = dateCell.style.padding = "1px 2px 1px 5px";

	var arr_img = "<img src='/bitrix/images/main/file_dialog/arrow_"+(oBXDialogWindow.sort_order == 'asc' ? 'up' : 'down')+".gif'>"

	switch(oBXDialogWindow.sort)
	{
		case 'name':
			nameCell.innerHTML += '&nbsp;'+arr_img;
			nameCell.setAttribute("sort_order",oBXDialogWindow.sort_order);
			break;
		case 'size':
			sizeCell.innerHTML += '&nbsp;'+arr_img;
			sizeCell.setAttribute("sort_order",oBXDialogWindow.sort_order);
			break;
		case 'type':
			typeCell.innerHTML += '&nbsp;'+arr_img;
			typeCell.setAttribute("sort_order",oBXDialogWindow.sort_order);
			break;
		case 'date':
			dateCell.innerHTML += '&nbsp;'+arr_img;
			dateCell.setAttribute("sort_order",oBXDialogWindow.sort_order);
			break;
	}

	var __onclick = function(__name,oCell)
	{
		fill_innerHTML();
		if (oBXDialogWindow.sort != __name)
		{
			oBXDialogWindow.sort = __name;
			var new_sort_order = 'asc';
		}
		else
			new_sort_order = (oCell.getAttribute("sort_order") == 'asc') ? 'des' : 'asc';

		oCell.setAttribute("sort_order",new_sort_order);
		oBXDialogWindow.sort_order = new_sort_order;

		var arr_img = "<img src='/bitrix/images/main/file_dialog/arrow_"+(oBXDialogWindow.sort_order == 'asc' ? 'up' : 'down')+".gif'>"
		oCell.innerHTML += '&nbsp;'+arr_img;

		oWaitWindow.Show();

		setTimeout(function ()
				{
					__BXSort(arElements,__name,new_sort_order);
					oBXDialogControls.SortSelector.Set(__name,new_sort_order);
					oBXDialogWindow.__DisplayElList_detail(arElements);
					oWaitWindow.Hide();
				}, 5
			);
	}

	nameCell.onclick = function(){__onclick("name",nameCell);}
	sizeCell.onclick = function(){__onclick("size",sizeCell);}
	typeCell.onclick = function(){__onclick("type",typeCell);}
	dateCell.onclick = function(){__onclick("date",dateCell);}

	lenS = arElements.length;
	var oSSRow,oSSCell;

	for (var i = 0; i<lenS; i++)
		addElement_detail(oSubContTable,arElements[i]);
}


BXDialogWindow.prototype.__DisplayElList_preview = function(arElements)
{
	var addElement_preview = function(oCont,oEl)
	{
		if (!oEl)
			return;

		var OnClick_dir = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');
			oBXDialogWindow.DirOnClick(path);
			oBXDialogWindow.HigthLightElement_preview(this);
		}

		var OnClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			var arPath = path.split("/");
			var _len = arPath.length;

			oBXDialogWindow.HigthLightElement_preview(this);
			var file_name = arPath[arPath.length-1];
			oBXDialogControls.filePath.Set(getFileName(path));
			oBXDialogControls.Preview.Display(path);
		}

		var OnDblClick = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			oBXDialogWindow.loadFolderContent(path);
			oBXDialogTree.HighlightPath(path);
			oBXDialogControls.dirPath.Set(path);
		}

		var OnDblClick_file = function(e)
		{
			var paramsCont = this;
			var path = paramsCont.getAttribute('__bxpath');

			var arPath = path.split("/");
			var _len = arPath.length;

			oBXDialogWindow.HigthLightElement_preview(this);
			var file_name = arPath[arPath.length-1];
			oBXDialogControls.filePath.Set(getFileName(path));
			oBXFileDialog.SubmitFileDialog();
		}

		var elDiv = oCont.appendChild(CreateElement('DIV', {style:{width:"160px", height:"150px"}}));
		elDiv.className = 'preview_cont';
		elTable = elDiv.appendChild(CreateElement('TABLE', {style:{width:"100%", height:"100%"}}));

		var oPreviewCell = elTable.insertRow(-1).insertCell(-1);
		oPreviewCell.align = "center";
		oPreviewCell.unselectable = "on";
		oPreviewCell.valign = "middle";
		var oDetailsCell = elTable.insertRow(-1).insertCell(-1);
		oDetailsCell.align = "center";
		oDetailsCell.unselectable = "on";
		oDetailsCell.style.cursor = "default";
		oPreviewCell.style.height = "110px";

		window._arFileList[oEl.path] = oEl;
		elDiv.setAttribute('__bxpath',oEl.path);

		if (oEl.icon == 'folder')
		{
			elDiv.onclick = OnClick_dir;
			elDiv.ondblclick = OnDblClick;
		}
		else
		{
			elDiv.onclick = OnClick_file;
			elDiv.ondblclick = OnDblClick_file;
		}
		elDiv.oncontextmenu = oBXDialogWindow.OnContextMenu;

		// ***** Preview IMAGE  ******
		var fileName = getFileName(oEl.path);
		var oImg = document.createElement('IMG');
		if (_IsImage(fileName))
		{
			var _oSize = oBXDialogControls.Preview._GetRealSize(oEl.path);
			var w = _oSize.w;
			var h = _oSize.h;
			var a = 110;

			oImg.src = oEl.path;

			var newW = w + "px";
			var newH = h + "px";

			if (w > h && w > a)
			{
				newW = a+"px";
				newH = Math.round(h * a / w) + "px";
			}
			else if (h > a)
			{
				newH = a+"px";
				newW = Math.round(w * a / h) + "px";
			}

			oImg.style.width = newW;
			oImg.style.height = newH;
		}
		else
		{
			oImg.src = oBXDialogWindow.iconsPath + oBXDialogWindow.arIcons[oEl.icon].big;
		}
		// **************************

		var _size = (oEl.icon != 'folder') ? getFileSize(oEl.size) : '';
		var _date = oEl.date;
		var _type = oBXDialogWindow.arIcons[oEl.icon].type;
		var _title = (jsUtils.IsIE()) ? (oEl.name + (oEl.icon != 'folder' ? "\n"+FD_MESS.FD_SORT_SIZE+": " + _size : "") + "\n"+FD_MESS.FD_SORT_DATE+": "+oEl.date) : (oEl.name);
		oImg.title = _title;

		oPreviewCell.appendChild(oImg);
		oDetailsCell.innerHTML = oBXDialogWindow.checkNameLength(oEl.name,170)+(_size!="" ? "<br />"+_size : '');
		elDiv.title = _title;
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	if (oSubContTable)
		oSubContTable.parentNode.removeChild(oSubContTable);


	oSubContTable = document.createElement('TABLE');
	oSubContTable.id = "__bx_oSubContTable";
	this.pWnd.appendChild(oSubContTable);
	oSubContTable.style.height = '0%';
	oSubContTable.style.width = '100%';
	var oCont = oSubContTable.insertRow(-1).insertCell(-1);

	lenS = arElements.length;
	var oSSRow,oSSCell;

	for (var i = 0; i<lenS; i++)
		addElement_preview(oCont,arElements[i]);
}


BXDialogWindow.prototype.checkNameLength = function(name,width,bAddEllipsis)
{
	if (name.length <= 12)
		return name;

	if (!bAddEllipsis)
		bAddEllipsis = false;

	oDiv = document.createElement('DIV');
	oDiv.style.position = "absolute";
	oDiv.innerHTML = name;
	document.body.appendChild(oDiv);
	w = oDiv.offsetWidth;
	document.body.removeChild(oDiv);

	if (w < width && !bAddEllipsis)
		return name;

	var len = name.length;
	name_base = name.substr(0,name.length - 7);
	name_end = name.substr(name.length - 7);

	if (w >= width)
		name = this.checkNameLength(name_base.substr(0, name_base.length - 3) + name_end, width, true);
	else if (bAddEllipsis)
		name = name_base + "..." + name_end;

	return name;
}


BXDialogWindow.prototype.HigthLightElement = function(path,oCont_tr)
{
	var by_path = (!oCont_tr) ? true : false;

	if (!by_path)
	{
		var iconCell = oCont_tr.cells[0];
		var titleCell = oCont_tr.cells[1];

		var oldSel_icon = document.getElementById('__bx_Window_SelectedIcon');
		var oldSel_title = document.getElementById('__bx_Window_SelectedTitle');
		this._UnHighlightElement(oldSel_icon,oldSel_title);
		this._HighlightElement(iconCell,titleCell);
	}
}

BXDialogWindow.prototype.HigthLightElement_preview = function(oCont)
{
	var he = document.getElementById("hightlightedElement_preview");
	if (he)
	{
		he.className = "preview_cont";
		he.id = "";
	}
	oCont.id = "hightlightedElement_preview";
	oCont.className = "preview_cont_sel";
}



BXDialogWindow.prototype._HighlightElement = function(ElIcon,ElTitle)
{
	if (ElIcon)
		ElIcon.id = '__bx_Window_SelectedIcon';

	ElTitle.id = '__bx_Window_SelectedTitle';
	ElTitle.className = 'windowSelectedElement';
}

BXDialogWindow.prototype._UnHighlightElement = function(ElIcon,ElTitle)
{
	try
	{
		if (ElIcon)
			ElIcon.id = '';

		ElTitle.id = '';
		ElTitle.className = 'windowStandartElement';
	}
	catch(e){}
}


BXDialogWindow.prototype.AddNewElement = function()
{
	switch(oBXDialogWindow.view)
	{
		case 'list':
			this.AddNewElement_list();
			break;
		case 'detail':
			this.AddNewElement_detail();
			break;
		case 'preview':
			this.AddNewElement_preview();
			break;
	}
}


BXDialogWindow.prototype.AddNewElement_list = function()
{
	var addSubCont = function(oTable,oRow)
	{
		var w = 220;
		oTable.style.width = (parseInt(oTable.style.width)+w)+"px";
		var oSC = oRow.insertCell(-1);
		oSC.className = 'bx-valign-top';
		oSC.style.width = w+'px';
		return oSC;
	};

	var addElement_list = function(oTable,oEl)
	{
		oR = oTable.insertRow(-1);

		var oIconCell = oR.insertCell(-1);
		var src = _this.iconsPath + _this.arIcons['folder'].small;
		oIconCell.innerHTML = '<img src="'+src+'"/>';
		oIconCell.style.width = '0%';

		oTitleCell = oR.insertCell(-1);
		oTitleCell.unselectable = "on";
		oTitleCell.style.cursor = "default";
		oTitleCell.style.width = '100%';
		oTitleCell.style.textAlign = 'left';

		var oNameInput = oTitleCell.appendChild(CreateElement('INPUT', {type:'text', value:oBXDialogControls.DefaultDirName, id:'__edited_element', __bx_mode:'new', style:{width:'100%'}}));

		oBXDialogWindow.SelectInput(oNameInput);
		jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
		jsUtils.addEvent(oNameInput, "blur", oBXDialogWindow.OnElementBlur);
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	var oRow = oSubContTable.rows[0];
	var oSSContTable = oRow.cells[oRow.cells.length - 1].childNodes[0];

	if (this.Last_ElList_len%12 == 0)
	{
		oSubCont = addSubCont(oSubContTable,oRow);
		var oSSContTable = oSubCont.appendChild(document.createElement('TABLE'));
		oSSContTable.style.width = "100%";
	}
	addElement_list(oSSContTable);
}


BXDialogWindow.prototype.AddNewElement_detail = function()
{
	var addElement_detail = function(oTable)
	{
		oR = oTable.insertRow(-1);
		oIconCell = oR.insertCell(-1);
		var src = _this.iconsPath + _this.arIcons['folder'].small;
		oIconCell.innerHTML = '<img src="'+src+'" />';
		oIconCell.style.width = '10px';
		oNameCell = oR.insertCell(-1);
		oNameCell.className = "windowStandartElement";
		oSizeCell = oR.insertCell(-1);
		oTypeCell = oR.insertCell(-1);
		oDateCell = oR.insertCell(-1);

		var oNameInput = oNameCell.appendChild(CreateElement('INPUT', {type:'text', value:oBXDialogControls.DefaultDirName, id:'__edited_element', __bx_mode:'new', style:{width:'100%'}}));

		oBXDialogWindow.SelectInput(oNameInput);
		jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
		jsUtils.addEvent(oNameInput, "blur", oBXDialogWindow.OnElementBlur);
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	addElement_detail(oSubContTable);
}

BXDialogWindow.prototype.AddNewElement_preview = function()
{
	var addElement_preview = function(oCont)
	{
		elDiv = oCont.appendChild(CreateElement('DIV', {className: "preview_cont", style:{width:"160px", height:"150px"}}));
		elTable = elDiv.appendChild(CreateElement('TABLE', {style:{width:"100%", height:"100%"}}));
		var oPreviewCell = elTable.insertRow(-1).insertCell(-1);
		oPreviewCell.align = "center";
		oPreviewCell.unselectable = "on";
		oPreviewCell.valign = "middle";
		oPreviewCell.style.height = "110px";

		var oDetailsCell = elTable.insertRow(-1).insertCell(-1);
		oPreviewCell.appendChild(CreateElement('IMG',{src:oBXDialogWindow.iconsPath + oBXDialogWindow.arIcons['folder'].big}));


		var oNameInput = oDetailsCell.appendChild(CreateElement('INPUT', {type:'text', value:oBXDialogControls.DefaultDirName, id:'__edited_element', __bx_mode:'new', style:{width:'100%'}}));

		oBXDialogWindow.SelectInput(oNameInput);
		jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
		jsUtils.addEvent(oNameInput, "blur", oBXDialogWindow.OnElementBlur);
	};

	var oSubContTable = document.getElementById("__bx_oSubContTable");
	var oCont = oSubContTable.rows[0].cells[0];

	addElement_preview(oCont);
}


BXDialogWindow.prototype.RenameElement = function(ElementCont)
{
	var path = ElementCont.getAttribute('__bxpath');
	var oEl = window._arFileList[path];
	var ElCont;
	if (ElementCont.nodeName.toUpperCase() == 'TABLE') //List and detail mode
		var ElCont = ElementCont.cells[1];
	else // Preview mode
		var ElCont = ElementCont.getElementsByTagName('TD')[1];

	oBXDialogWindow.cancelRename_innerHTML = ElCont.innerHTML;
	ElCont.innerHTML = '';

	var oNameInput = ElCont.appendChild(CreateElement('INPUT', {type:'text', value:oEl.name, id:'__edited_element', __bx_mode: 'rename', __bx_old_name: oEl.name, style: {width: '100%'}}));

	oBXDialogWindow.SelectInput(oNameInput);
	jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
	jsUtils.addEvent(oNameInput, "blur", oBXDialogWindow.OnElementBlur);
}


BXDialogWindow.prototype.OnElementKeyPress = function(e)
{
	try{
		if (!e)
			e = window.event
		if (!e)
			return;

		var esc = (e.keyCode == 27);
		var enter = (e.keyCode == 13);

		if (esc || enter)
		{
			oElement = document.getElementById('__edited_element');
			jsUtils.removeEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
			jsUtils.removeEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);

			var mode = oElement.getAttribute('__bx_mode');
			if (mode == 'new')
				oBXDialogWindow.NewDir((esc) ? oBXDialogControls.DefaultDirName : oElement.value);
			else if (mode == 'rename')
			{
				var old_name = oElement.getAttribute('__bx_old_name');
				if (esc)
					oBXDialogWindow.CancelRename();
				else
					oBXDialogWindow.Rename(old_name, oElement.value);
			}
		}
	} catch(e){}
}

BXDialogWindow.prototype.OnElementBlur = function(e)
{
	oElement = document.getElementById("__edited_element");
	if (!oElement)
		return;
	jsUtils.removeEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
	jsUtils.removeEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
	var mode = oElement.getAttribute('__bx_mode');

	if (mode == 'new')
		oBXDialogWindow.NewDir(oElement.value);
	else if (mode == 'rename')
	{
		var old_name = oElement.getAttribute('__bx_old_name');
		oBXDialogWindow.Rename(old_name, oElement.value);
	}
}


BXDialogWindow.prototype.NewDir = function(name)
{
	var path = oBXDialogControls.dirPath.Get();
	setTimeout(function ()
		{
			var nd = new JCHttpRequest();
			window.action_warning = '';
			nd.Action = function(result)
			{
				if (!__BXFileDialog)
					return;
				setTimeout(function ()
				{
					oWaitWindow.Hide();
					if (!window.action_status)
					{
						if (window.action_warning.length > 0)
							alert(window.action_warning);

						var oElement = document.getElementById('__edited_element');
						if (oElement)
						{
							oBXDialogWindow.SelectInput(oElement);
							jsUtils.addEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
							jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
						}
					}
					else if (window.action_status === true)
						oBXDialogWindow.loadFolderContent(path, true);
				}, 5);
			}

			var mess = oBXDialogWindow.ClientSideCheck(path, name, false, true);
			if (mess !== true)
			{
				setTimeout(function ()
				{
					if (!__BXFileDialog)
						return;
					alert(mess);
					var oElement = document.getElementById('__edited_element');
					if (oElement)
					{
						oBXDialogWindow.SelectInput(oElement);
						jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
						jsUtils.addEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
					}
				},250);
			}
			else
			{
				oWaitWindow.Show();
				nd.Send('/bitrix/admin/file_dialog_action.php?action=new_dir&site='+getSite()+'&path='+jsUtils.urlencode(path)+'&name='+jsUtils.urlencode(name));
			}
		}, 5
	);
}

// Remove file OR dir
BXDialogWindow.prototype.Remove = function(path)
{
	var rf = new JCHttpRequest();
	window.action_warning = '';
	rf.Action = function(result)
	{
		if (!__BXFileDialog)
			return;

		setTimeout(function ()
		{
			oWaitWindow.Hide();
			if (!window.action_status)
			{
				if (window.action_warning.length > 0)
					alert(window.action_warning);

				var oElement = document.getElementById('__edited_element');
				if (oElement)
				{
					oBXDialogWindow.SelectInput(oElement);
					jsUtils.addEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
					jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
				}
			}
			else if (window.action_status === true)
				oBXDialogWindow.loadFolderContent(oBXDialogControls.dirPath.Get(), true);
		}, 5);
	}

	oWaitWindow.Show();
	rf.Send('/bitrix/admin/file_dialog_action.php?action=remove&site='+getSite()+'&path='+jsUtils.urlencode(path));
}


BXDialogWindow.prototype.Rename = function(old_name, name)
{
	if (old_name == name)
		return oBXDialogWindow.CancelRename();

	var path = oBXDialogControls.dirPath.Get();
	var oEl = window._arFileList[path+'/'+old_name];
	var f = true, d = true;
	if (oEl)
		if (oEl.icon == 'folder')
			f = false;
		else
			d = false;
	var mess = oBXDialogWindow.ClientSideCheck(path, name, f, d);
	if (mess !== true)
	{
		setTimeout(function ()
		{
			if (!__BXFileDialog)
				return;

			alert(mess);
			var oElement = document.getElementById('__edited_element');
			if (oElement)
			{
				oElement.value = old_name;
				oBXDialogWindow.SelectInput(oElement);
				jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
				jsUtils.addEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
			}
		}, 250);
	}
	else
	{
		var rn = new JCHttpRequest();
		window.action_warning = '';

		rn.Action = function(result)
		{
			if (!__BXFileDialog)
				return;
			setTimeout(function ()
			{
				oWaitWindow.Hide();
				if (!window.action_status)
				{
					if (window.action_warning.length > 0)
						alert(window.action_warning);

					var oElement = document.getElementById('__edited_element');
					if (oElement)
					{
						oBXDialogWindow.SelectInput(oElement);
						jsUtils.addEvent(oElement, "blur", oBXDialogWindow.OnElementBlur);
						jsUtils.addEvent(document, "keypress", oBXDialogWindow.OnElementKeyPress);
					}
				}
				else if (window.action_status === true)
					oBXDialogWindow.loadFolderContent(path, true);
			}, 5);
		}

		oWaitWindow.Show();
		rn.Send('/bitrix/admin/file_dialog_action.php?action=rename&site='+getSite()+'&path='+jsUtils.urlencode(path)+'&name='+jsUtils.urlencode(name)+'&old_name='+jsUtils.urlencode(old_name));
	}
}


BXDialogWindow.prototype.CancelRename = function()
{
	var oElement = document.getElementById('__edited_element');
	if (oElement)
		oElement.parentNode.innerHTML = oBXDialogWindow.cancelRename_innerHTML;
}


BXDialogWindow.prototype.ClientSideCheck = function(path, name, checkFiles, checkDirs)
{
	if (name.length <= 0)
		return FD_MESS.FD_EMPTY_NAME;

	var new_name = name.replace(/[^a-zA-Z0-9\s!#\$%&\(\)\[\]\{\}+\-\.;=@\^_\~]/i, '');
	if (name !== new_name)
		return FD_MESS.FD_INCORRECT_NAME;

	if (checkFiles)
		for (var p in window._arFiles[path])
			if (window._arFiles[path][p].name == name)
				return FD_MESS.FD_NEWFILE_EXISTS;

	if (checkDirs)
		for (var p in window._arTreeItems[path])
			if (window._arTreeItems[path][p].name == name)
				return FD_MESS.FD_NEWFOLDER_EXISTS;

	return true;
}


BXDialogWindow.prototype.SelectInput = function(oElement, value)
{
	if (!value)
		var value = oBXDialogControls.DefaultDirName;

	if (!oElement)
		var oElement = document.getElementById('__edited_element');
	if (!oElement)
		return;

	oElement.select();
	oElement.focus();
}


BXDialogWindow.prototype.OnContextMenu = function(e)
{
	var paramsCont = this; // TR or DIV
	var path = paramsCont.getAttribute('__bxpath');
	var oEl = window._arFileList[path];

	if (!e)
		e = window.event
	if (!e)
		return;

	if (paramsCont.nodeName.toUpperCase() == 'DIV') // Preview mode
		oBXDialogWindow.HigthLightElement_preview(paramsCont);
	else // List or detail mode
		oBXDialogWindow.HigthLightElement(path,paramsCont)

	if (oEl.permission.f_delete || oEl.permission.rename)
	{
		if (e.pageX || e.pageY)
		{
			e.realX = e.pageX;
			e.realY = e.pageY;
		}
		else if (e.clientX || e.clientY)
		{
			e.realX = e.clientX + document.body.scrollLeft;
			e.realY = e.clientY + document.body.scrollTop;
		}

		var arItems = [];
		if (oEl.permission.rename)
		{
			arItems.push({
				id : 'rename',
				src : '/bitrix/images/main/file_dialog/rename.gif',
				name : FD_MESS.FD_RENAME,
				handler : function()
				{
					oBXDialogWindow.RenameElement(paramsCont);
				}
			});
		}

		if (oEl.permission.f_delete)
		{
			if (arItems.length > 0)
				arItems.push('separator');

			arItems.push({
				id : 'delete',
				src : '/bitrix/images/main/file_dialog/delete.gif',
				name : FD_MESS.FD_DELETE,
				handler : function()
				{
					if (confirm(oEl.icon == 'folder' ? FD_MESS.FD_CONFIRM_DEL_DIR : FD_MESS.FD_CONFIRM_DEL_FILE))
						oBXDialogWindow.Remove(path);
				}
			});
		}

		oBXFDContextMenu.Show(2500, 0, {left : e.realX, top : e.realY}, arItems);
	}
	else if (oBXFDContextMenu)
			oBXFDContextMenu.menu.PopupHide();

	if (e.stopPropagation)
	{
		e.preventDefault() ;
		e.stopPropagation() ;
	}
	else
	{
		e.cancelBubble = true;
		e.returnValue = false;
	}

	return false;
}


function BXDialogTabs()
{
	BXDialogTabs.prototype.Init = function()
	{
		this.contTable = document.getElementById("__bx_tab_cont");
		this.arTabs = {};
		this.tabsCount = 0;
		this.activeTabName = '';
	}

	BXDialogTabs.prototype.AddTab = function(name,title,fFunc,bActive)
	{
		if (this.tabsCount >2)
		{
			alert('BXFileDialog: Too much tabs');
			return;
		}

		this.arTabs[name] =
		{
			name : name,
			title : title,
			func : fFunc,
			active : bActive,
			disable : false
		};

		if (bActive)
		{
			if (this.activeTabName != '' && this.activeTabName != name)
				this.arTabs[this.activeTabName].active = false;

			this.activeTabName = name;
		}

		this.tabsCount++;
	}

	BXDialogTabs.prototype.DisplayTabs = function()
	{
		this.contTable.innerHTML = "";
		if (this.tabsCount < 2)
		{
			this.DisplayStub();
			return;
		}
		this.contTable.style.backgroundColor = "#d7d7d7";
		var _this = this;
		var createBlankImage = function(oCell,width,className)
		{
			var _style = "background-image: url(/bitrix/images/main/file_dialog/tabs/tab_icons.gif);";
			oCell.innerHTML = '<img class=" '+className+'" src="/bitrix/images/1.gif" height="27px" width="'+width+'px" style="'+_style+'"/>';
		}

		var createTitleArea = function(oCell,name,title,hint,bActive,bDisable)
		{
			oCell.innerHTML = title;
			oCell.style.cursor = "default";
			if (bActive)
				oCell.className = "fd_tabs_a";
			else if (bDisable)
				oCell.className = "fd_tabs_pd";
			else
				oCell.className = "fd_tabs_p";

			oCell.style.padding = "0px 5px 0px 5px";
			if (!bDisable)
				oCell.onclick = function(e){_this.SetActive(name,!bActive);};
			oCell.title = hint;
		}

		var count = 0;
		var oTab;
		var class1, class2, class3;
		var oTable = this.contTable.appendChild(document.createElement("TABLE"));

		oTable.className = "tab-content-table";
		oTable.cellpadding = "0";
		oTable.cellspacing = "0";
		oTable.style.borderCollapse = "collapse";

		oTable.style.height = "27px";
		oTable.style.width = "100%";
		var oRow = oTable.insertRow(-1);
		for (var name in this.arTabs)
		{
			count++;
			oTab = this.arTabs[name];
			oCell_1 = oRow.insertCell(-1);
			oCell_1.style.width = "0%";
			oCell_2 = oRow.insertCell(-1);
			oCell_2.style.width = "0%";

			if (count == 1)
			{
				if (oTab.active)
					createBlankImage(oCell_1,6,"fd_tabs_0a");
				else
					createBlankImage(oCell_1,6,"fd_tabs_0p");

				createTitleArea(oCell_2,oTab.name,oTab.title,_ReplaceNbspBySpace(oTab.title),oTab.active,oTab.disable);
			}
			else if (this.tabsCount == count)
			{
				oCell_3 = oRow.insertCell(-1);
				if (oTab.active)
				{
					createBlankImage(oCell_1,11,"fd_tabs_pa");
					createTitleArea(oCell_2,oTab.name,oTab.title,_ReplaceNbspBySpace(oTab.title),oTab.active,oTab.disable);
					createBlankImage(oCell_3,9,"fd_tabs_a0");
				}
				else
				{
					createBlankImage(oCell_1,11,"fd_tabs_ap");
					createTitleArea(oCell_2,oTab.name,oTab.title,_ReplaceNbspBySpace(oTab.title),oTab.active,oTab.disable);
					createBlankImage(oCell_3,9,"fd_tabs_p0");
				}
			}
		}
		lastCell = oRow.insertCell(-1);
		lastCell.style.width = "100%";
		lastCell.className = "fd_tabs_0";

	}

	BXDialogTabs.prototype.SetActive = function(tabName,bActive)
	{
		var oTab = this.arTabs[tabName];
		if (oTab.active)
			return;

		for (var name in this.arTabs)
			this.arTabs[name].active = false;

		oTab.active = true;

		if (oTab.func)
			oTab.func();

		this.DisplayTabs();
	}


	BXDialogTabs.prototype.DisplayStub = function()
	{
	}


	BXDialogTabs.prototype.DisableTab = function(tabName,bDisable)
	{
		if (!this.arTabs[tabName])
			return;
		var br = false;
		for (var name in this.arTabs)
		{
			this.arTabs[name].active = false;
			if (name == tabName || br)
				continue;

			this.arTabs[name].active = true;
			this.arTabs[name].func();
			br = true;
		}

		this.arTabs[tabName].disable = bDisable;

		this.DisplayTabs();
	}

	this.Init();
}


function BXDialogControls()
{
	var _this = this;
	this.DefaultDirName = 'New Folder';
	this.dirPath = new __DirPathBar();
	this.filePath = new __FilePathBar();
	this.Preview = new __Preview();
	this.ViewSelector = new __ViewSelector();
	this.SortSelector = new __SortSelector();
	this.Uploader = new __Uploader();
	this.Filter = new __FileFilter();
	this.History = new __History();

	this.currentSite = BXSite;

	// Part of logic of JCFloatDiv.Show()   Prevent bogus rerendering window in IE...
	window.fd_view_list.BuildItems();
	this.fd_view_list_frame = document.body.appendChild(CreateElement('IFRAME',{id: 'fd_view_list_frame', src: "javascript:''", style: {position: 'absolute', zIndex: 2495, left: '-2000px', top: '-2000px', visibility: 'hidden'}}));
	if (window.fd_site_list)
	{
		window.fd_site_list.BuildItems();
	this.fd_site_list_frame = document.body.appendChild(CreateElement('IFRAME',{id: 'fd_site_list_frame', src: "javascript:''", style: {position: 'absolute', zIndex: 2495, left: '-2000px', top: '-2000px', visibility: 'hidden'}}));
	}


	this.SubmButton = document.getElementById("__bx_fd_submit_but");
	this.SubmButton2 = document.getElementById("__bx_fd_submit_but2");
	if (oBXFileDialog.oConfig.operation == 'O')
	{
		this.SubmButton.value = this.SubmButton2.value = FD_MESS.FD_BUT_OPEN;
		this.SubmButton.onclick = this.SubmButton2.onclick = function(e)
		{
			SubmitFileDialog();
		}
	}
	else if (oBXFileDialog.oConfig.operation == 'S')
	{
		this.SubmButton.value = this.SubmButton2.value = FD_MESS.FD_BUT_SAVE;
		this.SubmButton.onclick = this.SubmButton2.onclick = function(e)
		{
			SubmitFileDialog();
		}
	}

	if (oBXFileDialog.oConfig.operation == 'S' && oBXFileDialog.oConfig.showAddToMenuTab)
	{
		document.getElementById("__bx_page_title_cont").style.display = "block";
		this.PageTitle1 = document.getElementById("__bx_page_title1");
		this.PageTitle2 = document.getElementById("__bx_page_title2");
		this.PageTitle1.onchange = function(e)
		{
			_this.PageTitle2.value = this.value;
		}
		this.PageTitle2.onchange = function(e)
		{
			_this.PageTitle1.value = this.value;
		}
		this.PageTitle = {};
		this.PageTitle.Get = function()
		{
			return _this.PageTitle1.value;
		}

		this.PageTitle.Set = function(value)
		{
			_this.PageTitle1.value = _this.PageTitle2.value = value;
		}

		var defTitleInp = document.getElementById('title');
		if (defTitleInp)
			this.PageTitle.Set(defTitleInp.value);
		else
			this.PageTitle.Set('Title');
	}

	this.GoButton = document.getElementById("__bx_dir_path_go");
	this.GoButton.onclick = function(e)
	{
		var sPath = oBXDialogControls.dirPath.Get();
		oBXDialogWindow.loadFolderContent(sPath);
		oBXDialogTree.HighlightPath(sPath);
		oBXDialogControls.dirPath.Set(sPath);
	}

	this.UpButton = document.getElementById("__bx_dir_path_up");
	this.UpButton.onclick = function(e)
	{
		var sPath = oBXDialogControls.dirPath.Get();
		if (sPath == '')
			return;

		var newPath = sPath.substr(0,sPath.lastIndexOf('/'));
		oBXDialogWindow.loadFolderContent(newPath);
		oBXDialogTree.HighlightPath(newPath);
		oBXDialogControls.dirPath.Set(newPath);
	}

	this.RootButton = document.getElementById("__bx_dir_path_root");
	this.RootButton.onclick = function(e)
	{
		if (oBXDialogControls.dirPath.Get() == "")
			return;
		oBXDialogWindow.loadFolderContent("");
		oBXDialogTree.HighlightPath("");
		oBXDialogControls.dirPath.Set("");
	}


	this.NewDirButton = document.getElementById("__bx_new_dir");
	this.NewDirButton.onclick = function(e)
	{
		oBXDialogWindow.AddNewElement();
	}

	this.NewDirButtonActive = true;
	this.NewDirButtonChange = function()
	{
		var path = oBXDialogControls.dirPath.Get();
		if (path == '' || path == '/')
			var oEl = window._arPermissions['/'];
		else
			var oEl = window._arPermissions[path];

		if (!oEl)
			return;
		if (oEl.new_folder && !this.NewDirButtonActive)
		{
			this.NewDirButton.className = "fd_iconkit new_dir";
			this.NewDirButtonActive = true;
		}
		else if(!oEl.new_folder && this.NewDirButtonActive)
		{
			this.NewDirButton.className = "fd_iconkit new_dir_dis";
			this.NewDirButtonActive = false;
		}
	}
}

BXDialogControls.prototype.RefreshOnclick = function()
{
	window._arTreeItems = {};
	window._arFiles = {};
	window.bxfdTreeTable = false;

	oBXDialogWindow.loadFolderContent('', true, true);
	var path = oBXDialogControls.dirPath.Get() || '';
	if (path != '')
		oBXDialogWindow.loadFolderContent(path, true);
	oBXDialogTree.HighlightPath(path);
}

BXDialogControls.prototype.SiteSelectorOnChange = function(site)
{
	if (this.currentSite == site)
		return window.fd_site_list.PopupHide();
	if (!window.bx_fd_site_selector)
		window.bx_fd_site_selector = document.getElementById('__bx_site_selector');
	window.bx_fd_site_selector.innerHTML = '<span>' + site.toUpperCase() + '</span>';
	this.currentSite = site;
	this.RefreshOnclick();
	// Cange selected item in selector
	fd_site_list.SetItemIcon(window.bx_fd_site_selector.getAttribute('bxvalue'), '');
	fd_site_list.SetItemIcon(site, 'checked');
	window.bx_fd_site_selector.setAttribute('bxvalue', site);
	window.fd_site_list.PopupHide();
};

BXDialogControls.prototype.SiteSelectorOnClick = function(node)
{
	var pos = jsUtils.GetRealPos(node);
	pos.left += 2;
	setTimeout(function(){window.fd_site_list.PopupShow(pos);}, 5);
};


function __BXSort(arr,sort,sort_order)
{
	var _name = function(a,b)
	{
		if ((a.icon == 'folder' && b.icon == 'folder') || (a.icon != 'folder' && b.icon != 'folder'))
			return common_sort(a.name,b.name);
		else if (a.icon == 'folder' && b.icon != 'folder')
			return -1*(sort_order == 'des' ? -1 : 1);
		else
			return 1*(sort_order == 'des' ? -1 : 1);
	};
	var _size = function(a,b)
	{
		var _a = parseInt(a.size);
		var _b = parseInt(b.size);

		return common_sort(_a,_b);
	};
	var _type = function(a,b)
	{
		if ((a.icon == 'folder' && b.icon == 'folder') || (a.ext == b.ext))
			return common_sort(a.name,b.name);
		else if (a.icon != 'folder' && b.icon != 'folder')
			return common_sort(a.ext,b.ext);
		else if (a.icon == 'folder' && b.icon != 'folder')
			return -1*(sort_order == 'des' ? -1 : 1);
		else
			return 1*(sort_order == 'des' ? -1 : 1);
	};
	_date = function(a,b)
	{
		var _a = parseInt(a.timestamp);
		var _b = parseInt(b.timestamp);

		if ((a.icon == 'folder' && b.icon == 'folder') || (a.icon != 'folder' && b.icon != 'folder'))
			return common_sort(_a,_b);
		else if (a.icon == 'folder' && b.icon != 'folder')
			return -1*(sort_order == 'des' ? -1 : 1);
		else
			return 1*(sort_order == 'des' ? -1 : 1);
	}

	var common_sort = function(a,b)
	{
		var res = 1;
		if (a < b)
			res = -1;
		else if (a == b)
			res = 0;

		if (sort_order == 'des')
			res = -res;

		return res;
	}

	switch (sort)
	{
		case 'name' :
			arr.sort(_name);
			break;
		case 'size' :
			arr.sort(_size);
			break;
		case 'type' :
			arr.sort(_type);
			break;
		case 'date' :
			arr.sort(_date);
			break;
	}
	return arr;
}

function __DirPathBar()
{
	this.oInput = document.getElementById("__bx_dir_path_bar");
	this.oInput.onkeypress = function(e)
	{
		if (!e)
			e = window.event;

		if (e.keyCode == 13)
		{
			var sPath = oBXDialogControls.dirPath.Get();
			oBXDialogWindow.loadFolderContent(sPath);
			oBXDialogTree.HighlightPath(sPath);
			oBXDialogControls.dirPath.Set(sPath);
		}
	}
	this.value = this.oInput.value;

	this.butBack = document.getElementById("__bx_dir_path_back");
	this.butForward = document.getElementById("__bx_dir_path_forward");
	this.butBack.onclick = function(e)
	{
		var newPath = oBXDialogControls.History.Back();
		if (newPath !== false)
		{
			oBXDialogWindow.loadFolderContent(newPath);
			oBXDialogTree.HighlightPath(newPath);
			oBXDialogControls.dirPath.Set(newPath);
		}
	}
	this.butForward.onclick = function(e)
	{
		var newPath = oBXDialogControls.History.Forward();
		if (newPath !== false)
		{
			oBXDialogWindow.loadFolderContent(newPath);
			oBXDialogTree.HighlightPath(newPath);
			oBXDialogControls.dirPath.Set(newPath);
		}
	}

	__DirPathBar.prototype.Set = function(sValue)
	{
		if (!sValue || sValue == "")
			sValue = "/";
		sValue = sValue.replace(/\/\//ig,"/");

		if (this.value != sValue)
		{
			this.oInput.value = this.value = sValue;
			this.OnChange();
		}
		else
			this.oInput.value = this.value = sValue;
	};

	__DirPathBar.prototype.Get = function()
	{
		var path = this.oInput.value;
		path = path.replace(/\\/ig,"/");
		path = path.replace(/\/\//ig,"/");
		if (path.substr(path.length-1) == "/")
			path = path.substr(0,path.length-1);

		return path;
	};

	__DirPathBar.prototype.OnChange = function()
	{
		var _get = this.Get();
		oBXDialogControls.UpButton.className = "fd_iconkit "+(_get == "" ? "dir_path_up_dis" : "dir_path_up");
		oBXFileDialog.UserConfig.path = _get;
		oBXDialogControls.History.Push(_get);
		SaveConfig(oBXFileDialog.UserConfig);
	};
}

function __FilePathBar()
{
	__FilePathBar.prototype.Init = function()
	{
		this.oInput = document.getElementById("__bx_file_path_bar");
		if (oBXFileDialog.oConfig.operation == 'S')
		{
			var defFilenameInp = document.getElementById('filename');
			if (defFilenameInp && defFilenameInp.value.length > 0)
				this.defaultName = defFilenameInp.value;
			else
			{
				var exts = oBXFileDialog.oConfig.fileFilter, ext;
				if (exts.length > 0)
				{
					var ind = exts.indexOf(',');
					ext = (ind > 0) ? exts.substr(0, ind) : exts;
				}
				else
					ext = 'php';
				this.defaultName = "untitled." + ext;
			}
		}
		this.oInput.onclick = function(){this.focus()};
	};

	__FilePathBar.prototype.Set = function(sValue)
	{
		this.oInput.value = sValue;
	};

	__FilePathBar.prototype.Get = function()
	{
		return this.oInput.value;
	};

	this.Init();
}

function __Preview()
{
	this.oDiv = document.getElementById("__bx_previewContainer");
	this.addInfoCont = document.getElementById("__bx_addInfoContainer");
	if (oBXFileDialog.oConfig.select == 'D')
		this.oDiv.parentNode.style.visibility = "hidden";

	__Preview.prototype.Display = function(sPath)
	{
		this.oDiv.innerHTML = "";
		this.oDiv.style.padding = "0px";
		var fileName = getFileName(sPath);

		if (_IsImage(fileName))
			this.DisplayImage(sPath);
		else if (_IsUserExt(fileName,['swf']))
			this.DisplayFlash(sPath);
		else
			this.DisplayBigIcon(sPath);
	}

	__Preview.prototype._GetRealSize = function(src)
	{
		var _div = document.getElementById("__bx_get_real_size_cont");
		_div.innerHTML = "<IMG id='__bx_get_real_size_img' src='"+src+"' />";
		var oImg = document.getElementById("__bx_get_real_size_img");
		var w = parseInt(oImg.offsetWidth);
		var h = parseInt(oImg.offsetHeight);
		return {'w':w,'h':h};
	}


	__Preview.prototype.DisplayImage = function(sPath)
	{
		var _this = this;
		var _div = document.getElementById("__bx_get_real_size_cont");
		_div.innerHTML = "<IMG id='__bx_get_real_size_img' src='"+sPath+"' />";
		var oImg = document.getElementById("__bx_get_real_size_img");
		oImg.onload = function()
		{
			var w = parseInt(oImg.offsetWidth);
			var h = parseInt(oImg.offsetHeight);
			_this._DisplayImage(sPath,{w:w,h:h});
		}
	}


	__Preview.prototype._DisplayImage = function(sPath,_oSize)
	{
		var w = _oSize.w;
		var h = _oSize.h;
		var oEl = window._arFileList[sPath];
		var _date = oEl.date.substr(0,oEl.date.lastIndexOf(':'));
		var _add_info = w+" x "+h+", "+_ReplaceSpaceByNbsp(getFileSize(oEl.size))+"<br/>"+_date;
		this.addInfoCont.innerHTML = _add_info;

		var a = 100; //max height
		var b = 130; //max width
		var oImg = document.createElement('IMG');
		oImg.src = sPath;
		var newW = w + "px";
		var newH = h + "px";

		if (a/b > h/w)
		{
			//Resize by width
			if (w > b)
			{
				newW = b+"px";
				newH = Math.round(h * b / w) + "px";
			}
		}
		else
		{
			//Resize by height
			if (h > a)
			{
				newH = a+"px";
				newW = Math.round(w * a / h) + "px";
			}
		}

		//this.addInfoCont.innerHTML += "2: "+newW+" : "+newH;
		oImg.style.width = newW;
		oImg.style.height = newH;
		this.oDiv.appendChild(oImg);
		this.oDiv.style.paddingLeft = Math.round((b - parseInt(newW)) / 2) + "px";
		this.oDiv.style.paddingTop = Math.round((a - parseInt(newH)) / 2) + "px";
		this.oDiv.style.paddingBottom = Math.round((a - parseInt(newH)) / 2) + "px";
	}

	__Preview.prototype.DisplayFlash = function(sPath)
	{
		var a = 90;
		var w = a+"px";
		var h = a+"px";

		var oEl = window._arFileList[sPath];
		var _date = oEl.date.substr(0,oEl.date.lastIndexOf(':'));
		var _add_info = _ReplaceSpaceByNbsp(getFileSize(oEl.size))+"<br/>"+_date;
		this.addInfoCont.innerHTML = _add_info;

		var pFrame = document.createElement("IFRAME");
		pFrame.setAttribute("id", "__bx_ifrm_flash");
		pFrame.setAttribute("src", "javascript:''");
		pFrame = this.oDiv.appendChild(pFrame);

		pFrame.style.width = w;
		pFrame.style.padding = "0px";
		pFrame.style.margin = "0px";
		pFrame.style.height = h;
		pFrame.border = "0px";
		pFrame.frameborder="0"
		pFrame.setAttribute("src", "/bitrix/admin/file_dialog_flash_preview.php?path="+jsUtils.urlencode(sPath)+"&width="+"86px"+"&height="+"86px");
	}

	__Preview.prototype.DisplayBigIcon = function(sPath)
	{
		var oEl = window._arFileList[sPath];
		if (oEl.icon == 'folder')
			return;
		var _date = oEl.date.substr(0,oEl.date.lastIndexOf(':'));
		var _add_info = _ReplaceSpaceByNbsp(getFileSize(oEl.size))+"<br/>"+_date;
		this.addInfoCont.innerHTML = _add_info;

		var oImg = document.createElement('IMG');
		var _src = oBXDialogWindow.iconsPath + oBXDialogWindow.arIcons[oEl.icon].big;
		oImg.src = _src;
		var _oSize = {w:25,h:25};
		var a = 90;
		oImg.style.width = _oSize.w;
		oImg.style.height = _oSize.h;
		this.oDiv.appendChild(oImg);
		this.oDiv.style.paddingLeft = Math.round((a - parseInt(_oSize.w)) / 2) + "px";
		this.oDiv.style.paddingTop = Math.round((a - parseInt(_oSize.h)) / 2) + "px";
		this.oDiv.style.paddingBottom = Math.round((a - parseInt(_oSize.h)) / 2) + "px";
	}

	__Preview.prototype.Clear = function()
	{
		this.oDiv.innerHTML = "";
		this.addInfoCont.innerHTML = "";
	}
}

function __ViewSelector()
{
	this.oSel = document.getElementById("__bx_view_selector");
	this.value = '';
	__ViewSelector.prototype.OnClick = function()
	{
		var pos = jsUtils.GetRealPos(this.oSel);
		pos.left += 7;
		pos.top += 6;
		setTimeout(function(){window.fd_view_list.PopupShow(pos);}, 5);
	};

	__ViewSelector.prototype.OnChange = function(value)
	{
		oWaitWindow.Show();
		setTimeout(function ()
			{
				var path = oBXDialogControls.dirPath.Get();
				oBXDialogWindow.DisplayElementsList(window._arTreeItems[path], window._arFiles[path], value,oBXDialogWindow.filter,oBXDialogWindow.sort, oBXDialogWindow.sort_order);
				oWaitWindow.Hide();
				oBXDialogControls.ViewSelector.Set(value);
			}, 3
		);
	};

	__ViewSelector.prototype.Set = function(value, bSaveConfig)
	{
		// Cange selected item in selector
		var cur_val = this.oSel.getAttribute('bxvalue') || '';
		fd_view_list.SetItemIcon(cur_val, '');
		fd_view_list.SetItemIcon(value, 'checked');
		this.oSel.setAttribute('bxvalue', value);
		this.value = value;
		window.fd_view_list.PopupHide();
		oBXFileDialog.UserConfig.view = value;
		if (bSaveConfig)
			SaveConfig(oBXFileDialog.UserConfig);
	}

	__ViewSelector.prototype.Get = function()
	{
		return this.value;
	}
}

function __SortSelector()
{
	var _this = this;
	this.oSel = document.getElementById("__bx_sort_selector");
	this.oCheck = document.getElementById("__bx_sort_order");

	this.oSel.onchange = function()
	{
		if (oBXDialogWindow.sort == this.value)
			return;

		oWaitWindow.Show();
		oBXDialogControls.SortSelector.OnChange();
		oBXDialogWindow.sort = this.value;
		setTimeout(function ()
			{
				var path = oBXDialogControls.dirPath.Get();
				oBXDialogWindow.DisplayElementsList(window._arTreeItems[path],window._arFiles[path],oBXDialogWindow.view,oBXDialogWindow.filter,oBXDialogWindow.sort,oBXDialogWindow.sort_order);
				oWaitWindow.Hide();

			}, 3
		);
	};

	this.oCheck.onclick = function()
	{
		var _new = (oBXDialogControls.SortSelector.SortOrderGet() == 'asc' ? 'des' : 'asc');
		oBXDialogControls.SortSelector.SortOrderSet(_new);
		oBXDialogWindow.sort_order = _new;
		oWaitWindow.Show();
		oBXDialogControls.SortSelector.OnChange();
		setTimeout(function ()
			{
				var path = oBXDialogControls.dirPath.Get();
				oBXDialogWindow.DisplayElementsList(window._arTreeItems[path],window._arFiles[path],oBXDialogWindow.view,oBXDialogWindow.filter,oBXDialogWindow.sort,oBXDialogWindow.sort_order);
				oWaitWindow.Hide();
			}, 3
		);
	}

	__SortSelector.prototype.Set = function(sort,sort_order)
	{
		this.oSel.value = sort;
		this.SortOrderSet(sort_order);
		//checked = (sort_order == 'asc') ? 'checked' : '';
		this.OnChange();
	}

	__SortSelector.prototype.Get = function()
	{
		return {sort : this.oSel.value,sort_order : this.SortOrderGet()};
	}

	__SortSelector.prototype.SortOrderSet = function(sort_order)
	{
		this.oCheck.setAttribute("__bx_value", sort_order);
		this.oCheck.className = "fd_iconkit " + ((sort_order == 'asc') ? "sort_up" : "sort_down");
	}

	__SortSelector.prototype.SortOrderGet = function()
	{
		return this.oCheck.getAttribute("__bx_value");
	}

	__SortSelector.prototype.OnChange = function()
	{
		var r = this.Get();
		oBXFileDialog.UserConfig.sort = r.sort;
		oBXFileDialog.UserConfig.sort_order = r.sort_order;
		SaveConfig(oBXFileDialog.UserConfig);
	}
}

function __FileFilter()
{
	__FileFilter.prototype.Init = function()
	{
		var filter = oBXFileDialog.oConfig.fileFilter;
		this.curentFilter = false;
		this.arFilters = [];
		var _this = this;
		this.oSel = document.getElementById("__bx_file_filter");
		this.oSel.options.length = 0;
		this.oSel.onchange = function(e)
		{
			_this.curentFilter = oBXDialogWindow.filter = this.value;
			var path = oBXDialogControls.dirPath.Get();
			oWaitWindow.Show();
			oBXDialogWindow.DisplayElementsList(window._arTreeItems[path],window._arFiles[path],oBXDialogWindow.view,oBXDialogWindow.filter,oBXDialogWindow.sort,oBXDialogWindow.sort_order);
			oWaitWindow.Hide();
		};

		var addOption = function(arExt, sExt, sTitle)
		{
			oOpt = document.createElement('OPTION');
			oOpt.value = _this.arFilters.length;
			_this.arFilters.push(arExt);
			oOpt.innerHTML = sTitle+" ("+sExt+")";
			_this.oSel.appendChild(oOpt);
			oOpt = null;
		};

		if (filter == '')
		{
			addOption('*','*.*',FD_MESS.FD_ALL_FILES);
			return;
		}

		this.oSel.style.display = 'block';
		var arExt, sExt, sTitle, oExt;
		if (typeof(filter) == 'object')
		{
			try
			{
				for (var i = 0; i < filter.length; i++)
				{
					oExt = filter[i];
					if (typeof(oExt.ext) == 'string')
						oExt.ext = oExt.ext.split(',');
					sExt = '*.'+oExt.ext.join(',*.');
					addOption(oExt.ext, sExt, oExt.title);
				}
			}
			catch(e)
			{
				arExt = filter;
				sExt = '*.'+arExt.join(',*.');
				sTitle = '';
				addOption(arExt, sExt, sTitle);
			}
		}
		else if (filter == 'image')
		{
			arExt = ['jpeg','jpg','gif','png','bmp'];
			sExt = '*.jpeg,*.jpg,*.gif,*.png,*.bmp';
			sTitle = FD_MESS.FD_ALL_IMAGES;
			addOption(arExt, sExt, sTitle);
		}
		else
		{
			arExt = filter.split(",");
			sExt = '*.'+arExt.join(',*.');
			sTitle = '';
			addOption(arExt, sExt, sTitle);
		}

		if (oBXFileDialog.oConfig.allowAllFiles)
			addOption('*','*.*',FD_MESS.FD_ALL_FILES);
		this.oSel.options[0].selected = "selected";
		this.curentFilter = 0;
	}
	this.Init();
}

function __Uploader()
{
	__Uploader.prototype.Init = function()
	{
		this.oCont = document.getElementById("__upload_container");
		this.oIfrm = document.getElementById('__bx_iframe_upload');
		this.oIfrm.src = "/bitrix/admin/file_dialog_upload.php?lang="+BXLang+"&site="+getSite();
		this.oIfrm.style.width = "100%";
		this.oIfrm.style.border = "0px";
		this.oIfrm.style.height = "140px";
		this.oIfrm.border = "0px";
		var _this = this;

		if (this.oIfrm.contentDocument)
			this.oIfrm.$document = this.oIfrm.contentDocument;
		else
			this.oIfrm.$document = this.oIfrm.contentWindow.document;

		this.oIfrm.$window = this.oIfrm.$document.contentWindow;
	}


	this.Init();
}

function __History()
{
	__History.prototype.Init = function()
	{
		this.arHistoryPath = [];
		this.currentPos = -1;
	}

	__History.prototype.Push = function(sValue)
	{
		var len = this.arHistoryPath.length;

		this.currentPos++;

		if (len == 0 || (this.arHistoryPath.length > this.currentPos-1 && this.arHistoryPath[this.currentPos-1] != sValue))
		{
			this.arHistoryPath[this.currentPos] = sValue;
			if (len > 0)
				this.ButBackDisable(false);
		}
		else
			this.currentPos--;
	}

	__History.prototype.RemoveLast = function()
	{
		this.arHistoryPath.splice(2,1);
		this.currentPos--;
		if (this.currentPos == this.arHistoryPath.length-1)
			this.ButForwardDisable(true);
	}

	__History.prototype.Back = function()
	{
		if (this.currentPos <= 0 || !this.CheckButBack())
			return false;

		this.currentPos--;

		var newPath = this.arHistoryPath[this.currentPos];

		if (newPath)
		{
			if (this.currentPos == 0)
				this.ButBackDisable(true);


			this.ButForwardDisable(false);
			return newPath;
		}
		return false;
	}

	__History.prototype.Forward = function()
	{
		var len = this.arHistoryPath.length;
		if (!this.CheckButForward() || (this.currentPos > len-2))
			return false;
		this.currentPos++;

		var newPath = this.arHistoryPath[this.currentPos];
		if (newPath)
		{
			if (this.currentPos == len-1)
				this.ButForwardDisable(true);

			this.ButBackDisable(false);
			return newPath;
		}
		return false;
	}

	__History.prototype.CheckButBack = function()
	{
		if (oBXDialogControls.dirPath.butBack.getAttribute("__bx_disable") == 'Y')
			return false;
		else
			return true;
	}

	__History.prototype.ButBackDisable = function(bDisable)
	{
		if (bDisable)
		{
			oBXDialogControls.dirPath.butBack.setAttribute("__bx_disable",'Y');
			oBXDialogControls.dirPath.butBack.className = "fd_iconkit path_back_dis";
		}
		else
		{
			oBXDialogControls.dirPath.butBack.setAttribute("__bx_disable",'N');
			oBXDialogControls.dirPath.butBack.className = "fd_iconkit path_back";
		}
	}

		__History.prototype.CheckButForward = function()
	{
		if (oBXDialogControls.dirPath.butForward.getAttribute("__bx_disable") == 'Y')
			return false;
		else
			return true;
	}

	__History.prototype.ButForwardDisable = function(bDisable)
	{
		if (bDisable)
		{
			oBXDialogControls.dirPath.butForward.setAttribute("__bx_disable",'Y');
			oBXDialogControls.dirPath.butForward.className = "fd_iconkit path_forward_dis";
		}
		else
		{
			oBXDialogControls.dirPath.butForward.setAttribute("__bx_disable",'N');
			oBXDialogControls.dirPath.butForward.className = "fd_iconkit path_forward";
		}
	}

	this.Init();
}

function _objCompare(obj1,obj2)
{
	for (var p in obj1)
	{
		if (obj1[p] != obj2[p])
			return false;
	}
	return true;
}

function _objCopy(obj)
{
	var newObj = {};
	for (var p in obj)
		newObj[p] = obj[p];
	return newObj;
}


// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

function getFileName(sPath)
{
	sPath = sPath.replace(/\\/ig,"/");
	return sPath.substr(sPath.lastIndexOf("/")+1);
}


function getExtension(sName)
{
	var ar = sName.split(".");
	return ar[ar.length-1].toLowerCase();
}


function getFileSize(size)
{
	if (size < 1024)
		return size+" "+FD_MESS.FD_BYTE;

	size = Math.round(size/1024);
	if (size < 1024)
		return size+" K"+FD_MESS.FD_BYTE;

	size = Math.round(size/1024);
	if (size < 1024)
		return size+" M"+FD_MESS.FD_BYTE;
}

function getSite(size)
{
	if (window.oBXDialogControls && window.oBXDialogControls.currentSite)
		return window.oBXDialogControls.currentSite;
	return BXSite;
}

function SaveConfig(_oConfig)
{
	if (!oBXFileDialog.oConfig.SaveConfig)
		return;

	if (_objCompare(oBXFileDialog.LastSavedConfig,_oConfig))
		return;

	var sc = new JCHttpRequest();
	sc.Action = function(result)
	{
		oBXFileDialog.LastSavedConfig = _objCopy(_oConfig);
	}

	sc.Send('/bitrix/admin/file_dialog_manage_config.php?mode=set&site='+getSite()+'&path='+jsUtils.urlencode(_oConfig.path)+'&view='+_oConfig.view+'&sort='+_oConfig.sort+'&sort_order='+_oConfig.sort_order);
}


function SubmitFileDialog()
{
	var filename = oBXDialogControls.filePath.Get();
	var path = oBXDialogControls.dirPath.Get();
	var site = oBXFileDialog.oConfig.site;

	if (filename == '')
	{
		alert(FD_MESS.FD_EMPTY_FILENAME);
		return;
	}
	if (oBXFileDialog.oConfig.operation == 'O')
	{
		window[oBXFileDialog.oConfig.submitFuncName](filename, path, site);
	}
	else if (oBXFileDialog.oConfig.operation == 'S')
	{
		var title,menuObj = {type : false};
		if (oBXFileDialog.oConfig.showAddToMenuTab)
		{
			var title = oBXDialogControls.PageTitle.Get();
			var add2MenuCheck = document.getElementById("__bx_fd_add_to_menu");
			if (add2MenuCheck.checked)
			{
				menuObj = {};
				menuObj.type = document.getElementById("__bx_fd_menutype").value;
				if (document.getElementById("__bx_fd_itemtype_n").checked)
				{
					menuObj.menu_add_new = true;
					menuObj.menu_add_name = document.getElementById("__bx_fd_newp").value;
					menuObj.menu_add_pos = document.getElementById("__bx_fd_newppos").value;

					if (menuObj.menu_add_name == '')
					{
						alert(FD_MESS.FD_INPUT_NEW_PUNKT_NAME);
						return;
					}
				}
				else
				{
					menuObj.menu_add_new = false;
					menuObj.menu_add_pos = document.getElementById("__bx_fd_menuitem").value;
				}
			}
		}
		window[oBXFileDialog.oConfig.submitFuncName](filename, path, site, title, menuObj);
	}
	oBXFileDialog.Close();
}


function _IsImage(fileName,arExt)
{
	if (!arExt)
		arExt = ['gif','jpg','jpeg','png','jpe','bmp'];
	return _IsUserExt(fileName,arExt);
}


function _IsPHP(fileName)
{
	return _IsUserExt(fileName,['php']);
}


function _IsUserExt(fileName,arExt)
{
	var ext = getExtension(fileName);
	var len = arExt.length;
	for (var i=0; i<len; i++)
	{
		if (arExt[i] == ext)
			return true;
	}
	return false;
}

function _ReplaceSpaceByNbsp(str)
{
	if (typeof(str)!='string')
		return str;
	str = str.replace(/\s/g, '&nbsp;');
	return str;
}

function _ReplaceNbspBySpace(str)
{
	if (typeof(str)!='string')
		return str;
	str = str.replace(/&nbsp;/g, ' ');
	return str;
}

function _Show_tab_OPEN()
{
	try{
		document.getElementById("__bx_fd_preview_and_panel").style.display = "block";
		document.getElementById("__bx_fd_load").style.display = "none";
		document.getElementById("__bx_fd_container_add2menu").style.display = "none";
	}catch(e){}
}

function _Show_tab_LOAD()
{
	try{
		document.getElementById("__bx_fd_preview_and_panel").style.display = "none";
		document.getElementById("__bx_fd_load").style.display = "block";
		document.getElementById("__bx_fd_container_add2menu").style.display = "none";
	}catch(e){}
}

function _Show_tab_SAVE()
{
	try{
		document.getElementById("__bx_fd_top_controls_container").style.display = "block";
		document.getElementById("__bx_fd_tree_and_window").style.display = "block";
		document.getElementById("__bx_fd_preview_and_panel").style.display = "block";
		document.getElementById("__bx_fd_load").style.display = "none";
		document.getElementById("__bx_fd_container_add2menu").style.display = "none";
	}catch(e){}
}

function _Show_tab_MENU()
{
	try{
		document.getElementById("__bx_fd_top_controls_container").style.display = "none";
		document.getElementById("__bx_fd_tree_and_window").style.display = "none";
		document.getElementById("__bx_fd_preview_and_panel").style.display = "none";
		document.getElementById("__bx_fd_load").style.display = "none";
		document.getElementById("__bx_fd_container_add2menu").style.display = "block";
		document.getElementById("__bx_fd_file_name").innerHTML = oBXDialogControls.filePath.Get();
	}catch(e){}
}


function BXMenuHandling()
{
	var _this = this;
	this.Add2MenuCheckbox = document.getElementById("__bx_fd_add_to_menu");
	this.Add2MenuCheckbox.onclick = function(e)
	{
		oBXDialogTabs.DisableTab('tab2',!this.checked);
		oBXMenuHandling.Show(this.checked);
	}
	this.MenuTypeSelect = document.getElementById("__bx_fd_menutype");
	this.MenuTypeSelect.onchange = function()
	{
		oBXMenuHandling.ChangeMenuType();
	}

	this.NewItemOpt = document.getElementById("__bx_fd_itemtype_n");
	this.ExsItemOpt = document.getElementById("__bx_fd_itemtype_e");

	var optCheck = function()
	{
		if (_this.NewItemOpt.checked)
		{
			_this._displayRow("__bx_fd_e1",true);
			_this._displayRow("__bx_fd_e2",true);
			_this._displayRow("__bx_fd_e3",false);
		}
		else
		{
			_this._displayRow("__bx_fd_e1",false);
			_this._displayRow("__bx_fd_e2",false);
			_this._displayRow("__bx_fd_e3",true);
		}
	}
	this.NewItemOpt.onclick = this.ExsItemOpt.onclick = optCheck;

	BXMenuHandling.prototype.Show = function(bShow)
	{
		if (bShow)
			document.getElementById("add2menu_table").style.display = "block";
		else
			document.getElementById("add2menu_table").style.display = "none";
	}
	//#################################################################################

	BXMenuHandling.prototype.ChangeMenuType = function()
	{
		var _path = oBXDialogControls.dirPath.Get();
		if (!window._arMenuList[_path])
			return;
		var arTypes = window._arMenuList[_path].types;
		var arItems = window._arMenuList[_path].items;

		var cur = this.MenuTypeSelect.value;
		var i;
		for(i=0; i<arTypes.length; i++)
		{
			if (cur==arTypes[i])
				break;
		}
		var itms = arItems[i];
		if (itms.length == 0)
		{
			this.NewItemOpt.checked = true;
			this.ExsItemOpt.disabled = "disabled";
			this._displayRow("__bx_fd_e1",true);
			this._displayRow("__bx_fd_e2",false);
			this._displayRow("__bx_fd_e3",false);
		}
		else if (this.NewItemOpt.checked)
		{
			this.ExsItemOpt.disabled = false;
			this._displayRow("__bx_fd_e1",true);
			this._displayRow("__bx_fd_e2",true);
			this._displayRow("__bx_fd_e3",false);
		}

		var list = document.getElementById("__bx_fd_menuitem");
		list.options.length = 0;

		for(i=0; i<itms.length; i++)
			list.options.add(new Option(itms[i], i+1, false, false));

		list = document.getElementById("__bx_fd_newppos");
		list.options.length = 0;

		for(i=0; i<itms.length; i++)
			list.options.add(new Option(itms[i], i+1, false, false));

		list.options.add(new Option(FD_MESS.FD_LAST_POINT, 0, true, true));
	}

	BXMenuHandling.prototype._displayRow = function(rowId,bDisplay)
	{
		var row = document.getElementById(rowId);
		if (bDisplay)
		{
			try{row.style.display = 'table-row';}
			catch(e){row.style.display = 'block';}
		}
		else
		{
			row.style.display = 'none';
		}
	}
	//##################################################
}


function WaitWindow()
{
	this.Show = function()
	{
		if (!this.oDiv)
		{
			this.oDiv = document.createElement("DIV");
			this.oDiv.id = "__bx_wait_window";
			this.oDiv.className = "waitwindow";
			this.oDiv.style.position = "absolute";
			this.oDiv.innerHTML = FD_MESS.FD_LOADIND;//"Loading...";
			this.oDiv.style.zIndex = "3000";
			this.oDiv.width = "150px";
			this.oDiv.style.left = '320px';
			this.oDiv.style.top = '200px';
			document.getElementById("BX_file_dialog").appendChild(this.oDiv);
		}


		this.oDiv.style.display = "block";
	}

	this.Hide = function()
	{
		if (!this.oDiv)
			this.oDiv = document.getElementById("__bx_wait_window");
		if (this.oDiv)
			this.oDiv.style.display = "none";
	}
}



//*********************************** CONTEXT MENU ********************************************//
function BXFDContextMenu()
{
	this.oDiv = document.body.appendChild(CreateElement('DIV',{id: '__BXFDContextMenu', style: {position: 'absolute', zIndex: 2500, left: '-1000px', top: '-1000px', visibility: 'hidden'}}));

	this.oDiv.innerHTML = '<table cellpadding="0" cellspacing="0">'+
		'<tr><td class="popupmenu">'+
		'<table cellpadding="0" cellspacing="0" id="__BXFDContextMenu_items">'+
		'<tr><td></td></tr>'+
		'</table>'+
		'</td></tr>'+
		'</table>';

	// Part of logic of JCFloatDiv.Show()   Prevent bogus rerendering window in IE...
	document.body.appendChild(CreateElement('IFRAME',{id: '__BXFDContextMenu_frame', src: "javascript:''", style: {position: 'absolute', zIndex: 2495, left: '-1000px', top: '-1000px', visibility: 'hidden'}}));

	this.menu = new PopupMenu('__BXFDContextMenu');
}

BXFDContextMenu.prototype.Show = function(zIndex,dxShadow,oPos, arItems)
{
	if (!arItems)
		return;
	this.menu.PopupHide();

	this.AddItems(arItems);
	if (!isNaN(zIndex))
		this.oDiv.style.zIndex = zIndex;
	if (!isNaN(dxShadow))
		this.menu.dxShadow = dxShadow;

	oPos.right = oPos.left + this.oDiv.offsetWidth;
	oPos.bottom = oPos.top;
	this.menu.PopupShow(oPos);
}

BXFDContextMenu.prototype.AddItems = function(arMenuItems)
{
	//Cleaning menu
	var tbl = document.getElementById(this.menu.menu_id+'_items');
	while(tbl.rows.length>0)
		tbl.deleteRow(0);

	//Creation menu elements
	var n = arMenuItems.length;
	for(var i=0; i<n; i++)
	{
		var row = tbl.insertRow(-1);
		var cell = row.insertCell(-1);
		if (arMenuItems[i] == 'separator')
		{
			cell.innerHTML =
				'	<table cellpadding="0" cellspacing="0" border="0" class="popupseparator">\n'+
				'		<tr><td><div class="empty"></div></td></tr>\n'+
				'	</table>\n';
		}
		else
		{
			var el_params = arMenuItems[i];
			cell.innerHTML =
				'<table cellpadding="0" cellspacing="0" class="popupitem" onMouseOver="this.className=\'popupitem popupitemover\';" onMouseOut="this.className=\'popupitem\';" __bx_i="'+i+'">\n'+
				'	<tr>\n'+
				'		<td class="gutter"><div style="background-image:url('+el_params.src+')"></div></td>\n'+
				'		<td class="item" title="'+((el_params.title) ? el_params.title : el_params.name)+'"'+'>'+el_params.name+'</td>\n'+
				'	</tr>\n'+
				'</table>';
			var oTable = cell.firstChild;

			oTable.onclick = function(e)
			{
				var i = this.getAttribute('__bx_i');
				arMenuItems[i].handler();
				oBXFDContextMenu.menu.PopupHide();
			};
			oTable.id=null;
		}
	}
	this.oDiv.style.width = tbl.parentNode.offsetWidth;
}

function CreateElement(nodeName,arParams)
{
	var el = document.createElement(nodeName);
	for(var prop in arParams)
	{
		if (prop == 'style' && typeof(arParams.style) == 'object')
			for(var st in arParams.style)
				el.style[st] = arParams.style[st];
		else
			el.setAttribute(prop, arParams[prop]);
	}
	return el;
}