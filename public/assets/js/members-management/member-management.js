var __membersManagement = new __membersManagement();

function __membersManagement() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');

    let processing = false;

    // =========================
    // RFID SCANNER
    // =========================
    this.initRFIDScanner = function() {
        const rfidInput = document.getElementById('rfid_uid');

        if(!rfidInput) return;

        rfidInput.focus();

        rfidInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();

                let uid = this.value.trim();
                if(uid === '' || processing) return;

                processing = true;

                $.ajax({
                    type: "POST",
                    url: mesiteurl + 'membersmanagement',
                    data: { 
                        rfid_uid: uid,
                        meaction: 'CHECK-RFID'
                    },
                    dataType: 'json',

                    success: function(response){

                        // =========================
                        // RFID EXISTS
                        // =========================
                        if(response.exists === true){
                            toastr.clear();
                            toastr.error('RFID already registered to ' + response.member_name);

                            processing = false;
                            rfidInput.value = "";
                            rfidInput.focus();
                            return;
                        }

                        // =========================
                        // RFID AVAILABLE
                        // =========================
                        toastr.clear();
                        toastr.success('RFID ready for registration');

                        // auto fill input (optional)
                        rfidInput.value = uid;

                        processing = false;
                        rfidInput.focus();
                    },

                    error: function(){
                        toastr.error("Server error");
                        processing = false;
                    }
                });
            }
        });
    };

    // =========================
    // SAVE MEMBER - JSON STYLE
    // =========================
    this.__saveMember = function() { 
        'use strict';

        var forms = document.querySelectorAll('.member-reg-form');

        Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    return;
                }

                try {
                    event.preventDefault();
                    event.stopPropagation();

                    // =========================
                    // GET ALL FIELDS
                    // =========================
                    var member_id = document.querySelector('input[name="member_id"]');
                    var rfid_uid = document.getElementById("rfid_uid");
                    var member_no = document.querySelector('input[name="member_no"]');
                    var last_name = document.querySelector('input[name="last_name"]');
                    var first_name = document.querySelector('input[name="first_name"]');
                    var middle_name = document.querySelector('input[name="middle_name"]');
                    var gender = document.querySelector('select[name="gender"]');
                    var date_of_birth = document.getElementById("date_of_birth");
                    var age = document.getElementById("age");

                    var email = document.querySelector('input[name="email"]');
                    var mobile_number = document.querySelector('input[name="mobile_number"]');
                    var emergency_contact_name = document.querySelector('input[name="emergency_contact_name"]');
                    var emergency_contact_number = document.querySelector('input[name="emergency_contact_number"]');
                    var emergency_contact_relationship = document.querySelector('input[name="emergency_contact_relationship"]');
                    var address = document.querySelector('textarea[name="address"]');
                    var city = document.querySelector('input[name="city"]');

                    var health_conditions = document.querySelector('textarea[name="health_conditions"]');
                    var allergies = document.querySelector('textarea[name="allergies"]');
                    var fitness_goals = document.querySelector('textarea[name="fitness_goals"]');
                    var experience_level = document.querySelector('select[name="experience_level"]');

                    var membership_plan = document.getElementById("membership_plan");
                    var membership_start_date = document.getElementById("start_date");
                    var membership_end_date = document.getElementById("end_date");
                    var membership_status = document.querySelector('select[name="membership_status"]');

                    var waiver_signed = document.getElementById("waiver");
                    var terms_accepted = document.getElementById("terms");

                    // =========================
                    // VALIDATION
                    // =========================
                    if (!terms_accepted.checked) {
                        toastr.error('Please accept terms and conditions');
                        return;
                    }

                    // =========================
                    // PARAMS
                    // =========================
                    var mparam = {
                        member_id: member_id.value,
                        rfid_uid: rfid_uid.value,
                        member_no: member_no.value,
                        last_name: last_name.value,
                        first_name: first_name.value,
                        middle_name: middle_name.value,
                        gender: gender.value,
                        date_of_birth: date_of_birth.value,
                        age: age.value,

                        email: email.value,
                        mobile_number: mobile_number.value,
                        emergency_contact_name: emergency_contact_name.value,
                        emergency_contact_number: emergency_contact_number.value,
                        emergency_contact_relationship: emergency_contact_relationship.value,
                        address: address.value,
                        city: city.value,

                        health_conditions: health_conditions.value,
                        allergies: allergies.value,
                        fitness_goals: fitness_goals.value,
                        experience_level: experience_level.value,

                        waiver_signed: waiver_signed.checked ? 1 : 0,
                        terms_accepted: terms_accepted.checked ? 1 : 0,

                        meaction: 'MEMBER-SAVE'
                    };

                    // =========================
                    // AJAX - JSON RESPONSE
                    // =========================
                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'membersmanagement',
                        data: mparam,
                        dataType: 'json',
                        success: function(response) {
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.href = mesiteurl + 'membersmanagement?meaction=MAIN';
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error("Error saving member: " + error);
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
    __membersManagement.initRFIDScanner();
    __membersManagement.__saveMember();
});