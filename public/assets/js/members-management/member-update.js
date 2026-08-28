var __memberUpdate = new __memberUpdate();
function __memberUpdate() {  
    const mesiteurl = $('#__siteurl').attr('data-mesiteurl');
    
    this.initRFIDScanner = function() {
        const rfidInput = document.getElementById('rfid_uid');
        if(!rfidInput) return;
        
        rfidInput.focus();
        
        rfidInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                let uid = this.value.trim();
                
                if(uid !== '') {
                    $.ajax({
                        type: "POST",
                        url: mesiteurl + 'memberregistration',
                        data: { 
                            rfid_uid: uid,
                            meaction: 'CHECK-RFID'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.exists) {
                                toastr.warning('RFID card already registered to: ' + response.member_name, 'Duplicate Card', {
                                    progressBar: true,
                                    closeButton: true,
                                    timeOut: 3000
                                });
                            } else {
                                toastr.success('RFID card detected!', 'Success', {
                                    progressBar: true,
                                    closeButton: true,
                                    timeOut: 1500
                                });
                            }
                        }
                    });
                }
            }
        });
    };
    
    this.__saveMember = function() { 
        'use strict' 
        var forms = document.querySelectorAll('.member-reg-form')
        
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                try {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const termsAccepted = document.querySelector('input[name="terms_accepted"]').checked;
                    if(!termsAccepted) {
                        toastr.error('Please accept the terms and conditions!', 'Validation Error', {
                            progressBar: true,
                            closeButton: true,
                            timeOut: 3000
                        });
                        return false;
                    }
                    
                    var mparam = {
                        member_id: document.querySelector('input[name="member_id"]').value,
                        member_no: document.querySelector('input[name="member_no"]').value,
                        rfid_uid: document.querySelector('input[name="rfid_uid"]').value,
                        last_name: document.querySelector('input[name="last_name"]').value,
                        first_name: document.querySelector('input[name="first_name"]').value,
                        middle_name: document.querySelector('input[name="middle_name"]').value,
                        gender: document.querySelector('select[name="gender"]').value,
                        date_of_birth: document.querySelector('input[name="date_of_birth"]').value,
                        age: document.querySelector('input[name="age"]').value,
                        email: document.querySelector('input[name="email"]').value,
                        mobile_number: document.querySelector('input[name="mobile_number"]').value,
                        emergency_contact_name: document.querySelector('input[name="emergency_contact_name"]').value,
                        emergency_contact_number: document.querySelector('input[name="emergency_contact_number"]').value,
                        emergency_contact_relationship: document.querySelector('input[name="emergency_contact_relationship"]').value,
                        address: document.querySelector('textarea[name="address"]').value,
                        city: document.querySelector('input[name="city"]').value,
                        health_conditions: document.querySelector('textarea[name="health_conditions"]').value,
                        allergies: document.querySelector('textarea[name="allergies"]').value,
                        fitness_goals: document.querySelector('textarea[name="fitness_goals"]').value,
                        experience_level: document.querySelector('select[name="experience_level"]').value,
                        membership_plan: document.querySelector('select[name="membership_plan"]').value,
                        membership_start_date: document.querySelector('input[name="membership_start_date"]').value,
                        membership_end_date: document.querySelector('input[name="membership_end_date"]').value,
                        membership_status: document.querySelector('select[name="membership_status"]').value,
                        username: document.querySelector('input[name="username"]').value,
                        password: document.querySelector('input[name="password"]').value,
                        referred_by: document.querySelector('input[name="referred_by"]').value,
                        how_did_you_hear: document.querySelector('select[name="how_did_you_hear"]').value,
                        waiver_signed: document.querySelector('input[name="waiver_signed"]').checked ? 1 : 0,
                        terms_accepted: document.querySelector('input[name="terms_accepted"]').checked ? 1 : 0,
                        meaction: 'MEMBER-UPDATE'
                    };
                    
                    jQuery.ajax({
                        type: "POST",
                        url: mesiteurl + 'memberregistration',
                        context: document.body,
                        data: mparam,
                        global: false,
                        cache: false,
                        success: function(data) {
                            jQuery('.me-members-list-msg').html(data);
                            return false;
                        },
                        error: function(xhr, status, error) {
                            toastr.error('[REGISTRATION-ENT]', "Error: " + error, {
                                closeButton: true,
                            });
                            return false;
                        }
                    });
                    
                } catch(err) { 
                    alert(err.message)
                    return false;
                }
            }, false)
        });
    };
}

$(document).ready(function() {
    __memberUpdate.initRFIDScanner();
    __memberUpdate.__saveMember();
});