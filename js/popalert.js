var alertconText='<div class="popWinBox" style="display: none; z-index:111;" id="conWindow">'+
 ' <div class="pop_div">'+
    '<div class="title_div"><a href="javascript:;" onclick="popConClose();" class="closeBtn">X</a><span class="title_divText" id="title_divSpan">��ʾ</span> </div>'+
    '<div class="pop_txt01" id="conMessage">'+
    '</div>'+
	 '<ul class="com_btn_list clearfix">'+
        '<li><a class="okBtn" href="javascript:;"  jsBtn="okBtn" >确定</a></li>'+
        '<li><a class="calBtn" href="javascript:;"  jsBtn="calBtn" >删除</a></li>'+
      '</ul>'+
  '</div>'+
 ' <div class="popmap"></div>'+
'</div>';

var wholeIndex = 1;

function getPopText(index){
	return '<div class="popWinBox" style="display: none;" id="popWindow'+index+'">'+
	 ' <div class="pop_div">'+
		'<div class="title_div"><a href="javascript:;" onclick="popClose(this);" class="closeBtn">&nbsp;</a><span class="title_divText"></span> </div>'+
		'<div class="pop_txt01">'+
		'</div>'+
		 '<ul class="com_btn_list clearfix">'+
			'<li><a class="red_btn" href="javascript:;"  jsBtn="okBtn" ></a></li>'+
			'<li><a class="grey_border_btn" href="javascript:;"  jsBtn="calBtn" ></a></li>'+
		  '</ul>'+
	  '</div>'+
	 ' <div class="popmap"></div>'+
	'</div>';
}

function popConClose(){
	$("#conWindow").remove();
}

function alertconShow(titText,okBtn,calBtn,talkText){
	if($('#conWindow').length==0)
	{
		$(alertconText).appendTo('body');
	}
	
	$('#conWindow').css('z-index', zIndex);
	$('#conWindow').find('.popmap').css('z-index', zIndex+1);
	$('#conWindow').find('.pop_div').css('z-index', zIndex+2);
	zIndex = zIndex + 1;
	
	$('#conWindow #title_divSpan').html(titText);
	$('#conWindow #conMessage').html(talkText);
	$('#conWindow').show();
		
	$("#conWindow").undelegate('click');
	if(okBtn!=""){
		$("#conWindow [jsBtn='okBtn']").click(function(){
		 	eval(okBtn);
		});
	}else{
		$('#conWindow [jsBtn="okBtn"]').remove();
	};
	if(calBtn!=""){
		$("#conWindow [jsBtn='calBtn']").click(function(){
		 	eval(calBtn);
		});
	}else{
		$('#conWindow [jsBtn="calBtn"]').remove();
	}
	
}

function popClose(obj){
	$(obj).closest(".popWinBox").remove();
}
// titText,okBtn,calBtn,url, fn

/*
* titText:.
* okBtn:.
* calBtn:.
* height:.
* width:.
* url:Чurl.
**/
function popShow(settings){
    this.settings = settings;
	
	var index = wholeIndex;
	wholeIndex += 1;
	var popWindowId = '#popWindow' + index;
	var popMessageId = '#popMessage' + index;
	if($(popWindowId).length==0)
	{
		var popText = getPopText(index);
		$(popText).appendTo('body');
	}
	
	this.index = index;
	
	var popWindowObj = $(popWindowId);
	var popMessageObj = popWindowObj.find('.popConts');
	
	popWindowObj.css('z-index', zIndex);
	zIndex = zIndex + 1;
	
	$(popWindowId + ' .title_divText').html(settings.titText);
	
	popWindowObj.show();
		
	popWindowObj.undelegate('click');
	if(settings.okBtn){
		popWindowObj.delegate('[jsBtn="okBtn"]',"click",function(){
		 	eval(settings.okBtn);
		});
	}else{
		$(popWindowId + ' [jsBtn="okBtn"]').remove();
		
	}
	if(settings.calBtn){
		popWindowObj.delegate('[jsBtn="calBtn"]',"click",function(){
		 	eval(settings.calBtn);
		});
	}else{
		$(popWindowId + ' [jsBtn="calBtn"]').remove();
	}
	
	if(settings.classNames){
		$(popWindowId + ' .pop_div').addClass(settings.classNames);
		var hp=$(popWindowId + ' .pop_div').height();
		if(settings.okBtn||settings.calBtn){
			popMessageObj.height(hp - 88);
		}else{
			popMessageObj.height(hp - 44);
		}
	}
	
	
	if(settings.height){
		$(popWindowId + ' .pop_div').height(settings.height).css('margin-top', -1 * (settings.height/2));
		popMessageObj.height(settings.height - 88);
	}
	
	if(settings.width){
		$(popWindowId + ' .pop_div').width(settings.width).css('margin-left', -1 * (settings.width/2));
	}
	
	if(settings.url){
		if(settings.iframeName){
			var iframeHtml='<iframe width="100%" height="99%" frameborder="0" src="'+settings.url+'" name="'+settings.iframeName+'" allowtransparency="true" scrolling="auto">';
			popMessageObj.html(iframeHtml);
			return this;
		}
		
		$.ajax({
            type: "GET",
            url: settings.url,
			async: false,
            contentType: "application/json; charset=utf-8",
            success: function (data){
            	if (settings.fnsuc && $.isFunction(settings.fnsuc)) {
		 	     	var a=settings.fnsuc(data);
					popMessageObj.html(a);
		   		 }else{
		   		 	popMessageObj.html(data);
		   		 }
			},
            error:  function (data){
            	if (settings.fnerr && $.isFunction(settings.fnerr)) {
				    var b=settings.fnerr(data);
					popMessageObj.html(b);
				}else{
					popMessageObj.html(data);
				}
			}
        });
    }else if(settings.html){
	    popMessageObj.append(settings.html);
	}
	
	return this;
	
}

/**
 * 
 **/
function getAlertText(index){
    return '<div class="popWinBox" style="display: none;" id="alertWindow'+index+'">'+
		' <div class="pop_div">'+
			'<div class="title_div"><a href="javascript:;" onclick="alertClose(this);" class="popClose">X</a><span class="title_div"></span> </div>'+
			'<div class="popConts">'+
			'</div>'+
			'<div class="popBtn">'+
				'<a class="okBtn" href="javascript:;"  jsBtn="okBtn" >确定</a>'+
				'<a class="calBtn" href="javascript:;"  jsBtn="calBtn" >删除</a>'+
		  ' </div>'+
	    '</div>'+
		' <div class="popmap"></div>'+
	'</div>';
}

var zIndex = 99999;

function alertClose(obj){
    $(obj).closest('.popWinBox').remove();
}

// titText,okBtn,calBtn,talkText
function alertShow(settings){

    this.settings = settings;
	
	var index = wholeIndex;
	wholeIndex += 1;
	var alertWindowId = '#alertWindow' + index;
	if($(alertWindowId).length==0)
	{
		var alertText = getAlertText(index);
		$(alertText).appendTo('body');
	}
	this.index = index;
	var alertWindowObj = $(alertWindowId);
	
	zIndex = zIndex + 1;
	
	alertWindowObj.css('z-index', zIndex);

	alertWindowObj.find('span.title_divText').html(settings.titText);
	alertWindowObj.find('div.popConts').html(settings.talkText);
	alertWindowObj.show();
		
	alertWindowObj.undelegate('click');
	if(settings.okBtn){
		alertWindowObj.find("[jsBtn='okBtn']").click(function(){
		 	eval(settings.okBtn);
		});
	}else{
		alertWindowObj.find("[jsBtn='okBtn']").remove();
	};
	if(settings.calBtn){
		alertWindowObj.find("[jsBtn='calBtn']").click(function(){
		 	eval(settings.calBtn);
		});
	}else{
		alertWindowObj.find("[jsBtn='calBtn']").remove();
	}
	
	return this;
	
}