var __RangeAssistants = new __RangeAssistants();

function __RangeAssistants() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('RangeAssistants initialized, URL: ' + mesiteurl);

    this.__saveAssistant = function() { 
        'use strict';
        
        console.log('__saveAssistant function called');
        
        var forms = document.querySelectorAll('.ra-reg-form');
        
        console.log('Found forms: ' + forms.length);

        Array.prototype.slice.call(forms).forEach(function (form) {
            console.log('Adding listener to form', form);
            form.addEventListener('submit', function (event) {
                console.log('Submit event triggered!');
                event.preventDefault();
                event.stopPropagation();
                
                if (!form.checkValidity()) {
                    console.log('Form invalid');
                    return;
                }
                
                try {
                    var full_name = $('#full_name').val();
                    var badge_number = $('#badge_number').val();
                    var position = $('#position').val();
                    var status = $('#status').val();

                    console.log('Full Name: ' + full_name);

                    var mparam = {
                        full_name: full_name,
                        badge_number: badge_number,
                        position: position,
                        status: status,
                        meaction: 'SAVE'
                    };

                    console.log('Sending AJAX...');

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'rangeassistants',
                        data: mparam,
                        dataType: 'json',
                        success: function(data) {
                            console.log('AJAX Success:', data);
                            if(data.status == 'success'){
                                toastr.success(data.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', error);
                            toastr.error("Error: " + error);
                        }
                    });

                } catch(err) {
                    console.log('Error:', err.message);
                    alert(err.message);
                    return false;
                }
            }, false);
        });
    };
}

$(document).ready(function() {
    console.log('Document ready, initializing saveAssistant');
    __RangeAssistants.__saveAssistant();
});