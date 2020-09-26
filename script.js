$(document).ready(function() {
    // DATE and TIME pickers

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '-3d'
    });

    $('#i_time').clockpicker({
        autoclose: true
    });

    // on Form submit
    $('form').on('submit', function(e){
        // validation code here
        e.preventDefault();
        if(validateFields()){
            grecaptcha.ready(function() {
                grecaptcha.execute('6Le9xdAZAAAAAMmyDQGKWzMIVEiigVu57amsfMk3', {action: 'submit'}).then(function(token) {
                    sendDataToBackEnd(token);
                });
            });
        }
    });
});

function validateFields(){
    var i_name = $("#i_name");
    var i_phone = $("#i_phone");
    var i_email = $("#i_email");
    var i_time = $("#i_time");
    var i_date = $("#i_date");

    err_list = []; // contains all the errors found within our fields

    // regex to compare our field values
    reg_name = new RegExp('^[A-z]+ [A-z]+$');
    reg_phone = new RegExp('^[0-9]{8,10}$');
    reg_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    reg_time = new RegExp('^[0-9]{2}[:][0-9]{2}$');
    reg_date = new RegExp('^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$');

    if (!reg_name.test(i_name.val())) {
        i_name.addClass("is-invalid");
        err_list.push('Name Field must contain First and Last name. Example: "John Doe"!')
    } else {i_name.removeClass("is-invalid");}

    if (!reg_phone.test(i_phone.val())) {
        i_phone.addClass("is-invalid");
        err_list.push('Phone number must be in a valid format, from 8 to 10 numbers!')
    } else {i_phone.removeClass("is-invalid");}

    if (!reg_email.test(i_email.val())) {
        i_email.addClass("is-invalid");
        err_list.push('Email Address is not valid!')
    } else {i_email.removeClass("is-invalid");}

    if (!reg_time.test(i_time.val())) {
        i_time.addClass("is-invalid");
        err_list.push('Time is not valid!')
    } else {i_time.removeClass("is-invalid");}

    if (!reg_date.test(i_date.val())) {
        i_date.addClass("is-invalid");
        err_list.push('Date is not valid!')
    }  else {i_date.removeClass("is-invalid");}
        
    if (!err_list.length < 1){
        var formInvalidText="<ul>";
        err_list.forEach(elem=>{
            formInvalidText += "<li>" + elem + "</li>";
        })
        formInvalidText +="</ul>";
        $('#formInvalid').html(formInvalidText);
        return false;
    } else {
        formInvalidText="";
        $('#formInvalid').html(formInvalidText);
        return true;
    }
}

function sendDataToBackEnd(token){
    var i_name = $("#i_name").val();
    var i_phone = $("#i_phone").val();
    var i_email = $("#i_email").val();
    var i_time = $("#i_time").val();
    var i_date = $("#i_date").val();
    var i_note = $("#i_note").val();
    $.ajax({
        type: "POST",
        async: false,
        url:window.location.href + '/form.handler.php',
        data: {
            i_name : i_name,
            i_phone : i_phone,
            i_email : i_email,
            i_time : i_time,
            i_date : i_date,
            i_note : i_note,
            recapcha_token:token
        },
        success: function(data){
        console.log(data);
        $('#succ_modal').modal('show');
        },
        error: function(){
            $('#err_modal').modal('show');
        }
    });
}