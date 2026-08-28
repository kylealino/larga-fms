var __ChartOfAccounts = new __ChartOfAccounts();

function __ChartOfAccounts() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    console.log('ChartOfAccounts initialized, URL: ' + mesiteurl);

    this.__saveAccount = function() { 
        'use strict';
        
        console.log('__saveAccount function called');
        
        var forms = document.querySelectorAll('.coa-reg-form');
        
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
                    var account_code = $('#account_code').val();
                    var account_name = $('#account_name').val();
                    var account_type = $('#account_type').val();
                    var account_category = $('#account_category').val();
                    var normal_balance = $('#normal_balance').val();
                    var parent_account_id = $('#parent_account_id').val();
                    var description = $('#description').val();

                    console.log('Account Code: ' + account_code);
                    console.log('Account Name: ' + account_name);

                    var mparam = {
                        account_code: account_code,
                        account_name: account_name,
                        account_type: account_type,
                        account_category: account_category,
                        normal_balance: normal_balance,
                        parent_account_id: parent_account_id,
                        description: description,
                        meaction: 'SAVE'
                    };

                    console.log('Sending AJAX...');

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'chartofaccounts',
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
    console.log('Document ready, initializing saveAccount');
    __ChartOfAccounts.__saveAccount();
});