var __BayStatus = new __BayStatus();

function __BayStatus() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('BayStatus initialized, URL: ' + mesiteurl);

    this.__saveBayStatus = function() { 
        'use strict';
        
        console.log('__saveBayStatus function called');
        
        var forms = document.querySelectorAll('.bs-reg-form');
        
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
                    var stage_id = $('#stage_id').val();
                    var status = $('#status').val();

                    console.log('Stage: ' + stage_id + ', Status: ' + status);

                    var mparam = {
                        stage_id: stage_id,
                        status: status,
                        meaction: 'SAVE'
                    };

                    console.log('Sending AJAX...');

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'baystatus',
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
    console.log('Document ready, initializing saveBayStatus');
    __BayStatus.__saveBayStatus();
});