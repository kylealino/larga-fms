var __GeneralJournal = new __GeneralJournal();

function __GeneralJournal() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    this.__saveEntry = function() { 
        var forms = document.querySelectorAll('.gj-reg-form');

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
                    var description = $('#description').val();
                    var reference_no = $('#reference_no').val();

                    console.log('Saving Entry:');
                    console.log('Date:', date);
                    console.log('Account Code:', account_code);
                    console.log('Amount:', amount);
                    console.log('Description:', description);
                    console.log('Reference No.:', reference_no);

                    if(amount == '' || amount == 0){
                        toastr.error('Amount is required and must be greater than 0');
                        return;
                    }

                    if(account_code == ''){
                        toastr.error('Please select an Account Code');
                        return;
                    }

                    var mparam = {
                        date: date,
                        account_code: account_code,
                        amount: amount,
                        description: description,
                        reference_no: reference_no,
                        meaction: 'SAVE'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'general-journal',
                        data: mparam,
                        dataType: 'json',
                        success: function(response) {
                            console.log('Response:', response);
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
                            console.log('Error:', xhr.responseText);
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
    __GeneralJournal.__saveEntry();
});