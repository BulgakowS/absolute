jQuery(document).ready(function($){ 
	$('#uber-media-settings').on('click', '.uber-connect.connect', function(e) {
		e.preventDefault();
		if ($(this).is('.connect')) {
			$('body').addClass('uber-media-overlay');
			var self = this,
				w = $(this).data('w'),
				h = $(this).data('h'),
				source = $(this).data('source'),
				left = (screen.width/2)-(w/2),
				top = (screen.height/2)-(h/2),
				params = 'location=0,status=0,width=' + w + ',height=' + h + ',top=' + top + ', left=' + left;
			$(this).removeClass('connect');
			$(this).removeClass('button-primary');
			$('[data-source="' + source + '"]').text('Connecting...');
			this.oauth_window = window.open(this.href, 'Connect', params);
			
			this.interval = window.setInterval((function() {
				if (self.oauth_window.closed) {
					window.clearInterval(self.interval);
					$('body').removeClass('uber-media-overlay');
					$.post(ajaxurl, 
			            { action:'uber_check',
			              nonce: uber_media.nonce,
			              source: source }, 
			            function(data){
			               if(data.message == 'success') {
			               		$('[data-source="' + source + '"]').text('Disconnect');
			               		$('[data-source="' + source + '"]').addClass('disconnect');
			               		var sourceTitle = $('[data-source="' + source + '"]').prop('title');
			               		$('[data-source="' + source + '"]').prop('title', sourceTitle.replace("Connect", "Disconnect"));
			               		$('[data-source="' + source + '"]').prop('href', '#');
			               } else {
			               	$('[data-source="' + source + '"]').text('Connect');
			               	$('[data-source="' + source + '"]').addClass('connect');
			               	$('[data-source="' + source + '"]').addClass('button-primary');
			               }
			            }
			        , 'json');
					return false;
				}
			}), 100);
		}
	});
	
	$('#uber-media-settings').on('click', '.uber-connect.disconnect', function(e) {
		e.preventDefault();
		var r= confirm($(this).prop('title') + '?');
		if (r == true) {
	       	$.post(ajaxurl, 
	            { action:'uber_disconnect',
	              nonce: uber_media.nonce,
	              source: $(this).data('source') }, 
	            function(data){
	               if(data.message == 'success') location.reload();
	            }
	        , 'json');
	        return false;
        }
	});	
});


(function ($) {	
	
	var UberImage = Backbone.Model.extend({
	});

	var UberImages = Backbone.Collection.extend({
	    model: UberImage
	});
	
	var UberImageView = Backbone.View.extend({
	    tagName: "li",
	    className: "uber-image attachment",
	    template:  wp.media.template('uberimage'),
	    render: function () {
	       	this.$el.html( this.template( this.model.toJSON() ) );
	        return this;
	    }
	});
	
	var UberImageSidebar = Backbone.View.extend({
		tagName: "div",
		className: 'uber-sidebar media-sidebar'
	});
	
	var UberImageSettings = Backbone.View.extend({
	   	tagName: "div",
	    className: "uber-settings",
	    template:  wp.media.template('uberimage-settings'), 

	    events: {
			'click button':    'updateHandler'
		},
	    render: function () {
	       	this.$el.html( this.template( this.model.toJSON() ) );
	       	var imgdata = this.model.toJSON();
	       	var img = new Image();
			$(img).load(function () {
				$(this).attr('id', 'img-uber');
				$(this).attr('draggable', 'false');
				$(this).css('display', 'none'); // .hide() doesn't work in Safari when the element isn't on the DOM already
				$(this).hide();
				$('#uberload').hide();
				$('.thumbnail').empty();
				$('.thumbnail').append(this);
				$(this).fadeIn( function() { $("#uber-button").removeAttr('disabled'); });
				}).error(function () {
			}).attr('src', imgdata.custom_data.dataset.full); 
	        return this;
	    },
	    updateHandler: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]'),
				value = event.target.value,
				userSetting;
			event.preventDefault();
			if ( $setting.hasClass('button-group') ) {
				$buttons = $setting.find('button').removeClass('active');
				$buttons.filter( '[value="' + value + '"]' ).addClass('active');
			}
		},
	
	});
		    
	wp.media.controller.UberMedia = wp.media.controller.State.extend({
	
	    initialize: function(){
	        this.props = new Backbone.Model({ custom_data: '', method: '', param: '', images: '', selected_id: '', selected_image: '', page: '', pagin: '', altpage: '' });
	        this.props.on( 'change:custom_data', this.refresh, this );
	    },
	    refresh: function() {
	    	this.frame.toolbar.get().refresh();
		},
		customAction: function(){
		    var uberimage = this.props.get('custom_data');
		    var uber = $('#' + uberimage.id).getAttributes();
		    
		    var align = $("#align-button .button.active").val();
		    var link = $("#link-button .button.active").val();
		    var alt = $("#alt-uber").val();
		    var title = $("#title-uber").val();
		    var caption = $("#caption-uber").val();
		    var full = $("#full-uber").val();
		 
		    var img = $('#img-uber');
		    var hiddenImg = img.clone().css('visibility', 'hidden').removeAttr('height').removeAttr('width').appendTo('body');
		    var imgheight = hiddenImg.height();
		    var imgwidth = hiddenImg.width();
		    hiddenImg.remove(); 
		     
		    var imgstr = '<a href="' + link + '">';
		    var imgstr =  '<img src="' + full + '" width="' + imgwidth + '" height="' + imgheight + '" alt="' + alt + '" title="' + title + '" class="align' + align + '" />';
		    if (link != 'none') imgstr = '<a href="' + link + '">' + imgstr + '</a>';
		    
		    if (caption != '') imgstr = '[caption width="' + imgwidth + '" align="align' + align + '"]' + imgstr + ' ' + caption + '[/caption]';
		   
		    wp.media.editor.insert(imgstr);
		    this.frame.close();
		}
	    
	});
	
	wp.media.view.Toolbar.UberMedia = wp.media.view.Toolbar.extend({
		initialize: function() {
			_.defaults( this.options, {
			    event: 'custom_event',
			    close: false,
				items: {
				    custom_event: {
				        text: wp.media.view.l10n.ubermediaButton, // added via 'media_view_strings' filter,
				        style: 'primary',
				        id: 'uber-button',
				        priority: 80,
				        requires: false,
				        click: this.customAction
				    }
				}
			});
	
			wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
		},
	

		refresh: function() {
			var custom_data = this.controller.state().props.get('custom_data');
			this.get('custom_event').model.set( 'disabled', ! custom_data );
			$("#uber-button").attr('disabled','disabled');
	
			wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
		},
		
		customAction: function(){
		    this.controller.state().customAction();
		}
	});
	
	wp.media.view.UberMedia = wp.media.View.extend({
		events: {
			"change select#method": "setFilter",
			"change input#param": "setParam",
			'click .uber-image img': 'toggleSelectionHandler',
			'click .uber-image .check':  'removeFromSelection',
			'click .uber-image a':  'preventDefault',
			'click .uber-connect': 'oauthPopup',
			"click input#pagination": "getPagination",
		},
	
		initialize: function() {
		    this.sourceDetails = this.options.sourceDetails;
		    this.source = this.options.source;
		    this.imageError = '';
		    if (this.sourceDetails.url != '#') {
			    this.connect = this.make( 'a', {
				    href: this.sourceDetails.url,
				    id: 'uber-btn',
					class: 'button uber-connect',
					"data-source": this.source,
					"data-w": this.sourceDetails.w,
					"data-h": this.sourceDetails.h
				}, 'Connect');
				this.$el.append(this.connect);
		    } 
		    this.createToolbar();
		    if (this.sourceDetails.url != '#') {
		    	var toolbar = this.$el.find("#uber-toolbar");
		    	$(toolbar).hide();
		    }
		    
		    this.createSidebar(); 
		    this.model.on( 'change:custom_data', this.render, this );
		},
			
		createToolbar: function() {
			
			var images;
			var content = this.$el.find(".ubermedia"),
		        toolbar = $("<div/>", {
		        id: 'uber-toolbar'
		    });
		    this.$el.append(toolbar);
		    this.$el.find('#uber-toolbar').append(this.createSelect());
		          
		    var filter = this.$el.find(".ubermedia"),
		        paraminput = $("<input/>", {
		        id: 'param',
		        type: 'text'
		    });
		    
		    this.$el.find('#uber-toolbar').append(paraminput);
		    var filter = this.$el.find(".ubermedia"),
		        spinner = $("<span/>", {
		        class: 'spinner',
		        id: 'uberspin'
		    });
		    this.$el.find('#uber-toolbar').append(spinner);
		    $("#param").hide();
		    $("#uberspin").show();
		    var filter = this.$el.find(".ubermedia"),
		        ubermsg = $("<div/>", {
		        id: 'uber-msg'
		    });
		    this.$el.append(ubermsg);
		    $("#uber-msg").hide();
		    
		    if (this.model.get('method')) {
		    	stream = this.sourceDetails.settings[this.model.get('method')];
		    	this.filterType = this.model.get('method');
		    } else {
		    	for(var key in this.sourceDetails.settings) break;
		    	stream = this.sourceDetails.settings[key];
		    	this.filterType = key;    
	        }
	         var filter = this.$el.find(".ubermedia"),
		        page = $("<input/>", {
		        id: 'page',
		        type: 'hidden',
		        value: '1'
		    });
		    this.$el.append(page);
		    if (this.model.get( 'page')) {
		    	$(page).val(this.model.get( 'page'));
		    }
		    
		    var filter = this.$el.find(".ubermedia"),
		        altpage = $("<input/>", {
		        id: 'altpage',
		        type: 'hidden',
		        value: ''
		    });
		    this.$el.append(altpage);
		    if (this.model.get( 'altpage')) {
		    	$(altpage).val(this.model.get( 'altpage'));
		    }
		   
		    var show = this.displayParam(stream);
	        
	        if (this.model.get('param')) {
				var param = this.$el.find('#param');
				$(param).val(this.model.get('param'));
				$(param).show();
			}
	        
	        var filter = this.$el.find(".ubermedia"),
				imagelist = $("<ul/>", {
			    id: 'uberimages'
			});
			this.$el.append(imagelist);
			
			this.clearImages();
			var filter = this.$el.find(".ubermedia"),
		        paginli = $("<li/>", {
		        id: 'pagin'
		    });
			var filter = this.$el.find(".ubermedia"),
		        pagin = $("<input/>", {
		        id: 'pagination',
		        type: 'button',
		        class: 'button',
		        value: 'Load More'
		    });
		    $(paginli).append( pagin );	    
		    this.$el.find("#uberimages").append( paginli );	
						
			if (this.model.get('images')) {
		        	this.collection = new UberImages(images);
		        	this.collection.reset(this.model.get('images'));
		        	if (this.model.get( 'pagin')) {
					    this.$el.find("#pagination").hide(); 
				    } else this.$el.find("#pagination").show();
				    if (this.model.get('method')) this.displayPag(this.model.get('method'));
	        } else {
		       	this.$el.find("#pagination").hide();
		       	this.collection = new UberImages(images);
		       	if (show && this.sourceDetails.url == '#') {
		        	images = this.getImages(this, this.source, key, '', 1, '');
		        } 
	        }
			
	        this.on("change:filterType", this.filterByType, this);
	        this.on("change:paramValue", this.filterByParam, this);
	 
	        this.collection.on("reset", this.render, this);
		},
		
		oauthPopup: function( event ) {
			event.preventDefault();
			$('body').addClass('uber-media-overlay');
			var uber = this;
			var self = event.currentTarget,
			w = $(event.currentTarget).data('w'),
			h = $(event.currentTarget).data('h'),
			source = $(event.currentTarget).data('source'),
			left = (screen.width/2)-(w/2),
			top = (screen.height/2)-(h/2),
			params = 'location=0,status=0,width=' + w + ',height=' + h + ',top=' + top + ', left=' + left;
			$(event.currentTarget).removeClass('connect');
			event.currentTarget.oauth_window = window.open(event.currentTarget.href, 'Connect', params);
			
			event.currentTarget.interval = window.setInterval((function() {
				if (self.oauth_window.closed) {
					$('body').removeClass('uber-media-overlay');
					window.clearInterval(self.interval);
					$.post(ajaxurl, 
			            { action:'uber_check',
			              nonce: uber_media.nonce,
			              source: source }, 
			            function(data){
			               if(data.message == 'success') {
			               		uber.sourceDetails.url = '#'
			               		$('#uber-btn').hide();
								$('#uber-toolbar').show();
								uber.filterType = $('#method option:selected').val();
								uber.trigger("change:filterType");
			               }
			            }
			        , 'json');
					return false;
				}
			}), 100);	
		},
		
		clearImages: function() {
			this.$el.find('ul#uberimages li#pagin').prevAll().remove();
		},
		
		toggleSelectionHandler: function( event ) {
			$("#uber-button").attr('disabled','disabled');
			this.custom_update(event);
			$("ul#uberimages li").removeClass("selected");
			$("#" + event.target.id).closest('li').addClass("selected");
			this.$el.find(".uber-sidebar").empty();
			this.populateSidebar(this.model); 
		},
		
		preventDefault: function( event ) {
			event.preventDefault();
		},
		
		clearSelection: function() {
			this.model.set( 'selected_id', '' );
			this.model.set( 'selected_image', '' );
			this.model.set( 'custom_data', '' );
		},
		
		removeFromSelection: function( event ) {
			this.clearSelection();
			$("#" + event.target.id).closest('li').removeClass("selected");
			this.$el.find(".uber-sidebar").empty();
		},
		
		clearSidebar: function() {
			this.clearImages();
			this.clearSelection();
			$("ul#uberimages li").removeClass("selected");
			$(".uber-sidebar").empty();
		},
			
	    render: function () {
	        var that = this;
	        if (this.collection) {
		        if (this.collection.models.length > 0) {
			        this.clearImages();
			        _.each(this.collection.models, function (item) {
			            this.$el.find('#pagin').before(that.renderImage(item));
			        }, this);
			        this.$el.find("#uberspin").hide();
		        } else {
			        if (this.imageError != '') {
			        	$("#uber-msg").text(this.imageError);
			        	$("#uber-msg").show();
			        	this.$el.find("#uberspin").hide();
			        }
		        }
		        this.imageError = '';
		    }
	    	if (this.model.get('selected_image')) {
	    		var selectedimg = this.$el.find("img#" + this.model.get('selected_id'));
	    		$(selectedimg).closest('li').addClass("selected");
	    		this.$el.find(".uber-sidebar").append(this.model.get('selected_image'));
	    		$("#uber-button").removeAttr('disabled');
	    	}
	
	    },
	 
	    renderImage: function (item) {
	        var imageView = new UberImageView({
	            model: item
	        });
	        return imageView.render().el;
	    },
		
		custom_update: function( event ) {
			this.model.set( 'selected_id', event.target.id );
			this.model.set( 'custom_data', event.target );
		},
		
		createSelect: function () {
		    var filter = this.$el.find(".ubermedia"),
		        select = $("<select/>", {
		            html: "",
		            id: 'method'
		        });
		    var that = this;
		    _.each(this.sourceDetails.settings, function (settings, method) {
		        if (that.model.get('method') && (that.model.get('method') == method)) {
			    	var option = $("<option/>", {
			            value: method,
			            text: settings.name,
			            selected: 'selected'
			        }).appendTo(select);
			    } else {
				    var option = $("<option/>", {
			            value: method,
			            text: settings.name
			        }).appendTo(select);
			    } 
		    });
		    return select;
		},
		
		getImages: function(collection, source, method, param, page, altpage) {
			this.$el.find("#uberspin").show();
			this.model.set( 'custom_data', '');
			this.$el.find("#uber-msg").hide();
			this.$el.find("#pagination").val('Loading...');
			this.$el.find("#pagination").attr('disabled', 'disabled');
			if (page == 1) {
				this.$el.find("#pagination").hide();
				this.clearImages();
			}
			var images;
			$.post(ajaxurl, 
	            { action: 'uber_load_images',
	              source: source,
	              method: method,
	              param: param,
	              page: page,
	              altpage: altpage }, 
	            function(response){
	               if (response.error) {
	               		collection.imageError = response.message;
	               		collection.$el.find("#pagination").hide(); 
	               }
	               else {
	               		images = response.images;
		               	if(method == collection.filterType) {
		               		if (page == 1) {
		               			collection.model.set( 'images', images);
		               		}
		               		else {
			               		original = collection.model.get( 'images');
			               		collection.model.set( 'images', original.concat(images));
		               		}
		               	}
	               }
	               if(method == collection.filterType) {
	               		if (page == 1) collection.collection.reset(images);
	               		else {
		               		var pagCollection = new UberImages(images);
		               		collection.collection.add(pagCollection.models);
		               		_.each(pagCollection.models, function (item) {
					           	collection.$el.find('#pagin').before(collection.renderImage(item));
					        }, this);
	               		}
	               		collection.displayPag(method);
	               		collection.$el.find("#pagination").val('Load More');
	               		collection.$el.find("#uberspin").hide();
	               		if (response.pagin == 'end') collection.model.set( 'pagin', 'end');
	               		else {
		               		if (!response.error) {
		               			collection.$el.find("#pagination").removeAttr('disabled');
		               			collection.model.set( 'pagin', '');
		               		} else 	collection.$el.find("#pagination").hide(); 
	               		}
	               		if (response.altpage ) {
	               			collection.model.set( 'altpage', response.altpage);
	               			$("#altpage").val(response.altpage);
	               		}
	               }
	               else collection.$el.find("#uberspin").hide();
	              
	            }
	        , 'json');
	    },
	     
	    getPagination: function() {
			var page = $("#page").val();
			page++;
			$("#page").val(page);
			this.model.set( 'page', page);
			var altpage = $("#altpage").val();
			this.model.set( 'altpage', altpage);
			this.getImages(this, this.source, this.$el.find("#method").val(), this.$el.find("#param").val(), page, altpage);  
	    },
	    
	    displayPag: function(method) {
		    this.$el.find("#pagination").show();
			stream = this.sourceDetails.settings[method];
			if (stream.nopagin) this.$el.find("#pagination").hide(); 
		},
		
		displayParam: function(stream) {
			paraminput = this.$el.find("#param").hide();
			if(stream.param) {
				this.$el.find("#pagination").attr('disabled', 'disabled');
				this.$el.find("#param").val('');
				this.$el.find("#param").attr("placeholder", stream.param_desc);
				this.$el.find("#param").show();
			}
			this.$el.find("#pagination").show();
			if (stream.nopagin) this.$el.find("#pagination").hide(); 
			return !stream.param;
		},
		
		createSidebar: function() {
		    var sidebar = new UberImageSidebar();
		    this.$el.append(sidebar.render().el);
		},
		
		populateSidebar: function(item) {
			var imageSettings = new UberImageSettings({
	            model: item
	        });
	        this.model.set( 'selected_image', imageSettings.render().el );
	        this.$el.find(".uber-sidebar").append(imageSettings.render().el);
		},
		
		setFilter: function (e) {
		    this.clearSidebar();
		    this.filterType = e.currentTarget.value;
		    this.model.set( 'method', e.currentTarget.value);
		    this.model.set( 'param', '');
		    this.trigger("change:filterType");
		},
		
		setParam: function (e) {
		    if (e.currentTarget.value != '') {
			    this.clearSidebar();
			    this.paramValue = e.currentTarget.value;
			    this.model.set( 'param', e.currentTarget.value);
			    this.trigger("change:paramValue");
		    }
		},
		
		filterByType: function () {
		    this.$el.find("#uberspin").hide();
		    this.$el.find("#pagination").val('Load More');
		    this.$el.find("#page").val('1');
		    this.$el.find("#altpage").val('');
		    stream = this.sourceDetails.settings[this.filterType];
		    var show = this.displayParam(stream);
		    if (show) {
		    	var images = this.getImages(this, this.source, this.filterType, '', 1, '');
		    } else { 
		    	this.$el.find("#pagination").hide();
		    	this.collection.reset(); 
		    }
		},
		
		filterByParam: function() {
			this.$el.find("#page").val('1');
			this.$el.find("#altpage").val('');
			var images = this.getImages(this, this.source, this.$el.find("#method").val(), this.paramValue, 1, '');
		}
	
	});
	

	var oldMediaFrame = wp.media.view.MediaFrame.Post;
	wp.media.view.MediaFrame.Post = oldMediaFrame.extend({
	
	    initialize: function() {
	        oldMediaFrame.prototype.initialize.apply( this, arguments );
	        
	        var ubermedia_sources = wp.media.view.l10n.ubermedia;
	        var mediaframe = this;
		
	        var priority = 200;
	        $.each(ubermedia_sources, function(source, source_details) {
				mediaframe.states.add([
		            new wp.media.controller.UberMedia({
		                id:         source,
		                menu:       'default', // menu event = menu:render:default
		                content:    source + '-custom',
						title:      'Insert from ' + source_details.name,
						priority:   priority + 100,
						toolbar:    source + '-action', // toolbar event = toolbar:create:main-my-action
						type:       'link'
		            })
		        ]);
		       
		        mediaframe.on( 'content:render:'+ source + '-custom',  _.bind(mediaframe.customContent, mediaframe, source, source_details));
		        mediaframe.on( 'toolbar:create:'+ source + '-action', mediaframe.createCustomToolbar, mediaframe );
		        mediaframe.on( 'toolbar:render:'+ source + '-action', mediaframe.renderCustomToolbar, mediaframe );
	
			});
	        
	        
	    },
	    
	    createCustomToolbar: function(toolbar){
	        toolbar.view = new wp.media.view.Toolbar.UberMedia({
			    controller: this
		    });
	    },
	
	    customContent: function(source, source_details){
	        this.$el.addClass('hide-router');
	
	        var view = new wp.media.view.UberMedia({
	            controller: this,
	            model: this.state().props,
	            className: 'ubermedia media-' + source,
	            sourceDetails: source_details,
	            source: source
	        });
	
	        this.content.set( view );
	    }
	
	});

} (jQuery));

(function($) {
    $.fn.getAttributes = function() {
        var attributes = {}; 

        if( this.length ) {
            $.each( this[0].attributes, function( index, attr ) {
                attributes[ attr.name ] = attr.value;
            } ); 
        }

        return attributes;
    };
})(jQuery);