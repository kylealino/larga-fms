var __CashReceipts = new __CashReceipts();

function __CashReceipts() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    this.__saveCashReceipt = function() { 
        'use strict';
        
        var forms = document.querySelectorAll('.cr-reg-form');

        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    return;
                }
                
                try {
                    event.preventDefault();
                    event.stopPropagation();

                    var date = $('#date').val();
                    var account_code = $('#account_code').val();
                    var amount = $('#amount').val();

                    var mparam = {
                        date: date,
                        account_code: account_code,
                        amount: amount,
                        meaction: 'SAVE'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'cashreceipts',
                        data: mparam,
                        dataType: 'json',
                        success: function(response) {
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error("Error: " + error);
                        }
                    });

                } catch(err) {
                    alert(err.message);
                    return false;
                }
            }, false);
        });
    };
}

$(document).ready(function() {
    __CashReceipts.__saveCashReceipt();
});