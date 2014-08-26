PhotoClassSlider = function()
{
	__this = this;
	this.iActive =0;
	this.Result = {'prev' : [], 'curr' : [], 'next' : []};
	this.params = {'count_visible_cell' : 1, 'period' : 5, 'play' : true, 'play_count' : 0};
	this.events = {'onFinished' : ''};
	
	this.oImage = {'node' : false, 'id' : '', 'params' : {}};
	this.oDivTitle = {'node' : false, 'id' : ''};
	this.oCounter = {'node' : false, 'id' : ''};
	this.bReady = true;
	
	this.Init = function(arImages, params)
	{
		this.iActive = parseInt(params['iActive']);
		this.Result = {'prev' : [], 'curr' : [], 'next' : []};
		this.params = {'count_visible_cell' : 1, 'period' : (parseInt(params['period']) > 0 ? parseInt(params['period']) : 5 ), 'play' : (params['play'] ? true : false), 'play_count' : 0};
		this.events = {'onFinished' : params['onFinished']};
		
		this.oImage = {'node' : document.getElementById(params['sImage']), 'id' : params['sImage']};
		this.oDivTitle = {'node' : document.getElementById(params['sImageTitleDiv']), 'id' : params['sImageTitleDiv']};
		this.oCounter = {'node' : document.getElementById(params['sCounterDiv']), 'id' : params['sCounterDiv']};
		
		if (typeof(this.oImage['node']) != "object" || typeof(this.oDivTitle['node']) != "object")
			return false;

		if (typeof arImages != "object" || arImages.length <= 0)
			return false;
			
		var oImages = {};
		var oImage = {};
		
		var bFinedActive = false;
		
		for (var ii = 0; ii < arImages.length; ii++)
		{
			oImage = {
				'id' : parseInt(arImages[ii]['id']),
				'src': arImages[ii]['src'],
				'width' : arImages[ii]['width'],
				'height': arImages[ii]['height'],
				'title' : arImages[ii]['title'],
				'alt' : arImages[ii]['alt']};
				
			if (!oImage['src'] || oImage['src'].length <= 0)
				continue;


			if (oImage['id'] == this.iActive)
			{
				this.Result['curr'].push(oImage);
				bFinedActive = true;
			}
			else if (!bFinedActive)
				this.Result['prev'].push(oImage);
			else
				this.Result['next'].push(oImage);
		}
		
		if (this.Result['curr'].length <= 0 && this.Result['next'].length <= 0)
		{
			this.Result['next'] = this.Result['prev'];
			this.Result['prev'] = [];
			this.Result['curr'].push(this.Result['next'].shift());
		}
		return true;
	}
	
	this.Resize = function ()
	{
		__this.Show(true, true);
		return;
	}
	
	this.Show = function(skipPlay)
	{
		if (!this.bReady)
			return ;
		
		skipPlay = (skipPlay == true ? true : false);
		if ((typeof(this.Result)!="object") || this.Result['curr'].length <= 0)
			return false;
			
		var koeff = {'width' : 1, 'height' : 1, 'min' : 1};

		var res = {
				'id' : parseInt(this.Result['curr'][0]['id']),
				'src': this.Result['curr'][0]['src'],
				'width' : parseInt(this.Result['curr'][0]['width']),
				'height': parseInt(this.Result['curr'][0]['height']),
				'title' : this.Result['curr'][0]['title'],
				'alt' : this.Result['curr'][0]['alt']};
				
		if (res["width"] == 0)
			res["width"] = 1;
		if (res["height"] == 0)
			res["height"] = 1;

		var bodySize = {
			'width' : parseInt(document.body.clientWidth) - 50,
			'height' : parseInt(document.body.clientHeight) - 45};
			
		if ((res["width"] > bodySize['width']) || (res["height"] > bodySize['height']))
		{
			koeff['width'] = (bodySize['width']/res["width"]);
			koeff['height'] = (bodySize['height']/res["height"]);
			
			koeff['min'] = (koeff['width'] < koeff['height'] ? koeff['width'] : koeff['height']);
			koeff['min'] = (koeff['min'] < 1 ? koeff['min'] : 1);
			
			if (koeff['min'] < 1)
			{
				res['width'] = parseInt(res['width'] * koeff['min']);
				res['height'] = parseInt(res['height'] * koeff['min']);
			}
		}
		
		var img = document.createElement('img');
		img.style.width = "1px";
		img.style.height = "1px";
		img.style.position = "absolute";
		
		this.oImage['node'] = this.oImage['node'].parentNode.appendChild(img);
		this.oImage['node'].onload = new Function(
			'var prev = this.previousSibling; ' + 
			'prev.parentNode.removeChild(prev);' +
			'this.style.width = "' + res["width"] + 'px";' +
			'this.style.height = "' + res["height"] + 'px";' +
			'this.style.position = "static";' +
			'__this.bReady = true;' +
			'document.getElementById("image-upload").style.display = \'none\';' +
			'__this.oDivTitle["node"].innerHTML = "' + res["title"] + '";' +
			'__this.oCounter["node"].innerHTML = (__this.Result["prev"].length + 1);' +
			((this.params['play'] && !skipPlay) ? 
				'setTimeout(new Function("__this.GoToNext(true, \'" + __this.params["play_count"] + "\')"), __this.params["period"]*1000);' : ''));
		this.oImage['node'].title = res["title"];
		this.oImage['node'].alt = res["alt"];
		this.bReady = false;
		document.getElementById("image-upload").style.display = 'block';
		this.oImage['node'].src = res["src"];
		return true;
	}
	
	this.GoToNext = function(checkPlay, play_count)
	{
		checkPlay = (checkPlay == true ? true : false);
		if (checkPlay && (!this.params['play'] || (this.params['play_count'] != play_count)))
			return true;

		var arr = this.Result['next'].shift();
		if (typeof arr != "object")
		{
			eval(this.events['onFinished']);
			return false;
		}
		this.Result['curr'].push(arr);
		if (this.Result['curr'].length > this.params["count_visible_cell"])
		{
			arr = this.Result['curr'].shift();
			this.Result['prev'].push(arr);
		}
		return this.Show();
	}
	
	this.GoToFirst = function()
	{
		if (this.Result['prev'].length <= 0)
			return false;
		var arr = [];
		while (arr = this.Result['prev'].pop())
		{
			this.Result['curr'].unshift(arr);
			if (this.Result['curr'].length > this.params["count_visible_cell"])
			{
				arr = this.Result['curr'].pop();
				this.Result['next'].unshift(arr);
			}
		}
		return this.Show();
	}
	
	this.GoToPrev = function()
	{
		var arr = this.Result['prev'].pop();
		if (typeof arr != "object")
			return false;
		this.Result['curr'].unshift(arr);
		if (this.Result['curr'].length > this.params["count_visible_cell"])
		{
			arr = this.Result['curr'].pop();
			this.Result['next'].unshift(arr);
		}
		return this.Show();
	}
	
	
	this.GoToLast = function()
	{
		if (this.Result['next'].length <= 0)
			return false;
		var arr = [];
		while (arr = this.Result['next'].shift())
		{
			this.Result['curr'].push(arr);
			if (this.Result['curr'].length > this.params["count_visible_cell"])
			{
				arr = this.Result['curr'].shift();
				this.Result['prev'].push(arr);
			}
		}
		return this.Show();
	}
}

PhotoClassNavigator = function()
{
	_this = this;
	this.control = {'next' : '', 'prev' : '', 'pause' : ''};
	this.params = {'period' : '', 'play' : true};
	this.slider = false;
	this.play_count = 0;
	
	this.Init = function(arImages, params)
	{
		this.control = {
			'next' : document.getElementById(params['next']),
			'prev' : document.getElementById(params['prev']),
			'pause' : document.getElementById(params['pause']),
			'stop' : document.getElementById(params['stop']),
			'minus' : document.getElementById(params['minus']),
			'plus' : document.getElementById(params['plus']),
			'time' : document.getElementById(params['time'])};
		this.params = {
			'period' : (parseInt(params['period']) > 0 ? parseInt(params['period']) : 5),
			'play' : true,
			'url' : params['url']};

		if (typeof(this.control['prev']) != "object" || 
			typeof(this.control['next']) != "object" || 
			typeof(this.control['pause']) != "object" || 
			typeof(this.control['stop']) != "object")
			return false;
		
		this.control['next'].onclick = function(){_this.Next();};
		this.control['prev'].onclick = function(){_this.Prev();};
		this.control['pause'].onclick = function(){_this.Play();};
		this.control['stop'].onclick = function(){_this.Stop();};
			
		this.slider = new PhotoClassSlider();
		params['play'] = false;
		this.slider.Init(arImages, params);
		this.slider.Show(true);
		jsUtils.addEvent(window, "resize", this.slider.Resize);
		this.InitTime();
		jsUtils.addEvent(document, "keypress", _this.CheckPress);
		if (parseInt(params["iActive"]) <= 0)
			this.Play();
	}
	
	
	this.Play = function()
	{
		this.play_count++;
		
		this.slider.params['play_count'] = this.play_count;
		this.slider.params['play'] = true;
		
		this.control['pause'].id = 'pause';
		this.control['pause'].onclick = function(){_this.Pause();};
		
		this.slider.events['onFinished'] = "_this.Pause();";
		setTimeout(new Function("_this.GoToNext(true, '" + _this.play_count + "')"), this.params['period']*1000);
	}
	
	this.Pause = function()
	{
		this.params['play'] = false;
		this.slider.params['play'] = false;
		this.control['pause'].id = 'play';
		this.control['pause'].onclick = function(){_this.Play();};
		
		this.slider.events['onFinished'] = function(){return false;};
	}
	
	this.Stop = function()
	{
		_this.Pause();
		if (_this.params['url'] && _this.params['url'].length > 0)
		{
			var url = _this.params['url'].replace('#element_id#', _this.slider.Result['curr'][0]["id"]);
			window.location = url;
		}
	}

	this.Next = function()
	{
		this.Pause();
		this.GoToNext();
	}
	
	this.Prev = function()
	{
		this.Pause();
		this.GoToPrev();
	}
	
	this.GoToNext = function(checkPlay, play_count)
	{
		checkPlay = (checkPlay == true ? true : false);
		if (this.params['play'])
		{
			if (!this.slider.GoToNext(checkPlay, play_count))
			{
				this.slider.GoToFirst();
				this.Play();
			}
		}
		else
		{
			if (!this.slider.GoToNext(checkPlay, play_count))
			{
				this.slider.GoToFirst();
			}
		}
		return;
	}
	
	this.GoToPrev = function()
	{
		if (!this.slider.GoToPrev())
			this.slider.GoToLast();
		return;
	}
	
	this.InitTime = function()
	{
		if (typeof(this.control['time']) != "object")
			return false;
		this.control['time'].innerHTML = this.params['period'];
		
		if (typeof(this.control['minus']) == "object")
		{
			this.control['minus'].onclick = function()
			{
				if (_this.params['period'] <= 1)
				{
					return false;
				}
				else
				{
					_this.params['period']--;
					_this.control['time'].innerHTML = _this.params['period'];
					_this.slider.params['period'] = _this.params['period'];
				}
			};
		}
		
		if (typeof(this.control['plus']) == "object")
		{
			this.control['plus'].onclick = function()
			{
				if (_this.params['period'] > 20)
				{
					return false;
				}
				else
				{
					_this.params['period']++;
					_this.control['time'].innerHTML = _this.params['period'];
					_this.slider.params['period'] = _this.params['period'];
				}
			};
		}
	}
	
	this.CheckPress = function(e)
	{
		if(!e) e = window.event
		if(!e) return;
		if(e.keyCode == 27)
			_this.Stop();
	}
}
function GetWindowSize()
{
	var innerWidth, innerHeight;

	if (self.innerHeight) // all except Explorer
	{
		innerWidth = self.innerWidth;
		innerHeight = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight) // Explorer 6 Strict Mode
	{
		innerWidth = document.documentElement.clientWidth;
		innerHeight = document.documentElement.clientHeight;
	}
	else if (document.body) // other Explorers
	{
		innerWidth = document.body.clientWidth;
		innerHeight = document.body.clientHeight;
	}

	var scrollLeft, scrollTop;
	if (self.pageYOffset) // all except Explorer
	{
		scrollLeft = self.pageXOffset;
		scrollTop = self.pageYOffset;
	}
	else if (document.documentElement && document.documentElement.scrollTop) // Explorer 6 Strict
	{
		scrollLeft = document.documentElement.scrollLeft;
		scrollTop = document.documentElement.scrollTop;
	}
	else if (document.body) // all other Explorers
	{
		scrollLeft = document.body.scrollLeft;
		scrollTop = document.body.scrollTop;
	}

	var scrollWidth, scrollHeight;

	if ( (document.compatMode && document.compatMode == "CSS1Compat"))
	{
		scrollWidth = document.documentElement.scrollWidth;
		scrollHeight = document.documentElement.scrollHeight;
	}
	else
	{
		if (document.body.scrollHeight > document.body.offsetHeight)
			scrollHeight = document.body.scrollHeight;
		else
			scrollHeight = document.body.offsetHeight;

		if (document.body.scrollWidth > document.body.offsetWidth || 
			(document.compatMode && document.compatMode == "BackCompat") ||
			(document.documentElement && !document.documentElement.clientWidth)
		)
			scrollWidth = document.body.scrollWidth;
		else
			scrollWidth = document.body.offsetWidth;
	}

	return  {"innerWidth" : innerWidth, "innerHeight" : innerHeight, "scrollLeft" : scrollLeft, "scrollTop" : scrollTop, "scrollWidth" : scrollWidth, "scrollHeight" : scrollHeight};
}
bPhotoUtilsLoad = true;