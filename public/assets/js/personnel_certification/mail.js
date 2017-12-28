function validateForm() {
    var type = document.getElementById('Choice').value;

    if (type == '' || type == null) {
        document.getElementById('Choice').style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('You must choose a "type of email"');
        return false;
    }

    if (document.getElementById('Choice').value == 1 && document.getElementById('file').value == '') {
        document.getElementById('file').style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('You must load the certificate in PDF format');
        return false;
    }

    if (type == 2 && document.getElementById('type_recipient').value == '') {
        document.getElementById('type_recipient').style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "Reminder Sent To"');
        return false;
    }


}


function setChoice() {
    var choice = document.getElementById("Choice").value;
    var message = document.getElementById("Message");
    var subject = document.getElementById("Subject");
    var myAnchor = document.getElementById("Recipient");
    var myAnchor2 = document.getElementById("Recipient_name");
    var div_file = document.getElementById('div-file');
    if (choice == 1) {

        message.innerHTML = 'Congratulations ' + provider + '! You have successfully fulfilled the requirements of the national HIV tester certification program and are deemed competent to perform HIV  Rapid Testing.  This certificate of competency is delivered to you for a two year period from the date of issuance.\n\
\n\Important Note!!! \n\
\n\This certificate is only issued for HIV Rapid Testing and does not allow to perform any other test.\n\
\n\Note for printing the certificate!!!\n\
To print this certificate ensure that the paper size selected by the printer is A4 and that the orientation is landscape';
        subject.innerHTML = 'HIV Tester Certificate of Competency';
        myAnchor.innerHTML = "";
        myAnchor2.innerHTML = "";
    } else if (choice == 2) {

        myAnchor.innerHTML = "<div class='form-group col-lg-6'><label>Reminder Sent To</label><span class='mandatory'>*</span>\n\
<select name='type_recipient'id='type_recipient' class='form-control' onclick='emptyInput(type_recipient)' onchange = 'setMsg()'>\n\
<option value=''>please select an recipient</option>\n\
<option value='District focal person'>District focal person</option>\n\
<option value='HTS focal person'>HTS focal person</option>\n\
<option value='Implementing partner'>Implementing partner</option>\n\
<option value='Facility in charge'>Facility in charge</option>\n\
<option value='Provider'>Provider</option>\n\
<option value='QA/QI focal person'>QA/QI focal person</option><select></div>";
        message.innerHTML = '';
        subject.innerHTML = '';
        myAnchor2.innerHTML = "<div class='form-group col-lg-6'><label>Name Of Recipient</label><input name='name_recipient' id='name_recipient' class='form-control' type='text'/></div>";
        div_file.innerHTML = '';
    } 
//    else if (choice == "") {
//        document.getElementById('form').innerHTML='';
//        message.innerHTML = "";
//        subject.innerHTML = '';
//
//    }

}
setChoice();

function setMsg() {
    var recipient = document.getElementById("to");
    var recipient2 = document.getElementById("cc");
    var choice = document.getElementById("Choice").value;
    var message = document.getElementById("Message");
    var subject = document.getElementById("Subject");
    var type_recipient = document.getElementById("type_recipient").value;

    if (choice == 2 && type_recipient == '') {
        subject.innerHTML = '';
        message.innerHTML = '';
    } else if (choice == 2 && type_recipient == 'Provider') {
        message.innerHTML = "This is a reminder that your HIV tester certificate will expire on " + due_date + ". Please contact your national certification organization to schedule both the written and practical examinations. Any delay in completing these assessments will automatically result in the withdrawal of your certificate.";
        subject.innerHTML = 'HIV Tester Certificate Reminder';
           } else if (choice == 2 && type_recipient != 'Provider') {

        subject.innerHTML = 'HIV Tester Certificate Reminder';
        message.innerHTML = ' This is a reminder that the HIV tester certificate of ' + provider + ' will expire on ' + due_date + '. Please contact your national certification organization to schedule both the written and practical examinations. Any delay in completing these assessments will automatically result in the withdrawal of the certificate.';

    }


}



function emptyInput(input) {

    input.style.boxShadow = 'none';
}



