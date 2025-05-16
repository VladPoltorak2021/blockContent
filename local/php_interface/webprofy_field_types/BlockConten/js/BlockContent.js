var BlockContent = function(UID){
	this.UID = UID;


	this.initTextField = function(UID, data){
		var e = $(".block-content-block[data-uid='"+data.uid+"'] .block-content-data");
		var name = $(".block_content_field[data-uid='"+UID+"']").data("name");
		e.load("/local/action/BlockContent/TextEditor.php?",{
			'ID':data.uid,
			'CONTENT': data.content,
			'INPUT_NAME': name+'['+data.uid+']',
			'INPUT_ID':'block_field_'+data.uid,
			'JS_OBJ_NAME': 'js_'+data.uid,
			'VIEW': data.view,
			'MARGIN_BOTTOM': data.marginBottom,
		});
	}

	this.initProgrammField = function(UID, data){
		var e = $(".block-content-block[data-uid='"+data.uid+"'] .block-content-data");
		var name = $(".block_content_field[data-uid='"+UID+"']").data("name");

		// Empty the target element before loading new content
		e.empty();

		e.load("/local/action/BlockContent/link.php?",{
			'ID': data.uid,
			'COUNT': 1,
			'INPUT_NAME': name + '[' + data.uid + ']',
			'TYPE': data.type,
			'VIEW': data.content.view,
			'TEXT2': data.content.text2,
			'HEADER2': data.content.header2,
		});
	}


	this.initImageField = function(UID, data){
		var e = $(".block-content-block[data-uid='"+data.uid+"'] .block-content-data");
		var name = $(".block_content_field[data-uid='"+UID+"']").data("name");

		e.load("/local/action/BlockContent/ImageEditor.php?",{
			'ID':data.uid,
			'COUNT':10,
			'TEXT': data.text,
			'VIEW': data.content.view,
			'IMAGE': data.content,
			'INPUT_NAME': name+'['+data.uid+']',
			'TYPE': data.type,
			//'GALLERY': data.gallery
		});
	}


	this.addField = function(UID, data){
		$(".block-content[data-uid='"+UID+"'] .block-content-blocks").append(
			[
				'<div class="block-content-block active '+data.type+'" data-type="'+data.type+'" data-content="'+data.content+'" data-uid="'+data.uid+'">',
				'<div class="block-content-handle"></div>',
				'<div class="block-content-remove"></div>',
				'<div class="block-content-data">'+data.content+'</div>',
				'</div>'
			].join("")
		);

		switch(data.type){
			case "text":
				this.initTextField(UID, data);
				break;
			case "image":
				this.initImageField(UID, data);
				break;
			case "link":
				this.initProgrammField(UID, data);
				break;
		}
	}

	this.removeField = function(UID){
		var field = $(".block-content-block[data-uid='"+UID+"']");
		if(field.data("type") == "image" || field.data("type") == "gallery" || field.data("type") == "video" || field.data("type") == "feedback" || field.data("type") == "promo")
		{
			field.find('.adm-btn-del').trigger('click');
		}
		$(".block-content-block[data-uid='"+UID+"']").remove();
	}

	this.reInitField = function(item) {
		var UID = item.closest('.block-content').data('uid');
		var type = item.data('type');
		var new_content = item.find('input[type="hidden"]').val();
		var INPUT_NAME = item.find('input[name="INPUT_NAME"]').val();

		//item.find('.block-content-data').empty();

		var data = {
			uid: item.data('uid'),
			type: type,
			content: new_content
		};

		if(type == 'quote') {
			data = {
				uid: item.data('uid'),
				type: type,
				content: {
					view: item.find('input[name="'+INPUT_NAME+'[view]"]').val(),
					image: item.find('input[name="'+INPUT_NAME+'[image]"]').val(),
					text: item.find('input[name="'+INPUT_NAME+'[text]"]').val(),
					text2: item.find('input[name="'+INPUT_NAME+'[text2]"]').val(),
					text3: item.find('input[name="'+INPUT_NAME+'[text3]"]').val(),
				},
			};
		}

		// console.log(item);
		// console.log('input[name="'+INPUT_NAME+'[text3]"]');
		// console.log(item.find('input[name="'+INPUT_NAME+'[text]"]'));

		switch(type){
			case "text":
				this.initTextField(UID, data);
				break;
			case "image":
				this.initImageField(UID, data);
				break;
			case "link":
				this.initProgrammField(UID, data);
				break;
		}
	}

	this.Init = function(){
		var e = $("#field_"+this.UID);

		e.before(
			[
				'<div class="block-content" data-uid="'+this.UID+'">',
				'<div class="block-content-body">',
				'<div class="block-content-blocks"></div>',
				'<div class="block-content-plus text" data-type="text" data-content="">Текст</div>',
				'<div class="block-content-plus link" data-type="link" data-content="">Ссылки</div>',
				'<div class="block-content-plus image" data-type="image" data-content="">Слайдер</div>',
				'</div>',
				'<div class="block-content-footer">',
				'</div>',
				'</div>'
			].join("")
		);

		$(".block-content[data-uid='"+this.UID+"'] .block-content-plus").on('click',
			function(){
				var type = $(this).attr("data-type");
				var d = new Date();
				var id = d.getTime()+"_"+Math.round(Math.random()*1000000);
				var uid = $(this).closest(".block-content").attr("data-uid");
				BC.addField(uid,{type:type,uid:id,content:""});
			}
		);

		$( ".block-content-blocks" ).sortable({
			placeholder: "portlet-placeholder ui-corner-all",
			stop: function( event, ui){
				if(ui.item.data('type') == 'text' || ui.item.data('type') == 'quote')
					BC.reInitField(ui.item);
			}
		});

		$( ".block-content-blocks, .block-content-block, .block-content" ).disableSelection();

		$(document).on('click', '.block-content-remove',
			function(){
				var uid = $(this).closest(".block-content-block").attr("data-uid");
				BC.removeField(uid);
			}
		);

		$(document).on('click', '.js-load-video',
			function(){
				var block = $(this).closest(".block-content-block");
				var src = block.find('.js-input-video').val();
				var match = src.match(/\?v=(.*)$/);

				if(match !== null && match[1] != '')
					src = 'https://www.youtube.com/embed/' + match[1];
				else if(src != '')
					src = 'https://www.youtube.com/embed/' + src;

				if(src != '')
					block.find('.js-content-video').html('<iframe width="560" height="315" src="'+src+'" frameborder="0" allowfullscreen></iframe>');
				else
					block.find('.js-content-video').html('');
			}
		);

		try{
			var content = JSON.parse($.base64.decode(e.val()));
			var d = new Date();
			for(i in content){
				var n = d.getTime();
				content[i].uid = $.base64.encode(n)+"_"+Math.round(Math.random()*1000000)+'_'+i;
				this.addField(this.UID, content[i]);
			}
		}catch(error){}

		e.addClass("initialized");
		e = e.closest(".adm-detail-content-cell-r");
		e.parent().before('<tr class="heading"><td colspan="2">'+e.prev().text()+'</td></tr>');
		e.prev().remove();
		e.attr("colspan","2");
	}
	this.Init();
	delete this.Init;

	return this;
}

var BC;

BX.ready(function(){
	$('.adm-detail-content-btns [name="save"]').add('.adm-detail-content-btns [name="apply"]').attr('disabled', true);

	$(".block_content_field:not(.initialized)").each(
		function(){
			BC = new BlockContent($(this).attr("data-uid"));
		}
	);

	setTimeout(function (){
		$('.adm-detail-content-btns [name="save"]').add('.adm-detail-content-btns [name="apply"]').attr('disabled', false);
	}, 1000)
});
