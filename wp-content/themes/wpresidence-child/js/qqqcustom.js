// jQuery(document).ready(function($) {
//     $('#front_submit_next_step').off('click.customValidation').on('click.customValidation', function(e) {
//         e.preventDefault();
//         e.stopImmediatePropagation(); // Block theme's default behavior

//         let isValid = true;

//         // Remove previous errors
//         $('.inline-error').remove();

//         // Show error
//         function showError(input, message) {
//             isValid = false;
//             const error = $('<div class="inline-error" style="color: red; margin-bottom: 5px;">' + message + '</div>');
//             input.before(error);
//         }

//         // Fields
//         const address = $('#property_address');
//         const city = $('#property_city_submit');
//         const zip = $('#property_zip');
//         const country = $('#property_country');

//         // Validations
//         if ($.trim(address.val()) === '') {
//             showError(address, 'Address is required.');
//         }

//         if ($.trim(city.val()) === '') {
//             showError(city, 'City is required.');
//         }

//         if ($.trim(zip.val()) === '') {
//             showError(zip, 'Zip code is required.');
//         }

//         if (!country.val()) {
//             showError(country, 'Please select a country.');
//         }

//         // If valid, go to next step manually using theme's tab click
//         if (isValid) {
//             $('.submit_tab .active').removeClass('active').next().addClass('active'); // Switch tab
//             $('.submit_step_form').hide(); // Hide current tab content
//             $('.submit_step_form').eq($('.submit_tab li.active').index()).show(); // Show next tab content
//         }
//     });
// });

function clearAllAuthForms() {
    var $forms = jQuery('.wpestate_register_form_wrapper, .wpestate_login_form_wrapper, .wpestate_forgot_form_wrapper');

    // Clear all text-based inputs and textareas
    $forms.find('input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea').val('');

    // Reset all select dropdowns
    $forms.find('select').prop('selectedIndex', 0);

    // Uncheck all checkboxes and radio buttons
    $forms.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

    // Clear any message areas
    $forms.find('.wpestate_register_message_area, .wpestate_login_message_area, .wpestate_forgot_message_area').empty();

    // Reset CAPTCHA if loaded
    if (typeof Recaptcha !== 'undefined' && typeof Recaptcha.reload === 'function') {
        Recaptcha.reload();
    }
}

// Attach to switch links
jQuery(document).on('click', '.wpestate_login_form_switch_register, .wpestate_login_form_switch_forgot, .wpestate_login_form_switch_login', function(e) {
    e.preventDefault();
    clearAllAuthForms();
});

jQuery(document).ready(function($) {
    const fieldsToHide = [
        'property-year', 
        'exterior-material', 
        'property-roofing', 
        'energy_class', 
        'energy_index', 
        'co2_class', 
        'co2_index', 
        'renew_energy_index', 
        'building_energy_index',
        'structure-type', 
        'mls'
    ];

    // Listing page Details hide and gap remove
    fieldsToHide.forEach(function(id) {
        const $field = $('#' + id);
        const $label = $('label[for="' + id + '"]');

        // Find the common parent of label and field (like .form-group or .row)
        const $wrapper = $field.closest('div').has($label);

        // Hide label, input, and wrapper
        $label.hide();
        $field.hide();
        $wrapper.hide();
    });
});

jQuery(document).ready(function ($) {
    $('.wpresidence-detail-zip strong').each(function () {
      if ($(this).text().includes("Zip")) {
        $(this).text("Postal Code:");
      }
    });

    $('#front_submit_next_step').on('click', function() {
        $('html, body').animate({
          scrollTop: $('#front_submit_form').offset().top
        }, 100); 
    });




    let thirdLiValue = $('#a_filter_order').siblings('.filter_menu').find('li').eq(2).data('value');
    console.log("thirdLiValue " , thirdLiValue);
    // Get the selected value and label
    /* var selectedValue = jQuery(this).data('value');
    var selectedLabel = jQuery(this).text();

    // Update the button text
    jQuery(this).closest('.wpresidence_dropdown').find('.dropdown-toggle').text(selectedLabel);

    // Update the hidden input value
    jQuery(this).closest('.wpresidence_dropdown').find('input[type="hidden"]').val(selectedValue);

    // Optionally close the dropdown after selection
    jQuery(this).closest('.dropdown').removeClass('show');
    jQuery(this).closest('.dropdown').find('.dropdown-menu').removeClass('show'); */

    var checkInterval = setInterval(function() {
        var thirdLi = $('#a_filter_order').siblings('.filter_menu').find('li').eq(2);
        if (thirdLi.length > 0) { 
            thirdLi.trigger('click');
            clearInterval(checkInterval); 
        }
    }, 100); 
    

  });
  
  
  




//   document.addEventListener('DOMContentLoaded', function () {
//     const fieldsToPersist = [
//         'fullName', 'email', 'dateOfBirth', 'gender', 'phone',
//         'address', 'city', 'bio', // bio = "Describe your work"
//         'facebook', 'instagram', 'twitter', 'linkedin',
//         'youtube', 'tiktok', 'website'
//     ];

//     fieldsToPersist.forEach(id => {
//         const field = document.getElementById(id);
//         if (field) {
//             // Load saved value
//             const savedValue = localStorage.getItem(id);
//             if (savedValue) field.value = savedValue;

//             // Save on change/input
//             field.addEventListener('input', () => {
//                 localStorage.setItem(id, field.value);
//             });

//             // For select fields like gender
//             field.addEventListener('change', () => {
//                 localStorage.setItem(id, field.value);
//             });
//         }
//     });

//     // --- Project Details (Repeater fields) ---
//     function saveProjectDetails() {
//         const titles = Array.from(document.querySelectorAll('input[name="title[]"]')).map(el => el.value);
//         const sites = Array.from(document.querySelectorAll('input[name="site[]"]')).map(el => el.value);
//         localStorage.setItem('project_titles', JSON.stringify(titles));
//         localStorage.setItem('project_sites', JSON.stringify(sites));
//     }

//     function loadProjectDetails() {
//         const titles = JSON.parse(localStorage.getItem('project_titles') || '[]');
//         const sites = JSON.parse(localStorage.getItem('project_sites') || '[]');

//         if (titles.length && sites.length) {
//             const container = document.getElementById('address-sections-container');
//             const original = container.querySelector('.address-section');
//             const template = original.cloneNode(true);
//             container.innerHTML = ''; // Clear all

//             titles.forEach((title, i) => {
//                 const clone = template.cloneNode(true);
//                 clone.querySelector('input[name="title[]"]').value = title;
//                 clone.querySelector('input[name="site[]"]').value = sites[i] || '';
//                 container.appendChild(clone);
//             });
//         }
//     }

//     // Save project details on input
//     document.addEventListener('input', (e) => {
//         if (e.target.name === 'title[]' || e.target.name === 'site[]') {
//             saveProjectDetails();
//         }
//     });

//     loadProjectDetails();
// });


// document.getElementById('submitProfileBtn').addEventListener('click', function () {
//     const data = {
//         action: 'save_portfolio_draft',
//         full_name: document.getElementById('fullName').value,
//         email: document.getElementById('email').value,
//         date_of_birth: document.getElementById('dateOfBirth').value,
//         gender: document.getElementById('gender').value,
//         phone: document.getElementById('phone').value,
//         address: document.getElementById('address').value,
//         city: document.getElementById('city').value,
//         bio: document.getElementById('bio').value,
//         linkedin: document.getElementById('linkedin').value,
//         website: document.getElementById('website').value,
//         facebook: document.getElementById('facebook').value,
//         instagram: document.getElementById('instagram').value,
//         youtube: document.getElementById('youtube').value,
//         site: Array.from(document.querySelectorAll('input[name="site[]"]')).map(el => el.value)
//     };

//     fetch('/wp-admin/admin-ajax.php', {
//         method: 'POST',
//         headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//         body: new URLSearchParams(data)
//     })
//     .then(res => res.json())
//     .then(response => {
//         if (response.success) {
//             // Redirect to checkout after saving
//             window.location.href = '/checkout';
//         } else {
//             alert('Failed to save profile. Try again.');
//         }
//     });
// });


// ************

document.getElementById('usermobile').addEventListener('input', function(event) {
    var input = event.target;
    var value = input.value;

    // Allow only numbers
    value = value.replace(/\D/g, '');

    // Limit to a maximum of 12 digits
    if (value.length > 12) {
        value = value.slice(0, 12);
    }

    // Set the updated value back to the input
    input.value = value;
});