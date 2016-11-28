/**
 *  stibnite.js
 *  
 *  author: Pierre Marchand <pierremarc07@gmail.com>
 *  
 *  date: 2012-02-17
 * 
 */



var canvas = undefined;
var stibnite_gal_images = undefined;
var stibnite_gal_images_count = 0;
var stibnite_gal_cur_image = -1;
var stibnite_gal_raster = undefined;
var stibnite_tools = undefined;
var notificationPoint = 0;
var notificationBox = jQuery('<div id="notification-box"><span id="notification-message"></span><span id="notification-point-0">.</span><span id="notification-point-1">.</span><span id="notification-point-2">.</span></div>');


var AsyncImage = function(preURL, URL, container, idx, next)
{
	this.preURL = preURL;
	this.url = URL;
	this.container = jQuery(container);
	this.index = idx;
	this.next = next;
	this.preloaded = false;
	this.loaded = false;
	return this;
}


AsyncImage.prototype.load = function()
{
	var that = this;
	console.log('Async Load: '+that.index);
	that.pre = jQuery('<img src="" />');
	that.main = jQuery('<img src="" />');
	that.container.append(that.pre);
	that.container.append(that.main);
	
	that.pre.on('load', function(){
		that.preloaded = true;
		that.container.trigger('preload_complete', [that.index]);
		if(that.next)
		{
			that.next.load();
		}
	});
	that.main.on('load', function(){
		that.loaded = true;
		that.container.trigger('load_complete', [that.index]);
	});
	
	that.pre.attr('src', that.preURL);
	that.main.attr('src', that.url);
}

AsyncImage.prototype.show = function()
{
	var that = this;
	stibnite_update_nav();
	jQuery('#wrk-titre').html(that.title);
	jQuery('#wrk-tech').html(that.desc);
	jQuery('#gal-button-box').hide();
	stibnite_notify_stop();
	if(that.loaded)
	{
		stibnite_notify_stop();
		if(stibnite_gal_raster)
			stibnite_gal_raster.remove();
		stibnite_gal_raster = new paper.Raster(that.main[0]);
		stibnite_gal_raster.fitBounds(paper.view.bounds, false);
		paper.view.draw();
		jQuery('#gal-button-box').show();
	}
	else
	{
		if(that.preloaded)
		{
			stibnite_notify_stop();
			stibnite_notify_start('loading fullsize image ', '#wrkbox');
			if(stibnite_gal_raster)
				stibnite_gal_raster.remove();
			stibnite_gal_raster = new paper.Raster(that.pre[0]);
			stibnite_gal_raster.fitBounds(paper.view.bounds, false);
			paper.view.draw();
			that.container.on('load_complete', function(event, idx){
				if(idx == that.index)
				{
					that.show();
				}
			});
		}
		else
		{
			stibnite_notify_start('loading preview ', '#canvas-wrap');
			that.container.on('preload_complete', function(event, idx){
				if(idx == that.index)
				{
					that.show();
				}
			});
		}
	}
}

AsyncImage.prototype.lookup = function(idx)
{
	var ret = this;
	while(ret)
	{
		if(ret.index == idx)
			return ret;
		ret = ret.next;
	}
	return undefined;
}


function stibnite_notification_animate()
{
	if(!notificationBox.is(':visible'))
		return;
	for(var i = 0; i < 3; i++)
	{
		if(i <= notificationPoint)
			jQuery('#notification-point-'+i).show();
		else
			jQuery('#notification-point-'+i).hide();
	}
	notificationPoint++;
	if(notificationPoint > 2)
		notificationPoint = 0;
	window.setTimeout(stibnite_notification_animate, 700);
}

function stibnite_notify_start(note, parent)
{
	jQuery(parent).prepend(notificationBox);
	jQuery('#notification-message').html(note);
	stibnite_notification_animate();
}

function stibnite_notify_stop()
{
	notificationBox.detach();
}

function stibnite_gallery_nav(event)
{
	var that = jQuery(this);
	if(that.attr('id') == 'gal-nav-next')
		stibnite_gal_cur_image++;
	else
		stibnite_gal_cur_image--;
	if(stibnite_gal_cur_image >= stibnite_gal_images_count)
		stibnite_gal_cur_image = 0;
	if(stibnite_gal_cur_image < 0)
		stibnite_gal_cur_image = stibnite_gal_images_count -1;
		
	stibnite_gal_images.lookup(stibnite_gal_cur_image).show();

	
}

function stibnite_show_fit(e)
{
	stibnite_gal_raster.fitBounds(paper.view.bounds, false);
	paper.view.draw();
}

function stibnite_show_full(event)
{
	var jcanvas = jQuery(canvas);
	jcanvas.addClass('grab');
// 	jcanvas.animate({width : jcanvas.width()*2}, 'slow');
	var r = stibnite_gal_raster.getBounds();
	var s = stibnite_gal_raster.getWidth() / r.getWidth() ;
	var r1 = r.scale(s);
	stibnite_gal_raster.setBounds(r1);
	paper.view.draw();
}

function stibnite_canvas_mup(e)
{
	jQuery(canvas).removeClass('grabbing');
	jQuery(canvas).addClass('grab');
}

function stibnite_canvas_mdown(e)
{
	jQuery(canvas).removeClass('grab');
	jQuery(canvas).addClass('grabbing');
}

function stibnite_raster_pan(e)
{
	
	var d = e.delta.clone();
	var r = stibnite_gal_raster.getBounds();
	var p = new paper.Point(r.getPoint().x + d.x, r.getPoint().y + d.y);
	r.setPoint(p);
	stibnite_gal_raster.setBounds(r);
	paper.view.draw();
}

function stibnite_update_nav()
{
	jQuery('#gal-nav-ordinal').text( (stibnite_gal_cur_image + 1) + ' / ' + stibnite_gal_images_count );
}

function stibnite_load_gal(postURL)
{
	var wb = jQuery('#wrkbox');
	wb.show();
	wb.load( postURL, {'embed' : 1, 'gal_skel': 1} ,function()
	{
		canvas = document.getElementById('wrk-canvas');
		paper.setup(canvas);
		stibnite_tools = new paper.Tool();
		stibnite_tools.onMouseUp = stibnite_canvas_mup;
		stibnite_tools.onMouseDown = stibnite_canvas_mdown;
		stibnite_tools.onMouseDrag = stibnite_raster_pan;
		
		var icontainer = jQuery('<div id="gal-imgs" style="display:none"></div>');
		wb.append(icontainer);
		
		jQuery.getJSON( postURL, {'embed' : 1, 'gal_content': 1} ,function(data, textStatus, jqXHR)
		{
			var d = data;
			stibnite_gal_images = undefined;
			stibnite_gal_images_count = d.count;
			jQuery('#wrktxt').append(d.content);
			for(var i = stibnite_gal_images_count - 1; i >= 0; i--)
			{
				stibnite_gal_images = new AsyncImage(d.pre_img[i][0], d.src_img[i][0], icontainer, i, stibnite_gal_images);
				stibnite_gal_images.title = d.title[i];
				stibnite_gal_images.desc = d.desc[i];
			}
			stibnite_gal_images.load();
			stibnite_gal_cur_image = 0;
			stibnite_gal_images.show();
			jQuery('.gal-nav').on('click',stibnite_gallery_nav);
			jQuery('#gal-button-full').click(stibnite_show_full);  
			jQuery('#gal-button-fit').click(stibnite_show_fit);
			
		});
	});
}

function stibnite_init()
{
	
    jQuery('#wrkbox').hide();
    jQuery('#up').draggable();
    
    jQuery('#close-up').click(function(){jQuery('#up').hide()});
    
    jQuery('.wrk-wrap-link').click(function()
    {
	jQuery('html,body').animate({scrollTop : 120}, 'slow');
	jQuery('#up').fadeOut();
	stibnite_load_gal(jQuery(this).attr('href'));
	return false;
    });
    
    jQuery('#wrkbox-label-toggle').click(function(){
        jQuery('#wrktxt').show();
        jQuery(this).hide();
    });
}

jQuery(document).ready(stibnite_init);
