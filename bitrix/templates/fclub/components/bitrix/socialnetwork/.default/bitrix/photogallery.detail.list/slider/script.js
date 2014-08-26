PhotoConstructor = function(data)
{
	_this = this;
	this.oData = (typeof data == "object" ? data : false);
	this.Table = false, this.Tbody = false, this.Row = false;
	this.Result = {"prev" : [], "curr": [], "next":[]};
	this.params = {"width":120, "height":120, "count_visible_cell":4};
	this.speed = {};
	
	
	this.Init = function(table_pointer, params)
	{
		if (typeof this.oData != "object")
			return false;
		if (typeof this.oData["curr"] != "object" || typeof this.oData["prev"] != "object" || typeof this.oData["next"] != "object")
			return false;
		if (!table_pointer)
			return false;

		this.Result["prev"] = this.oData["prev"];
		this.Result["curr"] = this.oData["curr"];
		this.Result["next"] = this.oData["next"];
		
		this.params["count_visible_cell"] = this.Result["curr"].length;
		
		if (typeof params == "object")
		{
			for(var ii in params)	
				this.params[ii] = params[ii];
		}
		
		this.Table = table_pointer;
		this.Show();
		if (window.b_active_is_fined == 'N')
			this.GoToFirst();
	}
	
	this.CheckButtons = function()
	{
		if (this.Result["prev"].length > 0)
		{
			this.params["prev"].onclick = new Function("PhotoTape['"+this.params["index"]+"'].GoToPrev();");
			this.params["prev"].className = this.params["prev"].className.replace("-disabled", "");
			this.params["prev"].onmouseover=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = (tmp[0] + ' ' + tmp[0] + '-over');};
			this.params["prev"].onmouseout=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = tmp[0];};
			this.params["prev"].onmousedown=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-active');};
			this.params["prev"].onmouseup=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-over');};
			
			this.params["first"].onclick = new Function("PhotoTape['"+this.params["index"]+"'].GoToFirst();");
			this.params["first"].className = this.params["first"].className.replace("-disabled", "");
			this.params["first"].onmouseover=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = (tmp[0] + ' ' + tmp[0] + '-over');};
			this.params["first"].onmouseout=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = tmp[0];};
			this.params["first"].onmousedown=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-active');};
			this.params["first"].onmouseup=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-over');};
		}
		else
		{
			var tmp = this.params["prev"].className.split(' ');
			if (tmp.length <= 0)
				tmp[0] = this.params["prev"].className;
			this.params["prev"].className = tmp[0] + "-disabled";
			this.params["prev"].onclick = function(){return false;};
			this.params["prev"].onmouseover=function(){return false;};
			this.params["prev"].onmouseout=function(){return false;};
			this.params["prev"].onmousedown=function(){return false;};
			this.params["prev"].onmouseup=function(){return false;};
			
			var tmp = this.params["first"].className.split(' ');
			if (tmp.length <= 0)
				tmp[0] = this.params["first"].className;
			this.params["first"].className = tmp[0] + "-disabled";
			this.params["first"].onclick = function(){return false;};
			this.params["first"].onmouseover=function(){return false;};
			this.params["first"].onmouseout=function(){return false;};
			this.params["first"].onmousedown=function(){return false;};
			this.params["first"].onmouseup=function(){return false;};
		}
		
		if (this.Result["next"].length > 0)
		{
			this.params["next"].onclick = new Function("PhotoTape['"+this.params["index"]+"'].GoToNext();");
			this.params["next"].className = this.params["next"].className.replace("-disabled", "");
			this.params["next"].onmouseover=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = (tmp[0] + ' ' + tmp[0] + '-over');};
			this.params["next"].onmouseout=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = tmp[0];};
			this.params["next"].onmousedown=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-active');};
			this.params["next"].onmouseup=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-over');};
			
			this.params["last"].onclick = new Function("PhotoTape['"+this.params["index"]+"'].GoToLast();");
			this.params["last"].className = this.params["last"].className.replace("-disabled", "");
			this.params["last"].onmouseover=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = (tmp[0] + ' ' + tmp[0] + '-over');};
			this.params["last"].onmouseout=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className = tmp[0];};
			this.params["last"].onmousedown=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-active');};
			this.params["last"].onmouseup=function(){var tmp=this.className.split(' '); if(tmp.length<=0){tmp[0]=this.className;} this.className=(tmp[0] + ' ' + tmp[0] + '-over');};
		}
		else
		{
			var tmp = this.params["next"].className.split(' ');
			if (tmp.length <= 0)
				tmp[0] = this.params["next"].className;
			this.params["next"].className = tmp[0] + "-disabled";
			this.params["next"].onclick = function(){return false;};
			this.params["next"].onmouseover=function(){return false;};
			this.params["next"].onmouseout=function(){return false;};
			this.params["next"].onmousedown=function(){return false;};
			this.params["next"].onmouseup=function(){return false;};
			
			var tmp = this.params["last"].className.split(' ');
			if (tmp.length <= 0)
				tmp[0] = this.params["last"].className;
			this.params["last"].className = tmp[0] + "-disabled";
			this.params["last"].onclick = function(){return false;};
			this.params["last"].onmouseover=function(){return false;};
			this.params["last"].onmouseout=function(){return false;};
			this.params["last"].onmousedown=function(){return false;};
			this.params["last"].onmouseup=function(){return false;};
		}
	}
	
	this.MoveCell = function(duration, koeff_speed, time)
	{
		koeff_speed = 0;
		time = parseInt(time);
		time = (time <= 0 ? 500 : time);
		
		if (!this.speed[koeff_speed + ":" + time])
			this.speed[koeff_speed + ":" + time] = (parseInt(this.params["width"]) + 0.5*koeff_speed*time*time)/time;
			
		var timestart = new Date().getTime();
		var timepos = 0;
		var timestep = 0;
		
		while (timepos < time)
		{
			timestep = new Date().getTime();
			timepos = timestep - timestart;
			left = Math.round(this.speed[koeff_speed + ":" + time]*timepos - 0.5*koeff_speed*timepos*timepos);
			this.Table.style.left = "-" + left + "px";
//	    	ShowDebug("timepos: " + timepos + "\nthis.Table.style.left: " + this.Table.style.left);
		}

	    this.Table.style.left = "-" + this.params["width"] + "px";
	}
	
	this.ShowDiv = function()
	{
		
	}
	
	this.Show = function(direction, arr, params)
	{
		if (typeof params != "object")
			params = {};
		if (false && direction && typeof arr == "object")
		{
			if (!this.Row)
				this.Show(false, false, {"skip_check_buttons":"Y"});

			cell = document.createElement('td');
			cell.style.width = this.params["width"] + "px";
			anchor = document.createElement('a');
			anchor.href = arr["url"];
			anchor.style.width = (parseInt(arr["width"]) + 12) + "px";
			if (arr["active"] == "Y")
				anchor.className = "active";
			img = document.createElement('img');
			img.style.width = arr["width"] + "px";
			img.style.height = arr["height"] + "px";
			img.alt = arr["alt"];
			img.title = arr["title"];
			img.src = arr["src"];
			anchor.appendChild(img);
			cell.appendChild(anchor);
			
			if (direction == "next")
			{
				this.Row.appendChild(cell);
				this.MoveCell("right", 0, 500);
			}
		}
		
		if (params["skip_create_body"] != "Y")
		{
			var tbody = document.createElement('tbody');
			var row = document.createElement('tr');
			var ii = 0, res = [], cell = false;
			for(ii = 0; ii < this.Result["curr"].length; ii++)
			{
				res = this.Result["curr"][ii];
				cell = document.createElement('td');
				cell.style.width = this.params["width"] + "px";
				anchor = document.createElement('a');
				anchor.href = res["url"];
				anchor.style.width = (parseInt(res["width"]) + 12) + "px";
				if (res["active"] == "Y")
					anchor.className = "active";
				img = document.createElement('img');
				img.style.width = res["width"] + "px";
				img.style.height = res["height"] + "px";
				img.alt = res["alt"];
				img.title = res["title"];
				img.src = res["src"];
				anchor.appendChild(img);
				cell.appendChild(anchor);
				row.appendChild(cell);
			}
			row = tbody.appendChild(row);
			tbody = this.Table.appendChild(tbody);
			var prev = tbody.previousSibling;
			prev.parentNode.removeChild(prev);
			
			this.Row = row;
			this.Tbody = tbody;
			this.Table.style.left = "0px";
		}
		
		if (params["skip_check_buttons"] != "Y")
		{
			this.CheckButtons();
		}
	}
	
	this.GoToNext = function()
	{
		var arr = this.Result["next"].shift();
		if (typeof arr != "object")
			return false;
		this.Result["curr"].push(arr);
		if (this.Result["curr"].length > this.params["count_visible_cell"])
		{
			arr = this.Result["curr"].shift();
			this.Result["prev"].push(arr);
		}
		this.Show("next", arr);
	}
	
	this.GoToFirst = function()
	{
		if (this.Result["prev"].length <= 0)
			return false;
		var arr = [];
		while (arr = this.Result["prev"].pop())
		{
			this.Result["curr"].unshift(arr);
			if (this.Result["curr"].length > this.params["count_visible_cell"])
			{
				arr = this.Result["curr"].pop();
				this.Result["next"].unshift(arr);
			}
		}
		this.Show("first", arr);
	}
	
	this.GoToPrev = function()
	{
		var arr = this.Result["prev"].pop();
		if (typeof arr != "object")
			return false;
		this.Result["curr"].unshift(arr);
		if (this.Result["curr"].length > this.params["count_visible_cell"])
		{
			arr = this.Result["curr"].pop();
			this.Result["next"].unshift(arr);
		}
		this.Show("prev", arr);
	}
	
	
	this.GoToLast = function()
	{
		if (this.Result["next"].length <= 0)
			return false;
		var arr = [];
		while (arr = this.Result["next"].shift())
		{
			this.Result["curr"].push(arr);
			if (this.Result["curr"].length > this.params["count_visible_cell"])
			{
				arr = this.Result["curr"].shift();
				this.Result["prev"].push(arr);
			}
		}
		this.Show("last", arr);
	}
}

bPhotoUtilsLoad = true;