'use strict';

((jQuery) => {

    // Ensure the code runs only when Elementor and ElementorCommon are available.
    if (typeof elementor === 'undefined' || typeof elementorCommon === 'undefined') {
        return;
    }

    // When the Elementor preview is loaded
    elementor.on('preview:loaded', () => {
         
        
        let dialog = null;

        // Select the add section button template in Elementor
        const buttons = jQuery('#tmpl-elementor-add-section');

        // Modify the button text to include the custom modal trigger button
        const text = buttons.text().replace(
            '<div class="elementor-add-section-drag-title',
            '<div class="elementor-add-section-area-button wpestate-library-open-modal" title="Wpresidence Templates"></div><div class="elementor-add-section-drag-title'
        );

        buttons.text(text);

        // Event listener for opening the custom modal
        jQuery(elementor.$previewContents[0].body).on('click', '.wpestate-library-open-modal', () => {
            if (dialog) {
                dialog.show();
                return;
            }
            displayElementorLoading();
            const modalOptions = {
                id: 'wpestate-library-modal',
                headerMessage: jQuery('#wpestate-library-modal-header').html(),
                message: jQuery('#wpestate-elementor-load-modal').html(),
                className: 'elementor-templates-modal',
                closeButton: true,
                draggable: false,
                hide: {
                    onOutsideClick: true,
                    onEscKeyPress: true
                },
                position: {
                    my: 'center',
                    at: 'center'
                }
            };
            dialog = elementorCommon.dialogsManager.createWidget('lightbox', modalOptions);
            dialog.show();

            loadTemplates();
            hideElementorLoading();
        });

        // Load templates from the server
        function loadTemplates() {
            displayElementorLoading();

            jQuery.ajax({
                type: 'GET',
                url: 'https://kb9p5wan62.execute-api.us-east-1.amazonaws.com/live/wpresidence-studio-files/all.json',
                data: {},
                success: function (data) {
            
                    if (data && data.elements) {
                        var itemTemplate = wp.template('wpestate-studio-modal-item');
                        var itemOrderTemplate = wp.template('wpestate-studio-modal-order');

                        jQuery(itemTemplate(data)).appendTo(jQuery('#wpestate-library-modal #elementor-template-library-templates-container'));
                        jQuery(itemOrderTemplate(data)).appendTo(jQuery('#wpestate-library-modal #elementor-template-library-filter-toolbar-remote'));

                        wpestateLoadTemplate();
                        hideElementorLoading();
                    } else {
                        wpestateDisplayErr('We cannot read from server.');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
                }
            }); // end ajax
        }

        // Display the Elementor loading spinner
        function displayElementorLoading() {
            jQuery('#wpestate-library-modal #elementor-template-library-templates').hide();
            jQuery('#wpestate-library-modal .elementor-loader-wrapper').show();
        }

        // Hide the Elementor loading spinner
        function hideElementorLoading() {
            jQuery('#wpestate-library-modal #elementor-template-library-templates').show();
            jQuery('#wpestate-library-modal .elementor-loader-wrapper').hide();
        }

        // Display error message in the modal
        function wpestateDisplayErr(message) {
            jQuery(`<div class="wpestate-notice wpestate-error">${message}</div>`).appendTo(jQuery('#wpestate-library-modal #elementor-template-library-templates-container'));
            hideElementorLoading();
        }

        // Toggle the update buttons in Elementor editor
        function activateUpdateButton() {
            jQuery('#elementor-panel-saver-button-publish').toggleClass('elementor-disabled');
            jQuery('#elementor-panel-saver-button-save-options').toggleClass('elementor-disabled');
        }

        // Load the selected template into the Elementor editor
        function wpestateLoadTemplate() {
            jQuery('#wpestate-library-modal').on('click', '.elementor-template-library-template-insert', function () {
                

                var insertButton=jQuery(this);
                var parent = insertButton.closest('.elementor-template-library-template');
                var slug = parent.attr('data-slug');
        
                displayElementorLoading();

                jQuery.ajax({
                    type: 'GET',
                    url: 'https://kb9p5wan62.execute-api.us-east-1.amazonaws.com/live/wpresidence-studio-files/'+slug+'.json',
                    data: {},
                    success: function (data) {
                
                        if (data && data.content) {
                            detectAndDownloadImages(data.content, function (updatedContent) {
                    
                                    try {

                                        elementor.getPreviewView().addChildModel(updatedContent);
                                      
                                        dialog.hide();
                                        setTimeout(hideElementorLoading, 2000);
                                        activateUpdateButton();




                                    } catch (error) {
                                        console.error('Error applying template:', error);
                                        wpestateDisplayErr('Error applying template. Please try again.');
                                        hideElementorLoading();
                                    }
                                });
                        } else {
                            console.error('Invalid data received from server');
                            wpestateDisplayErr('We cannot read from server. Invalid data received.');
                            hideElementorLoading();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Handle AJAX errors
                    }
                }); // end ajax
            });
            
  
            
    
    



            function detectAndDownloadImages(content, callback) {
                    var imageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];
                    var imageUrls = [];

                    // Recursive function to search for image URLs in the content
                    function findImages(obj) {
                        if (obj && typeof obj === 'object') {
                            for (var key in obj) {
                                if (obj.hasOwnProperty(key)) {
                                    var value = obj[key];
                                    if (typeof value === 'string' && imageExtensions.some(ext => value.toLowerCase().endsWith('.' + ext))) {
                                        imageUrls.push(value);
                                    } else if (typeof value === 'object') {
                                        findImages(value);
                                    }
                                }
                            }
                        }
                    }

                    // Start searching for images in the content
                    findImages(content);

                    // If no images found, call callback immediately
                    if (imageUrls.length === 0) {
                        callback(content);
                        return;
                    }

                    // Send AJAX request to download images and replace URLs
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl, // Provided by WordPress to reference the admin-ajax.php handler
                        data: {
                            action: 'wpestate_sideload_images',
                            images: imageUrls,
                            nonce: wpestate_ajax_object.nonce // Include nonce for verification
                        },
                        success: function (response) {
                            if (response.success) {
                                var updatedImages = response.data;
                     
                                // Recursive function to replace image URLs in the content
                                function replaceImages(obj) {
                                         
                                        
                                    if (obj && typeof obj === 'object') {
                                        
                                      
                                    
                                        
                                        for (var key in obj) {
                                            if (obj.hasOwnProperty(key)) {
                                                var value = obj[key];
                          
                                                       
                                                if (typeof value === 'string' && updatedImages[value]) {
                                                    obj[key] = updatedImages[value].url;
                                                    if(key === 'url'){
                                                       
                                                        obj['url'] = updatedImages[value].url;
                                                        obj['id'] = updatedImages[value].id;
                                                    
                                                    }
                                                    
                                                } else if (key === 'background_image' && value && value.url && updatedImages[value.url]) {
                                                    obj[key] = {
                                                        id: updatedImages[value.url].id,
                                                        url: updatedImages[value.url].url,
                                                        alt: value.alt || '',
                                                        source: value.source || 'library',
                                                        size: value.size || ''
                                                    };
                                                } else if (typeof value === 'object') {
                                                    replaceImages(value);
                                                }
                                                
                                              
                                            }
                                        }
                                    }
                                }


                                // Start replacing image URLs in the content
                                replaceImages(content);
                                callback(content);
                            } else {
                                console.error('Error downloading images:', response.data);
                                callback(content); // Proceed with the original content if error occurs
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error:', textStatus, errorThrown);
                            callback(content); // Proceed with the original content if AJAX error occurs
                        }
                    });
                }



       
            // Close the modal when the close button is clicked
            jQuery('#wpestate-library-modal').on('click', '.elementor-templates-modal__header__close', () => {
                dialog.hide();
                hideLoader();
            });

            // Filter templates based on search input
     
            jQuery('#wpestate-library-modal #elementor-template-library-filter-text').on('keyup', function () {
			
                const searchValue = String( jQuery(this).val().toLowerCase());
                const activeTab = jQuery('#elementor-wpestate-library-header-menu .elementor-active').data('tab');


                jQuery('#wpestate-library-modal .elementor-template-library-template').each(function () {
                    const $this = jQuery(this);
                    const name = String( $this.data('name').toLowerCase() );                
                    if (name.includes(searchValue) ) {
                        $this.show();         
                    } else {
                        $this.hide();
                    }
                });
            });

            // Filter templates based on dropdown selection
            jQuery('#wpestate-library-modal').on('change', '#elementor-template-library-filter-subtype', function () {
                const val = jQuery(this).val();
            
                jQuery('#wpestate-library-modal .elementor-template-library-template-block').each(function () {
                    const slug = jQuery(this).data('slug').toLowerCase();
                 
                    if (slug.includes(val) || val === 'all') {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                });
            });

            // Set active tab for template filtering
            jQuery('#wpestate-library-modal').on('click', '.elementor-template-library-menu-item', function () {
                setActiveTab(jQuery(this).data('tab'));
            });

            // Update the active tab and filter visibility
            function setActiveTab(tab) {
                jQuery('#wpestate-library-modal .elementor-template-library-menu-item').removeClass('elementor-active');
                jQuery(`#wpestate-tab-${tab}`).addClass('elementor-active');

                document.querySelectorAll('#wpestate-library-modal .elementor-template-library-template').forEach((e) => {
                    const type = e.getAttribute('data-type');
                    e.style.display = type === tab ? 'block' : 'none';

                    if (tab === 'template') {
                        jQuery('#elementor-template-library-filter').hide();
                    } else {
                        jQuery('#elementor-template-library-filter').show();
                    }
                });
            }

            // Initialize the active tab to 'block'
            setActiveTab('block');
        }
    });
    
    
    
    
  
    
})(jQuery);