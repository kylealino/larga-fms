var __Transactions = new __Transactions();

function __Transactions() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('Transactions initialized, URL: ' + mesiteurl);

    this.__saveTransaction = function() { 
        'use strict';
        
        console.log('__saveTransaction function called');
        
        var forms = document.querySelectorAll('.trans-reg-form');
        
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
                    var transaction_date = $('#transaction_date').val();
                    var checkin_time = $('#checkin_time').val();
                    var checkout_time = $('#checkout_time').val();
                    var stage_id = $('#stage_id').val();
                    var shooter_type = $('#shooter_type').val();
                    var shooter_name = $('#shooter_name').val();
                    var range_assistant_id = $('#range_assistant_id').val();
                    var rangefee_amount = $('#rangefee_amount').val();
                    var targetboard_amount = $('#targetboard_amount').val();
                    var ammunition_amount = $('#ammunition_amount').val();
                    var total_amount = $('#total_amount').val();
                    var notes = $('#notes').val();

                    console.log('Saving transaction...');

                    var mparam = {
                        transaction_date: transaction_date,
                        checkin_time: checkin_time,
                        checkout_time: checkout_time,
                        stage_id: stage_id,
                        shooter_type: shooter_type,
                        shooter_name: shooter_name,
                        range_assistant_id: range_assistant_id,
                        rangefee_amount: rangefee_amount,
                        targetboard_amount: targetboard_amount,
                        ammunition_amount: ammunition_amount,
                        total_amount: total_amount,
                        notes: notes,
                        meaction: 'SAVE'
                    };

                    console.log('Sending AJAX...');

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'transactions',
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
    console.log('Document ready, initializing saveTransaction');
    __Transactions.__saveTransaction();
});