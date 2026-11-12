jQuery(document).ready(function($) {
    // Text Image Scroller

    if ($(".text-image-scroller-container").length) {
        // Desktop 
        if ($(window).width() > 1200) {
            $(window).scroll(function() {
                $(".text-image-scroller-container .image-holder").each(function() {
                    var $this = $(this);
                    var gifSrc = $this.find('.image-wrap img').data('src');
                    if (!gifSrc) return true; // Skip if data-src is not found
                    
                    gifSrc = gifSrc.replace('.png', '.gif');
                    
                    if (
                        $this.offset().top - ($(window).height() * 0.5) <= $(window).scrollTop() && 
                        $this.offset().top + $this.height() - ($(window).height() * 0.5) > $(window).scrollTop()
                    ) {
                        if ($this.hasClass('active')) {
                            return true;
                        }
                        
                        // Cache selectors to improve performance
                        var $imageHolders = $(".text-image-scroller-container .image-holder");
                        var $items = $(".text-image-scroller-container .items .item");
                        
                        // First, update image sources before manipulating classes
                        $imageHolders.find('.image-wrap img').attr('src', function() {
                            return $(this).data('src') || '';
                        });
                        
                        $imageHolders.removeClass('active active-load');
                        $this.addClass('active');
                        //$this.find('.image-wrap img').attr('src', gifSrc);
                        
                        $items.removeClass('active active-load');
                        
                        // Perform animations with the correct element check
                        var $itemDesc = $items.find('.item-desc');
                        if ($itemDesc.length) {
                            $itemDesc.stop().slideUp(150);
                        }
                        
                        var $activeItem = $items.eq($this.index());
                        if ($activeItem.length) {
                            $activeItem.addClass('active');
                            var $activeItemDesc = $activeItem.find('.item-desc');
                            if ($activeItemDesc.length) {
                                $activeItemDesc.stop().slideDown(150);
                            }
                        }
                    }
                });
            });
            
            $("body").on('click', '.text-image-scroller-container .items .item h3 a', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $parent = $this.parents('.item');
                
                if (!$parent.length) return;
                
                var $index = $parent.index() + 1;
                var $target = $(".text-image-scroller-container .image-holder:nth-child(" + $index + ")");
                var $header = $("#header");
                
                if ($target.length && $target.offset()) {
                    var headerHeight = $header.length ? $header.outerHeight(true) : 0;
                    
                    $('html, body').animate({
                        scrollTop: $target.offset().top - headerHeight
                    }, 500);
                }
            });
        } else {
            // Mobile
            $(window).scroll(function() {
                $(".text-image-scroller-container .item").each(function() {
                    var $this = $(this);
                    var $img = $this.find('img');
                    
                    if (!$img.length || !$img.data('src')) return true;
                    
                  
                    
                    if (
                        $this.offset().top - ($(window).height() * 0.35) <= $(window).scrollTop() && 
                        $this.offset().top + $this.height() - ($(window).height() * 0.35) > $(window).scrollTop()
                    ) {
                        if ($this.hasClass('active')) {
                            return true;
                        }
                        
                        var $allImages = $(".text-image-scroller-container .item  img");
                        $allImages.each(function() {
                            var $image = $(this);
                            if ($image.data('src')) {
                                $image.attr('src', $image.data('src'));
                            }
                        });
                        
                        $(".text-image-scroller-container .item").removeClass('active active-load');
                        $this.addClass('active');

                    }
                });
            });
        }
    }
    
    // Add window resize handler to handle viewport changes without page refresh
    $(window).on('resize', function() {
        if ($(".text-image-scroller-container").length) {
            // Force reload the correct version (mobile or desktop) on window resize
            //location.reload();
        }
    });
    
   
  
    
});




jQuery(window).on('elementor/frontend/init', function () {
  elementorFrontend.hooks.addAction(
    'frontend/element_ready/wpresidence_testimonial_carousel.default',
    function ($scope, $) {

      new Swiper($scope.find('.wpresidence-testimonial-swiper')[0], {
        // default (mobile-first): 1 slide
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,

        // at widths >= 600px, show 2 slides
        breakpoints: {
          600: {
            slidesPerView: 1
          },
          // at widths >= 1000px, show 3 slides
          1000: {
            slidesPerView: 3
          }
        },

        pagination: {
          el: $scope.find('.swiper-pagination')[0],
          clickable: true,
        },
        navigation: {
          nextEl: $scope.find('.swiper-button-next')[0],
          prevEl: $scope.find('.swiper-button-prev')[0],
        },
      });
    }
  );
});







jQuery(window).on('elementor/frontend/init', function () {
  elementorFrontend.hooks.addAction('frontend/element_ready/wpresidence_demo_showcase.default', function ($scope, $) {

    
    // Initialize content slider
    const contentSwiper = new Swiper($scope.find('.demo-content-swiper')[0], {
      slidesPerView: 1,
      spaceBetween: 30,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      navigation: {
        nextEl: $scope.find('.swiper-button-next')[0],
        prevEl: $scope.find('.swiper-button-prev')[0],
      },
      on: {
        slideChange: function() {
          // Update active thumbnail
          const activeIndex = this.activeIndex;
          const $thumbs = $scope.find('.demo-thumb');
          $thumbs.removeClass('active');
          $thumbs.filter('[data-index="' + activeIndex + '"]').addClass('active');
          
          // Lazy load the current slide's image
          const $currentSlide = $(this.slides[activeIndex]);
          const $lazyImage = $currentSlide.find('img.lazy');
          
          if ($lazyImage.length > 0 && $lazyImage.attr('data-src')) {
            $lazyImage.attr('src', $lazyImage.attr('data-src'));
            $lazyImage.removeClass('lazy');
            $lazyImage.removeAttr('data-src');
          }
        },
        init: function() {
          // Set the first thumbnail as active initially
          const $thumbs = $scope.find('.demo-thumb');
          $thumbs.filter('[data-index="0"]').addClass('active');
        }
      }
    });
    
    // Handle thumbnail clicks
    $scope.find('.demo-thumb').on('click', function() {
      const clickedIndex = parseInt($(this).data('index'), 10);
      contentSwiper.slideTo(clickedIndex);
    });
  });
});





// Also initialize on document.ready for non-Elementor contexts
jQuery(document).ready(function($) {
  if ($('.wpresidence-demo-showcase').length > 0 && typeof Swiper !== 'undefined') {
    $('.wpresidence-demo-showcase').each(function() {
      const $showcase = $(this);
      
      // Initialize content slider
      const contentSwiper = new Swiper($showcase.find('.demo-content-swiper')[0], {
        slidesPerView: 1,
        spaceBetween: 30,
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },
        navigation: {
          nextEl: $showcase.find('.swiper-button-next')[0],
          prevEl: $showcase.find('.swiper-button-prev')[0],
        },
        on: {
          slideChange: function() {
            // Update active thumbnail
            const activeIndex = this.activeIndex;
            const $thumbs = $showcase.find('.demo-thumb');
            $thumbs.removeClass('active');
            $thumbs.filter('[data-index="' + activeIndex + '"]').addClass('active');
            
            // Lazy load the current slide's image
            const $currentSlide = $(this.slides[activeIndex]);
            const $lazyImage = $currentSlide.find('img.lazy');
            
            if ($lazyImage.length > 0 && $lazyImage.attr('data-src')) {
              $lazyImage.attr('src', $lazyImage.attr('data-src'));
              $lazyImage.removeClass('lazy');
              $lazyImage.removeAttr('data-src');
            }
          },
          init: function() {
            // Set the first thumbnail as active initially
            const $thumbs = $showcase.find('.demo-thumb');
            $thumbs.filter('[data-index="0"]').addClass('active');
          }
        }
      });
      
      // Handle thumbnail clicks
      $showcase.find('.demo-thumb').on('click', function() {
        const clickedIndex = parseInt($(this).data('index'), 10);
        contentSwiper.slideTo(clickedIndex);
      });
    });
  }
});



jQuery(document).ready(function($) {
    // Initialize MixItUp on the container
    // var mixer = mixitup('.wpresidence-demo-showcase .container');
    
    // Fix for the button click handlers (optional, but recommended)
    $('.wpresidence-demo-showcase .controls button').on('click', function() {
        // Remove active class from all buttons
        $('.wpresidence-demo-showcase .controls button').removeClass('active');
        // Add active class to clicked button
        $(this).addClass('active');
    });
});