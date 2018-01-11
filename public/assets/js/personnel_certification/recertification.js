
function validateForm()
{
    var a = document.forms["form"]["due_date"].value;
    var b = document.forms["form"]["provider_id"].value;
    var c = document.forms["form"]["reminder_type"].value;
    var d = document.forms["form"]["reminder_sent_to"].value;
    var e = document.forms["form"]["name_of_recipient"].value;
    var f = document.forms["form"]["date_reminder_sent"].value;

    var elmt = document.getElementById("date")
    var elmt2 = document.getElementById("Provider");
    var elmt3 = document.getElementById("Type Of Reminder");
    var elmt4 = document.getElementById("Reminder Send To");
    var elmt5 = document.getElementById("Name Of Recipient");
    var elmt6 = document.getElementById("date2");


    if (a == null || a == "")
    {

        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the \"Due Date\"");

        return false;
    }

    if (b == null || b == "") {


        elmt2.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt2.id + "''");
        return false;
    }

    if (c == null || c == "") {


        elmt3.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt3.id + "''");
        return false;
    }
    if (d == null || d == "") {


        elmt4.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt4.id + "''");
        return false;
    }
    if (e == null || e == "") {


        elmt5.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt5.id + "''");
        return false;
    }

    if (f == null || f == "")
    {

        elmt6.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the \"Date Reminder Sent\"");

        return false;
    }

}

function emptyInput(input) {

    input.style.boxShadow = 'none';
}


$(document).ready(function () {

    $("#date").datepicker(
            {
                showButtonPanel: true
                , dateFormat: 'dd-mm-yy'
                , dayNamesMin: ['Mon', 'Tues', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun']
                , dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', ' Thursday ', 'Friday', 'Saturday']
                , monthNamesShort: ['Jan', 'Fed', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                , monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', ]
                , prevText: 'Previous'
                , nextText: 'Next'
                , closeText: 'OK'
                , currentText: "Today"
            });

}); //EOf:: DOM isReady

$(document).ready(function () {

    $("#date2").datepicker(
            {
                showButtonPanel: true
                , dateFormat: 'dd-mm-yy'
                , dayNamesMin: ['Mon', 'Tues', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun']
                , dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', ' Thursday ', 'Friday', 'Saturday']
                , monthNamesShort: ['Jan', 'Fed', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                , monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', ]
                , prevText: 'Previous'
                , nextText: 'Next'
                , closeText: 'OK'
                , currentText: "Today"
            });

}); //EOf:: DOM isReady

function setProvider() {


    if (id && name) {
        var myAnchor = document.getElementById("Provider");
        myAnchor.innerHTML = "<option value=" + id + ">" + name + "</option>";
    }

    if (due_date) {
        var myAnchor2 = document.getElementById('date');

        myAnchor2.setAttribute('value', due_date);
    }
}
setProvider();