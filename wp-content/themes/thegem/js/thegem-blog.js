(function($) {

	function blog_images_loaded($box, image_selector, callback) {
		function check_image_loaded(img) {
			return img.complete && img.naturalWidth !== undefined && img.naturalWidth != 0;
		}

		var $images = $(image_selector, $box).filter(function() {
				return !check_image_loaded(this);
			}),
			images_count = $images.length;

		if (images_count == 0) {
			return callback();
		}

		if (window.gemBrowser.name == 'ie' && !isNaN(parseInt(window.gemBrowser.version)) && parseInt(window.gemBrowser.version) <= 10) {
			function image_load_event() {
				images_count--;
				if (images_count == 0) {
					callback();
				}
			}

			$images.each(function() {
				if (check_image_loaded(this)) {
					return;
				}

				var proxyImage = new Image();
				proxyImage.addEventListener( 'load', image_load_event );
				proxyImage.addEventListener( 'error', image_load_event );
				proxyImage.src = this.src;
			});
			return;
		}

		$images.on('load error', function() {
			images_count--;
			if (images_count == 0) {
				callback();
			}
		});
	}

	function init_blog_scroll_next_page($blog, $pagination) {
		if (!$pagination.length) {
			return false;
		}

		var watcher = scrollMonitor.create($pagination[0]);
		watcher.enterViewport(function() {
			blog_load_more_request($blog, $pagination, true);
		});
	}

	function initBlogMasonryTimeline() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
				initBlogMasonryTimeline.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $blog = $(this),
			isTimeline = $blog.hasClass('blog-style-timeline_new');

		if (isTimeline && $blog.closest('.vc_row[data-vc-stretch-content="true"]').length > 0) {
			$('.post-image img.img-responsive', $blog).removeAttr('srcset');
		}

		blog_images_loaded($blog, 'article img', function() {
			$blog.prev('.preloader').remove();

			var itemsAnimations = $blog.itemsAnimations({
				itemSelector: 'article',
				scrollMonitor: true,
				firstItemStatic: isTimeline
			});

			var init_blog = true;
			if (isTimeline) {
				$blog
					.on('layoutComplete', function(event, laidOutItems) {
						laidOutItems.forEach(function(item) {
							if (item.position.x == 0) {
								$(item.element).removeClass('right-position').addClass('left-position');
							} else {
								$(item.element).removeClass('left-position').addClass('right-position');
							}
						});
					});
			}

			$blog
				.on( 'arrangeComplete', function( event, filteredItems ) {
					$blog.buildSimpleGalleries();
					$blog.updateSimpleGalleries();

					if (init_blog) {
						init_blog = false;
						itemsAnimations.show();
					}
				})
				.isotope({
					itemSelector: 'article',
					layoutMode: 'masonry',
					masonry: {
						columnWidth: 'article:not(.sticky)'
					},
					transitionDuration: 0
				});
		});

		var $blogParent = $blog;
		if (isTimeline) {
			$blogParent = $blog.parent();
		}
		$blogParent.siblings('.blog-load-more').on('click', function() {
			blog_load_more_request($blog, $(this), false);
		});

		init_blog_scroll_next_page($blog, $blogParent.siblings('.blog-scroll-pagination'));

		$blog.buildSimpleGalleries();
		$blog.updateSimpleGalleries();
	}

	function initBlogDefault() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function(node) {
				initBlogDefault.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $blog = $(this);

		$('.blog-load-more', $blog.parent()).on('click', function() {
			blog_load_more_request($blog, $(this), false);
		});

		init_blog_scroll_next_page($blog, $blog.siblings('.blog-scroll-pagination'));

		var itemsAnimations = $blog.itemsAnimations({
			itemSelector: 'article',
			scrollMonitor: true
		});
		itemsAnimations.show();

		blog_images_loaded($blog, 'article img', function() {
			if ($blog.hasClass('blog-style-justified-2x') || $blog.hasClass('blog-style-justified-3x') || $blog.hasClass('blog-style-justified-4x')) {
				oneSizeArticles($blog);
			}

			$blog.buildSimpleGalleries();
			$blog.updateSimpleGalleries();
		});
	}

	$('.blog-style-masonry, .blog-style-timeline_new').each(initBlogMasonryTimeline);

	$('.blog:not(body,.blog-style-timeline_new,.blog-style-masonry)').each(initBlogDefault);

	function oneSizeArticles($blog){
		var elements = {};
		$("article", $blog).css('height', '');
		$("article", $blog).each(function(i, e){
			var transform = $(this).css('transform');
			var translateY = 0;

			if (transform != undefined && transform != 'none') {
				translateY = parseFloat(transform.substr(1, transform.length - 2).split(',')[5]);
				if (isNaN(translateY)) {
					translateY = 0;
				}
			}

			var elPosition = parseInt($(this).position().top - translateY);
			var elHeight = $(this).height();

			if(elements[elPosition] == undefined){
				elements[elPosition] = {'array':[$(this)], 'maxHeight':elHeight};
			} else {
				elements[elPosition]['array'].push($(this));
				if(elements[elPosition]['maxHeight'] < elHeight){
					elements[elPosition]['maxHeight'] = elHeight;
				}
			}
		});
		$.each(elements, function(i, e){
			var item = this;
			$.each(item.array, function(){
				$(this).height(item.maxHeight);
			});
		});
	}

	$(window).on('resize', function(){
		$(".blog-style-justified-2x, .blog-style-justified-3x, .blog-style-justified-4x").each(function(){
			oneSizeArticles($(this));
		});
	});

	function finishAjaxRequestActions($blog, $inserted_data, is_scroll, $pagination, next_page, $loading_marker) {
		$inserted_data.buildSimpleGalleries();
		$inserted_data.updateSimpleGalleries();
		window.wp.mediaelement.initialize();
		$blog.itemsAnimations('instance').show($inserted_data);

		if ($blog.hasClass('blog-style-justified-2x') || $blog.hasClass('blog-style-justified-3x') || $blog.hasClass('blog-style-justified-4x')) {
			blog_images_loaded($blog, 'article img', function() {
				oneSizeArticles($blog);
			});
		}

		if (is_scroll) {
			$pagination.removeClass('active').html('');
		} else {
			$loading_marker.remove();
			if (next_page == 0) {
				$pagination.hide();
			}
		}
		$blog
			.data('request-process', false)
			.data('next-page', next_page);
	}

	function blog_load_more_request($blog, $pagination, is_scroll) {
		var data = thegem_blog_ajax;

		var is_processing_request = $blog.data('request-process') || false;
		if (is_processing_request) {
			return false;
		}

		var paged = $blog.data('next-page');
		if (paged == null || paged == undefined) {
			paged = 1;
		}
		if (paged == 0) {
			return false;
		}

		data['data']['paged'] = paged;
		data['action'] = 'blog_load_more';
		$blog.data('request-process', true);
		if (is_scroll) {
			$pagination.addClass('active').html('<div class="loading"><div class="preloader-spin"></div></div>');
		} else {
			var $loading_marker = $('<div class="loading"><div class="preloader-spin"></div></div>');
			$('.gem-button-container', $pagination).before($loading_marker);
		}

		$.ajax({
			type: 'post',
			dataType: 'json',
			url: thegem_blog_ajax.url,
			data: data,
			success: function(response) {
				if (response.status == 'success') {
					var $newItems = $(response.html),
						$inserted_data = $($newItems.html()),
						current_page = $newItems.data('page'),
						next_page = $newItems.data('next-page');

					if ($blog.hasClass('blog-style-masonry') || $blog.hasClass('blog-style-timeline_new')) {
						blog_images_loaded($inserted_data, 'article img', function() {
							$blog.isotope('insert', $inserted_data);
							finishAjaxRequestActions($blog, $inserted_data, is_scroll, $pagination, next_page, $loading_marker);
						});
					} else {
						$blog.append($inserted_data);
						finishAjaxRequestActions($blog, $inserted_data, is_scroll, $pagination, next_page, $loading_marker);
					}
					$blog.initBlogFancybox();
				} else {
					alert(response.message);
				}
			}
		});
	}

})(jQuery);
