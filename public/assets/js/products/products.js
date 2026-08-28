var __Products = new __Products();

function __Products() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    let processing = false;

    this.__updateProduct = function() { 
        'use strict';
        
        var forms = document.querySelectorAll('.product-update-form');

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

                    var product_id = $('#edit_product_id').val();
                    var product_name = $('#edit_product_name').val();
                    var category = $('#edit_category').val();
                    var purchase_price = $('#edit_purchase_price').val();
                    var selling_price = $('#edit_selling_price').val();

                    var mparam = {
                        product_id: product_id,
                        product_name: product_name,
                        category: category,
                        purchase_price: purchase_price,
                        selling_price: selling_price,
                        meaction: 'UPDATE-PRODUCT'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'products',
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
                            toastr.error('Error: ' + error);
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
    __Products.__updateProduct();
});