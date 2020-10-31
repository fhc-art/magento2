function customHomeSlider() {
	slider = jQuery("#home-slider");
	navigation = slider.data('navigation');
	pagination = slider.data('pagination');
	slideSpeed = slider.data('speed');
	auto_play = slider.data('autoplay');
	rewind_speed = slider.data('rewindspeed');
	stop_on_hover = slider.data('stoponhover');
	navigation ? navigation = true : navigation = false;
	pagination ? pagination = true : pagination = false;
	jQuery('body').css('direction') == 'rtl' ? isRtl = true : isRtl = false;
	auto_play ? auto_play = true : auto_play = false;
	rewind_speed ? rewind_speed : rewind_speed = 1000;
	stop_on_hover ? stop_on_hover = true : stop_on_hover = false;
	slider.owlCarousel({
		items : 1,
		nav : navigation,
		navSpeed : slideSpeed,
		dots: pagination,
		dotsSpeed : 400,
		rtl: isRtl,
		loop: true,
		autoplayTimeout : rewind_speed,
		autoplay : auto_play,
		autoplayHoverPause : stop_on_hover
	});
}

function pageNotFound() {
	if(jQuery('.not-found-bg').data('bgimg')){
		var bgImg = jQuery('.not-found-bg').data('bgimg');
		jQuery('.not-found-bg').attr('style', bgImg);
	}
}

function footerSmallPage() {
    if (jQuery(document.body).height() < jQuery(window).height()) {
        var offset = jQuery(window).height() - jQuery(document.body).height();
        jQuery('body .content-wrapper > .container').css('min-height',  jQuery('body .content-wrapper > .container').outerHeight() + offset);
    }
}

function shopByListener(a) {
    var b = a.touches[0];
    if (jQuery(b.target).parents("#layered-filter-block").length == 0 && jQuery(b.target).parents(".shop-by-button").length == 0 && !jQuery(b.target).hasClass("shop-by-button") && !jQuery(b.target).hasClass('block-layered-nav')) {
        jQuery("#layered-filter-block").removeClass("active");
        jQuery('.shop-by .shop-by-button').removeClass('active');
        jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},500);
        jQuery('#layered-filter-block').animate({'right' : '-300px'},500);
        document.removeEventListener("touchstart", shopByListener, false)
    }
}

function shopByClick() {
    jQuery('.shop-by .shop-by-button').off().on('click', function(e) {
        if (!jQuery('#layered-filter-block').hasClass('active')) {
            jQuery('#layered-filter-block').addClass('active');
            shopButton = jQuery('.shop-by .shop-by-button');
            shopButton.addClass('active');
            shopBlockWidth = jQuery('#layered-filter-block').width();
            jQuery('html body').animate({'margin-left' : '-300px', 'margin-right' : '300px'},500);
            jQuery('#layered-filter-block').animate({'right' : '0'},500);
            if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i))){
                document.addEventListener("touchstart", shopByListener, false);
            } else {
                jQuery(document).on("click", function(f) {
                    if (jQuery(f.target).parents("#layered-filter-block").length == 0 && jQuery(f.target).parents(".shop-by-button").length == 0 && !jQuery(f.target).hasClass(".shop-by-button") && !jQuery(b.target).hasClass('block-layered-nav')) {
                        jQuery("#layered-filter-block").removeClass("active");
                        jQuery('.shop-by .shop-by-button').removeClass('active');
                        jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},500);
                        jQuery('#layered-filter-block').animate({'right' : '-300px'},500);
                        jQuery(document).off("click");
                    }
                })
            }


        } else {
            e.stopPropagation();
            jQuery('#layered-filter-block').removeClass('active');
            shopButton = jQuery('.shop-by .shop-by-button');
            shopButton.removeClass('active');
            jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},500);
            jQuery('#layered-filter-block').animate({'right' : '-300px'},500);
        }
    })
}

function accordionNav(){
	if(jQuery('.block.filter').length){
		jQuery('.filter-options-title').off().on('click', function(){
			jQuery(this).parents('.filter-options-item').toggleClass('active').children('.filter-options-content').slideToggle();
		});
		if(jQuery(document.body).width() < 767 && jQuery('body').hasClass('page-layout-1column')){
			jQuery('#layered-filter-block .filter-title').on('click', function(){
				if(!jQuery('#layered-filter-block').hasClass('active')) {
					jQuery('#layered-filter-block').addClass('active');
				} else {
					jQuery('#layered-filter-block').removeClass('active');
				}
			});

		}
	}
}

function verticalHeader(){
		if(jQuery('.header-wrapper').hasClass('vertical-header')){
			windowHeight = jQuery(window).height();
			jQuery('.vertical-header').css('height', windowHeight).children('.vertical-header-block').animate({'opacity' : 1});
			if (jQuery(document.body).width() > 1007 && jQuery(document.body).width() <= 1374){
				jQuery('.menu-button').off().on('click', function(){
					jQuery(this).parent('.navbar-header').toggleClass('active');
					jQuery('.topmenu').slideToggle(0).toggleClass('active');
				});

				menuHeight = jQuery('ul.topmenu').css({
					opacity: 0,
					display: 'block',
					position: 'absolute'
				}).height();
				jQuery('ul.topmenu').css({
					opacity: '',
					display: '',
					position: ''
				});
				freeSpace = jQuery('.header-wrapper.vertical-header').height() - jQuery('.header-wrapper.vertical-header').height() - jQuery('.vertical-header-block').height() - 50;
				if(menuHeight >= freeSpace){
					jQuery('.header-wrapper.vertical-header .menu-block').addClass('over');
				}

				jQuery(document).on('touchstart.closetopmenu', function(event){
					if(jQuery('.header-wrapper.vertical-header .navbar-header.active').length && !jQuery(event.target).hasClass('navbar-header') && (jQuery(event.target).parents('.navbar-header').length == 0) && (jQuery(event.target).parents('.topmenu').length == 0)){
						jQuery('.header-wrapper.vertical-header .navbar-header').removeClass('active');
						jQuery('.topmenu').slideUp(0).removeClass('active');
					}
				});
			}
			if(jQuery(document.body).width() <= 1007){
				footerHeight = jQuery('.vertical-header-block').outerHeight();
				jQuery('.content-wrapper').css('padding-bottom', footerHeight);
			} else {
				jQuery('.content-wrapper').attr('style', '');
			}
		}
	}

function accordionIcons() {
    if (jQuery(document.body).width() <= 1007) {
        if (!jQuery('.accordion-list .accordion-item .accordion-title .icon-more').length) {
            jQuery('.accordion-list .accordion-item .accordion-title').prepend('<span class="icon-more"><i class="icon-plus fa fa-plus"></i><i class="icon-minus fa fa-minus"></i></span>');
        }
        if (jQuery('#product-details-panel')) {
            if (!jQuery('#product-details-panel .item h4 .icon-more').length) {
                jQuery('#product-details-panel .item h4').prepend('<span class="icon-more"><i class="icon-plus fa fa-plus"></i><i class="icon-minus fa fa-minus"></i></span>');
            }
        }
        if (jQuery('body').hasClass('catalog-product-view')) {
            jQuery('body .main-container').addClass('accordion-list');
        }
    } else {
       jQuery('.accordion-list .accordion-item .accordion-title').find('.icon-more').remove();
       jQuery('.accordion-list').find('.accordion-item.open .accordion-content').removeClass('open');
       jQuery('.accordion-list .accordion-item .accordion-content').each(function() {
        jQuery(this).css('display','');
       });
       if (jQuery('body').hasClass('catalog-product-view')) {
            if (jQuery('.customer-reviews-wrapper').length) {
                reviewsBox();
            }
        }
        if (jQuery('.product-collateral').length) {
            if (jQuery('.panel-group .panel-heading .panel-title .icon-more').length) {
                jQuery('.panel-group .panel-heading .panel-title').find('.icon-more').remove();
            }
        }
        if (jQuery('#product-details-panel').length) {
            jQuery('#product-details-panel .item h4').find('.icon-more').remove();
            jQuery('#product-details-panel').find('.item.open').removeClass('open');
            jQuery('#product-details-panel .item .content').each(function() {
                jQuery(this).css('display','');
            });
        }
    }

    if (jQuery(document.body).width() < 768) {
        if (jQuery('#layered-filter-block').length) {
            jQuery('#layered-filter-block').addClass('mobile');
        }
    } else {
        if (jQuery('#layered-filter-block').length) {
            jQuery('#layered-filter-block').removeClass('mobile');
            jQuery('#layered-filter-block').css('right', '');
        }
    }
}

function accordionToggle() {
    jQuery('.accordion-list').each(function() {
        jQuery('.accordion-title').off().on('click', function(e) {
            if (jQuery(document.body).width() <= 1007) {
                e.preventDefault();
                var $this = jQuery(this);
                if ($this.parent().hasClass('open')) {
                    $this.parent().removeClass('open');
                    $this.next().slideUp(500);
                } else {
                    if (jQuery('body').hasClass('catalog-product-view') && jQuery('#product-details-panel').length) {
                        jQuery('#product-details-panel').find('.item.open .content').slideUp(350);
                        jQuery('#product-details-panel').find('.item.open').removeClass('open');
                    }
                    $this.closest('.accordion-list').find('.accordion-item.open .accordion-content').slideUp(350);
                    $this.closest('.accordion-list').find('.accordion-item.open').removeClass('open');
                    $this.parent().addClass('open');
                    $this.next().slideDown(500);
                    if ($this.parent().parent().hasClass('box-collateral')) {
                        reviewsBox();
                    }
                }
            }
        });
    });
}

function mobileMenuListener(a) {
    var b = a.touches[0];
	console.log(b.target);
    if (jQuery(b.target).parents(".mobile-menu-wrapper").length == 0 && jQuery(b.target).parents(".menu-button").length == 0 && !jQuery(b.target).hasClass("menu-button") && !jQuery(b.target).hasClass('mobile-menu-wrapper')) {
        jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},300);
        jQuery('.mobile-menu-wrapper .mobile-menu-inner').removeClass('open');
        document.removeEventListener("touchstart", mobileMenuListener, false)
    }
}

function mobileMenu() {
	jQuery('.mobile-menu-wrapper .menu-button').off().on('click', function() {
        if (!jQuery('.mobile-menu-wrapper .mobile-menu-inner').hasClass('open')) {
            jQuery('html body').animate({'margin-left' : '300px', 'margin-right' : '-300px'},300);
            jQuery('.mobile-menu-wrapper .mobile-menu-inner').addClass('open');
            if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i))){
                document.addEventListener("touchstart", mobileMenuListener, false);
            } else {
                jQuery(document).on("click", function(f) {
                    if (jQuery(f.target).parents(".mobile-menu-wrapper").length == 0 && jQuery(f.target).parents(".shop-by-button").length == 0 && !jQuery(f.target).hasClass(".shop-by-button") && !jQuery(f.target).hasClass('block-layered-nav')) {
                        jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},500);
                        jQuery('.mobile-menu-wrapper .mobile-menu-inner').removeClass('open');
                        jQuery(document).off("click");
                    }
                })
            }
        } else {
            e.stopPropagation();
            jQuery('.mobile-menu-wrapper .mobile-menu-inner').removeClass('open');
            jQuery('html body').animate({'margin-left' : '0', 'margin-right' : '0'},500);
        }
	});

}

function backgroundWrapper(){
	if(jQuery('.background-wrapper').length){
		jQuery('.background-wrapper').each(function(){
			var thisBg = jQuery(this);
			if(jQuery(document.body).width() < 768){
				thisBg.attr('style', '');
				if(thisBg.parent().hasClass('text-banner') || thisBg.find('.text-banner').length || thisBg.find('.fullwidth-text-banners').length){
					bgHeight = thisBg.parent().outerHeight();
					thisBg.parent().css('height', bgHeight - 2);
				}
				if(jQuery('body').hasClass('boxed-layout')){
					bodyWidth = thisBg.parents('.container').outerWidth();
					bgLeft = (bodyWidth - thisBg.parents('.container').width())/2;
				} else {
					bgLeft = thisBg.parent().offset().left;
					bodyWidth = jQuery(document.body).width();
				}
				if(thisBg.data('bgColor')){
					bgColor = thisBg.data('bgColor');
					thisBg.css('background-color', bgColor);
				}
				setTimeout(function(){
					thisBg.css({
						'position' : 'absolute',
						'left' : -bgLeft,
						'width' : bodyWidth
					}).parent().css('position', 'relative');
				}, 300);
			} else {
				thisBg.attr('style', '');
				if(jQuery('body').hasClass('boxed-layout')){
					bodyWidth = thisBg.parents('.container').outerWidth();
					bgLeft = (bodyWidth - thisBg.parents('.container').width())/2;
				} else {
					bgLeft = thisBg.parent().offset().left;
					bodyWidth = jQuery(document.body).width();
				}
				thisBg.css({
					'position' : 'absolute',
					'left' : -bgLeft,
					'width' : bodyWidth
				}).parent().css('position', 'relative');
				if(thisBg.data('bgColor')){
					bgColor = thisBg.data('bgColor');
					thisBg.css('background-color', bgColor);
				}
				if(thisBg.parent().hasClass('text-banner') || thisBg.find('.text-banner').length || thisBg.find('.fullwidth-text-banners').length){
					bgHeight = thisBg.children().innerHeight();
					thisBg.parent().css('height', bgHeight - 2);
				}
			}
			
			if(thisBg.parents('.parallax-content')){
				jQuery('body').addClass('parallax');
			}
			if(thisBg.parent().hasClass('parallax-banners-wrapper')) {
					jQuery('.parallax-banners-wrapper').each(function(){
						block = jQuery(this).find('.text-banner');
						var wrapper = jQuery(this);
						var fullHeight = 0;
						var imgCount = block.size();
						var currentIndex = 0;
						block.each(function(){
							imgUrl = jQuery(this).css('background-image').replace(/url\(|\)|\"/ig, '');
							if(imgUrl.indexOf('none')==-1){
								img = new Image;
								img.src = imgUrl;
								img.setAttribute("name", jQuery(this).attr('id'));
								img.onload = function(){
									imgName = '#' + jQuery(this).attr('name');
									if(wrapper.data('fullscreen')){
										windowHeight = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
										jQuery(imgName).css({
											'height' : windowHeight+'px',
											'background-size' : 'cover'
										});
										fullHeight += windowHeight;
									} else {
										jQuery(imgName).css('height', this.height+'px');
										jQuery(imgName).css('height', (this.height - 200)+'px');
										fullHeight += this.height - 200;
										// if (pixelRatio > 1) {
											// jQuery(imgName).css('background-size', this.width+'px' + ' ' + this.height+'px');
										// }
									}
									wrapper.css('height', fullHeight);
									currentIndex++;
									if(!jQuery('body').hasClass('mobile-device') && !navigator.userAgent.match(/Android/i)){
										if(currentIndex == imgCount){
											if(jQuery(document.body).width() > 1278) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.7, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.7, false);
											} else if(jQuery(document.body).width() > 977) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.9, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.85, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.9, false);
											} /* else if(jQuery(document.body).width() > 767) {
												jQuery('#parallax-banner-1').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-2').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.8, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.8, false);
											} else {
												jQuery('#parallax-banner-1').parallax("30%", 0.5, true);
												jQuery('#parallax-banner-2').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-3').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-4').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-5').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-6').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-7').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-8').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-9').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-10').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-11').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-12').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-13').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-14').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-15').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-16').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-17').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-18').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-19').parallax("60%", 0.1, false);
												jQuery('#parallax-banner-20').parallax("60%", 0.1, false);
											}*/
										}
									}
								}
							}
							bannerText = jQuery(this).find('.banner-content');
							if(bannerText.data('top')){
								bannerText.css('top', bannerText.data('top'));
							}
							if(bannerText.data('left')){
								if(!bannerText.data('right')){
									bannerText.css({
										'left': bannerText.data('left'),
										'right' : 'auto'
									});
								} else {
									bannerText.css('left', bannerText.data('left'));
								}
							}
							if(bannerText.data('right')){
								if(!bannerText.data('left')){
									bannerText.css({
										'right': bannerText.data('right'),
										'left' : 'auto'
									});
								} else {
									bannerText.css('right', bannerText.data('right'));
								}
							}
						});
					});
					jQuery(window).scroll(function() {
						jQuery('.parallax-banners-wrapper').each(function(){
							block = jQuery(this).find('.text-banner');
							block.each(function(){
								var imagePos = jQuery(this).offset().top;
								var topOfWindow = jQuery(window).scrollTop();
								if (imagePos < topOfWindow+600) {
									jQuery(this).addClass("slideup");
								} else {
									jQuery(this).removeClass("slideup");
								}
							});
						});
					});
					setTimeout(function(){
						jQuery('#parallax-loading').fadeOut(200);
					}, 1000);
				}
				thisBg.animate({'opacity': 1}, 200)
		});
	}
}

var bsModal;

require(['jquery'], function ($)
{
	productTimer = {
        init: function(secondsDiff, id){
            daysHolder = jQuery('.timer-'+id+' .days');
            hoursHolder = jQuery('.timer-'+id+' .hours');
            minutesHolder = jQuery('.timer-'+id+' .minutes');
            secondsHolder = jQuery('.timer-'+id+' .seconds');
            timerId = jQuery('.timer-'+id);
            var firstLoad = true;
            productTimer.timer(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, timerId, firstLoad);
            setTimeout(function(){
                jQuery('.timer-box').css('display', 'block');
            }, 1100);
        },
        timer: function(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, timerId, firstLoad){
            setTimeout(function(){
                days = Math.floor(secondsDiff/86400);
                hours = Math.floor((secondsDiff/3600)%24);
                minutes = Math.floor((secondsDiff/60)%60);
                seconds = secondsDiff%60;

                jQuery(timerId).each(function(){
                   /* if (jQuery(this).closest('.product-items.owl-carousel').length == 1 && jQuery(this).closest('.owl-item.active').length == 1) {
                        currentTimer = jQuery(this);
                        secondsHolder = jQuery(currentTimer).closest('.owl-item.active').find('.timer-box .seconds');
                        secondsActive =  jQuery(secondsHolder).find('.flip-item.active');
                        secondsBefore = jQuery(secondsHolder).find('.flip-item.before');
                        if (seconds > 9) {
                            jQuery(secondsBefore).find('.flip-text').html(seconds);
                        } else {
                            jQuery(secondsBefore).find('.flip-text').html('0'+seconds);
                        }
                        secondsBefore.removeClass('before').addClass('active');
                        secondsActive.removeClass('active').addClass('before');
                    } else if(timerId.closest('.product-items.owl-carousel').length == 0) {
                        secondsHolder = jQuery(this).find('.seconds');
                        secondsActive =  jQuery(secondsHolder).find('.flip-item.active');
                        secondsBefore = jQuery(secondsHolder).find('.flip-item.before');
                        if (seconds > 9) {
                            jQuery(secondsBefore).find('.flip-text').html(seconds);
                        } else {
                            jQuery(secondsBefore).find('.flip-text').html('0'+seconds);
                        }
                        secondsBefore.removeClass('before').addClass('active');
                        secondsActive.removeClass('active').addClass('before');
                    }*/

                    secondsHolder = jQuery(this).find('.seconds');
                    secondsActive =  jQuery(secondsHolder).find('.flip-item.active');
                    secondsBefore = jQuery(secondsHolder).find('.flip-item.before');
                    if (seconds > 9) {
                        jQuery(secondsBefore).find('.flip-text').html(seconds);
                    } else {
                        jQuery(secondsBefore).find('.flip-text').html('0'+seconds);
                    }
                    secondsBefore.off().removeClass('before').addClass('active');
                    secondsActive.off().removeClass('active').addClass('before');
                });

                if(firstLoad == true){
                    if (seconds > 9) {
                        secondsHolder.find('.flip-text').html(seconds);
                    } else {
                        secondsHolder.find('.flip-text').html('0'+seconds);
                    }
                    if (days > 9) {
                        daysHolder.find('.flip-text').html(days);
                    } else {
                        daysHolder.find('.flip-text').html('0'+days);
                    }
                    if (hours > 9) {
                        hoursHolder.find('.flip-text').html(hours);
                    } else {
                        hoursHolder.find('.flip-text').html('0'+hours);
                    }
                    if (minutes > 9) {
                        minutesHolder.find('.flip-text').html(minutes);
                    } else {
                        minutesHolder.find('.flip-text').html('0'+minutes);
                    }
                    firstLoad = false;
                }
                if(seconds >= 59){
                    jQuery(timerId).each(function(){
                        currentTimer = jQuery(this);
                        minutesHolder = currentTimer.find('.minutes');
                        hoursHolder = currentTimer.find('.hours');
                        daysHolder = currentTimer.find('.days');
                        if (parseInt(minutesHolder.find('.flip-item.before .flip-up .flip-text').text()) != minutes) {
                            if (minutes > 9) {
                                minutesHolder.find('.flip-item.before .flip-text').html(minutes);
                            } else {
                                minutesHolder.find('.flip-item.before .flip-text').html('0'+minutes);
                            }
                            minutesActive =  jQuery(minutesHolder).find('.flip-item.active');
                            minutesBefore = jQuery(minutesHolder).find('.flip-item.before');
                            minutesBefore.removeClass('before').addClass('active');
                            minutesActive.removeClass('active').addClass('before');
                        }
                        if (parseInt(hoursHolder.find('.flip-item.before .flip-up .flip-text').text()) != hours) {
                            if (hours > 9) {
                                hoursHolder.find('.flip-item.before .flip-text').html(hours);
                            } else {
                                hoursHolder.find('.flip-item.before .flip-text').html('0'+hours);
                            }
                            hoursActive =  jQuery(hoursHolder).find('.flip-item.active');
                            hoursBefore = jQuery(hoursHolder).find('.flip-item.before');
                            hoursBefore.removeClass('before').addClass('active');
                            hoursActive.removeClass('active').addClass('before');
                        }
                        if (parseInt(daysHolder.find('.flip-item.before .flip-up .flip-text').text()) != days) {
                            if (days > 9) {
                                daysHolder.find('.flip-item.before .flip-text').html(days);
                            } else {
                                daysHolder.find('.flip-item.before .flip-text').html('0'+days);
                            }
                            daysHolder.find('.flip-item.before .flip-text').html(days);
                            daysActive =  jQuery(daysHolder).find('.flip-item.active');
                            daysBefore = jQuery(daysHolder).find('.flip-item.before');
                            daysBefore.removeClass('before').addClass('active');
                            daysActive.removeClass('active').addClass('before');
                        }
                    });
                }

                secondsDiff--;
                productTimer.timer(secondsDiff, daysHolder, hoursHolder, minutesHolder, secondsHolder, timerId, firstLoad);
            }, 1000);
        }
    }
	
	if(jQuery('#gift-options-cart').length) {
		jQuery(window).load(function(){
		});
	}

	
	require(["MeigeeBootstrap", "meigeeCookies"], function(modal, cookie)
	{
		if(jQuery('#popup-block').length){
			// "use strict";
			function popupBlock() {
				jQuery('#popup-block').modal({
					show: true
				});
			}
			subscribeFlag = jQuery.cookie('universalPopupFlag');
			
			
			jQuery('#popup-block .action.subscribe').on('click', function(){
				if(jQuery('#popup-block').find('.mage-error').length == 0 && !jQuery('#subscribecheck').attr('aria-invalid')) {
					jQuery.cookie('universalPopupFlag2', 'true', {
						expires: '30',
						path: '/'
					});
				} else {
					jQuery.removeCookie('universalPopupFlag2');
				}
			});
			
			expires = jQuery('#popup-block').data('expires');
			function subsSetcookie(){
				jQuery.cookie('universalPopup', 'true', {
					expires: ''+expires+'',
					path: '/'
				});
			}
			if(!(subscribeFlag) && !jQuery.cookie('universalPopupFlag2')){
				popupBlock();
			}else{
				jQuery.removeCookie('universalPopupFlag', { path: '/' });
				subsSetcookie();
			}
			jQuery('#popup-block').parents('body').css({
				'padding' : 0,
				'overflow' : 'visible'
			});
			jQuery('#popup-block .popup-bottom input').on('click', function(){
				if(jQuery(this).parent().find('input:checked').length){
					subsSetcookie();
				} else {
					jQuery.removeCookie('universalPopup', { path: '/' });
				}
			});
			setTimeout(function(){
				jQuery('#popup-block button.close').on('click', function(){
					jQuery.cookie('universalPopup', 'true');
				});
			}, 1000);
			
			if((jQuery('#popup-block .popup-content-wrapper').data('bgimg')) && (jQuery('#popup-block .popup-content-wrapper').data('bgcolor'))) {
				var bgImg = jQuery('#popup-block .popup-content-wrapper').data('bgimg');
				var bgColor = jQuery('#popup-block .popup-content-wrapper').data('bgcolor');
				jQuery('#popup-block .popup-content-wrapper').attr('style', bgImg + bgColor);
			}else{
				if(jQuery('#popup-block .popup-content-wrapper').data('bgimg')){
					var bgImg = jQuery('#popup-block .popup-content-wrapper').data('bgimg');
					jQuery('#popup-block .popup-content-wrapper').attr('style', bgImg);
				}
				if(jQuery('#popup-block .popup-content-wrapper').data('bgcolor')){
					jQuery('#popup-block .popup-content-wrapper').addClass('no-bgimg');
					var bgColor = jQuery('#popup-block .popup-content-wrapper').data('bgcolor');
					jQuery('#popup-block .popup-content-wrapper').attr('style', bgColor);
				}
			}
		}
	});
	
	require(['MeigeeBootstrap', 'MeigeeCarousel'], function(mb,mc)
    {
       bsModal = $.fn.modal.noConflict();

		jQuery(document).ready(function(){
			customHomeSlider();
			shopByClick();
			verticalHeader();
			accordionIcons();
			accordionToggle();
			mobileMenu();
			footerSmallPage();
			header24Logo();
			header24Logoswitcher();
			/* Mobile Devices */
			if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))){
			/* Mobile Devices Class */
			jQuery('body').addClass('mobile-device');
				var mobileDevice = true;
			}else if(!navigator.userAgent.match(/Android/i)){
				var mobileDevice = false;
			}

			/* Responsive */
			var responsiveflag = false;
			var topSelectFlag = false;
			var menu_type = jQuery('#nav').attr('class');

			jQuery('.language-currency-block').on('click', function(){
				jQuery('.language-currency-block').toggleClass('open');
				jQuery('.language-currency-dropdown').slideToggle();
			});




			jQuery('#sticky-header .search-button').on('click', function(){
				jQuery(this).toggleClass('active');
				jQuery('#sticky-header .block-search form.minisearch').slideToggle();
			});
			jQuery('.page-header.header-7 .block-search .block-title, .page-header.header-2 .block-search .block-title').on('click', function(){
				jQuery(this).toggleClass('active');
				jQuery(this).next().slideToggle();
			});


			var isApple = false;
		/* apple position fixed fix */
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))){
			isApple = true;
			function stickyPosition(clear){
				items = jQuery('.header, .backstretch');
				if(clear == false){
					topIndent = jQuery(window).scrollTop();
					items.css({
						'position': 'absolute',
						'top': topIndent
					});
				}else{
					items.css({
						'position': 'fixed',
						'top': '0'
					});
				}
			}
			jQuery('#sticky-header .form-search input').on('focusin focusout', function(){
				jQuery(this).toggleClass('focus');
				if(jQuery('header.header').hasClass('floating')){
					if(jQuery(this).hasClass('focus')){
						setTimeout(function(){
							stickyPosition(false);
						}, 500);
					}else{
						stickyPosition(true);
					}
				}
			});
		}

		if(jQuery('.footer-links-button').length){
			jQuery('.footer-links-button').on('click', function(){
				jQuery(this).toggleClass('active').parent().find('ul').slideToggle(300);
			});
		}
		if(jQuery('.vertical-header-block .mobile-button').length){
			jQuery('.vertical-header-block .mobile-button').click(function(){
				if(jQuery('.vertical-header-block .indent').hasClass('active')){
					jQuery(this).prev('.indent').removeClass('active').animate({
						'opacity' : 0,
						'z-index' : '-1',
						'height' : '0'
					});
				} else {
					jQuery(this).prev('.indent').addClass('active').animate({
						'opacity' : 1,
						'z-index' : '999',
						'height' : '100%'
					});
				}
			});

		}

		function header24Logo() {
			if(jQuery('.page-header.header-24').length){
			    var logo = jQuery('.logo-wrapper');
			    logoClone = logo.clone();
			    var logoClone = logoClone.wrap("<li class='item-logo level-top'></li>");
			    var position = jQuery(".page-header.header-24 #megamenu .navbar-collapse.collapse li.level-top").length-1;
			    console.log(jQuery(".page-header.header-24 #megamenu .navbar-collapse.collapse li.level-top").length);
			    var i = 0;
			    jQuery('.page-header.header-24 #megamenu .navbar-collapse.collapse li.level-top').each(function() {
			        if(i == Math.floor(position/2)) {
			            jQuery(this).after(logoClone.parent());
			        }
			        i++;
			    });
			}
		}
		function header24Logoswitcher (){
			if(jQuery('.page-header.header-24').length){
			    var logo = jQuery('.logo-wrapper');
			    var logoMenu = jQuery('.topmenu .logo-wrapper');
			    if(jQuery(document.body).width() > 1009){
			    	logo.hide();
			    	logoMenu.show().parents('div.topmenu').show().animate({'opacity': 1}, 800);
			    } else {
			    	logo.show().animate({'opacity': 1}, 800);
			    	logoMenu.hide();
			    }
			}
		}


		if(jQuery('.products-grid .btn.btn-details').length){
			jQuery('.products-grid .btn.btn-details').click(function(){ 
				jQuery(this).closest('.item').addClass('hover');
			});
		}

		if(jQuery('.page-header.header-27').length){
			if(jQuery(document.body).width() > 1007 && jQuery(document.body).width() < 1331){
				jQuery('.page-header.header-27 .action.nav-toggle').click(function(){
					jQuery(this).next().toggleClass('active').slideToggle();
				});
			}
		}


			/* sticky header */
			if(jQuery('#sticky-header').length){
				var headerHeight = jQuery('.page-header').height();
				sticky = jQuery('#sticky-header');
				jQuery(window).on('scroll', function(){
					if(jQuery(document.body).width() > 977){
						if(!isApple){
							heightParam = headerHeight;
						}else{
							heightParam = headerHeight*2;
						}
						if(jQuery(this).scrollTop() >= heightParam){
							sticky.stop().slideDown(250);
						}
						if(jQuery(this).scrollTop() < headerHeight ){
							sticky.stop().hide();
						}
						//

					} 
					// else {
						// jQuery('#sticky-header').appendTo('html');
					// }
				});
			}
			pageNotFound();
			accordionNav();
			
			jQuery(window).load(function(){
				backgroundWrapper();
			});
			jQuery(window).resize(function(){
				pageNotFound();
				accordionNav();
				verticalHeader();
				backgroundWrapper();
				accordionToggle();
                accordionIcons();
                footerSmallPage();
                header24Logoswitcher();
			});

			if(document.URL.indexOf("#product_tabs_reviews") != -1) {
				$('#tabs a[href="#product_tabs_reviews"]').tab('show')
			}
			$.fn.scrollTo = function (speed) {
				if (typeof(speed) === 'undefined')
					speed = 1000;
				$('html, body').animate({
					// scrollTop: parseInt($(this).offset().top)
					scrollTop: parseInt($('#tabs').offset().top - 100)
				}, speed);
			};
			$('.product-info-main .product-reviews-summary a.action').on('click', function(){
				$(this).scrollTo('#tabs');
				$('#tabs a[href="#product_tabs_reviews"]').tab('show');
			});


		});
	});

    require(['jquery/ui', 'MeigeeBootstrap', 'lightBox'], function(ui, lb)
    {
        // $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event)
        // {
            // event.preventDefault();
            // $(this).ekkoLightbox();
            // return false;
        // });
    });


});










