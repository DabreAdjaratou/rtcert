
//  -------------------------------------------------------  

function validateForm() {
    var numbers = /^[-+]?[0-9\.]+$/;

    var a = document.forms["form"]["provider_id"].value;
    var b = document.forms["form"]["exam_admin"].value;
    var c = document.forms["form"]["direct_observation_score"].value;
    var d = document.forms["form"]["Sample_testing_score"].value;
    var e = document.forms["form"]["date"].value;
    var f = document.forms["form"]["exam_type"].value;
    var elmt = document.getElementById("Tester");
    var elmt2 = document.getElementById("Exam Administrator");
    var elmt3 = document.getElementById("Direct Observation Score");
    var elmt4 = document.getElementById("Sample Testing Score");
    var elmt5 = document.getElementById("date");
    var elmt6 = document.getElementById("Type of Exam");


    if (f == null || f == "")
    {

        elmt6.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the \"Number of Attempts\"");
        return false;
    }

    if (a == null || a == "")
    {

        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt.id + "''");
        return false;
    }

    if (b == null || b == "") {

        elmt2.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt2.id + "''");
        return false;
    }

    if (c == null || c == "") {
        elmt3.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt3.id + '"');
        return false;
    } else if (c < 0 || c > 100 || numbers.test(c) == false)
    {
        elmt3.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"' + elmt3.id + '"  Must be a valid nunber between 0 and 100');
        return false;
    }

    if (d == null || d == "") {
        elmt4.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt4.id + '"');
        return false;
    } else if (d < 0 || d > 100 || numbers.test(d) == false)
    {
        elmt4.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"' + elmt4.id + '"  Must be a valid nunber between 0 and 100');
        return false;
    }

    if (e == null || e == "") {
        elmt5.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt5.id + '"');
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


function attempt() {
    var myAnchor = document.getElementById("Type of Exam");
    if (nombre) {
        if (nombre == 0) {
            myAnchor.innerHTML = "<option value='1st attempt'>1st attempt</option>";
        } else if (nombre == 1) {
            myAnchor.innerHTML = "<option value='2nd attempt'>2nd attempt</option>";
        } else if (nombre == 2) {
            myAnchor.innerHTML = "<option value='3rd attempt'>3rd attempt</option>";
        } else if (nombre >= 3) {
            alert('This tester has already made three unsuccessful attempts. Please contact the national certication office.');
            window.location = "/written-exam";
        }
    }
}
attempt();

function tester() {

    var myAnchor = document.getElementById("Tester");
    if (id && name) {
        myAnchor.innerHTML = "<option value=" + id + ">" + name + "</option>";
    }
}
tester();

      