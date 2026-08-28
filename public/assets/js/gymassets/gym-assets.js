var __GymAssets = new __GymAssets();

function __GymAssets() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    this.__saveAsset = function() { 
        var forms = document.querySelectorAll('.asset-reg-form');

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

                    var asset_name = $('#asset_name').val();
                    var asset_category = $('#asset_category').val();
                    var acquisition_cost = $('#acquisition_cost').val();
                    var useful_life_months = $('#useful_life_months').val();
                    var notes = $('#notes').val();

                    var mparam = {
                        asset_name: asset_name,
                        asset_category: asset_category,
                        acquisition_cost: acquisition_cost,
                        useful_life_months: useful_life_months,
                        notes: notes,
                        meaction: 'SAVE'
                    };

                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'gym-assets',
                        data: mparam,
                        dataType: 'json',
                        success: function(response) {
                            console.log('Save response:', response);
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
                            console.log('AJAX Error:', xhr.responseText);
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
    __GymAssets.__saveAsset();
});