var __Inventory = new __Inventory();

function __Inventory() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    let processing = false;

    this.__saveInventory = function() { 
        'use strict';
        
        var forms = document.querySelectorAll('.inv-reg-form');

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

                    var product_name = $('#product_name').val();
                    var category = $('#category').val();
                    var purchase_price = $('#purchase_price').val();
                    var selling_price = $('#selling_price').val();
                    var stock_qty = $('#stock_qty').val();

                    var mparam = {
                        product_name: product_name,
                        category: category,
                        purchase_price: purchase_price,
                        selling_price: selling_price,
                        stock_qty: stock_qty,
                        meaction: 'STOCKIN-SAVE'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'inventory',
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

    this.__saveAdjustment = function() { 
        'use strict';
        
        var forms = document.querySelectorAll('.adj-reg-form');

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

                    var adj_product_name = $('#adj_product_name').val();
                    var adj_type = $('#adj_type').val();
                    var remarks = $('#remarks').val();
                    var adj_qty = $('#adj_qty').val();

                    var mparam = {
                        adj_product_name: adj_product_name,
                        adj_type: adj_type,
                        remarks: remarks,
                        adj_qty: adj_qty,
                        meaction: 'ADJUSTMENT-SAVE'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'inventory',
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
    __Inventory.__saveInventory();
    __Inventory.__saveAdjustment();
});