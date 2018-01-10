function validateForm() {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

    var a = document.forms["form"]["last_name"].value;
    var b = document.forms["form"]["first_name"].value;
    var r = document.forms["form"]["middle_name"].value;
    var c = document.forms["form"]["email"].value;
    var d = document.forms["form"]["test_site_in_charge_email"].value;
    var e = document.forms["form"]["facility_in_charge_email"].value;

    var f = document.forms["form"]["region"].value;
    var g = document.forms["form"]["district"].value;
    var h = document.forms["form"]["type_vih_test"].value;
    var i = document.forms["form"]["phone"].value;
    var j = document.forms["form"]["prefered_contact_method"].value;
    var k = document.forms["form"]["current_jod"].value;
    var l = document.forms["form"]["time_worked"].value;
    var m = document.forms["form"]["test_site_in_charge_name"].value;
    var n = document.forms["form"]["test_site_in_charge_phone"].value;
    var o = document.forms["form"]["facility_in_charge_name"].value;
    var p = document.forms["form"]["facility_in_charge_phone"].value;
    var q = document.forms["form"]["facility_id"].value;

    var s = document.forms["form"]["select_time"].value;

    var elmt = document.getElementById("Last Name");
    var elmt2 = document.getElementById("First Name");
    var elmt6 = document.getElementById("Middle Name");
    var elmt3 = document.getElementById("Tester Email");
    var elmt4 = document.getElementById("Testing site in charge Email");
    var elmt5 = document.getElementById("Facility in charge Email");
    var elmt7 = document.getElementById("Region");
    var elmt8 = document.getElementById("District");
    var elmt18 = document.getElementById("Type of VIH Test Modality/Point");
    var elmt9 = document.getElementById("Tester Phone Number");
    var elmt10 = document.getElementById("Prefered Contact Method");
    var elmt11 = document.getElementById("Current Job Title");
    var elmt12 = document.getElementById("Time worked as tester");
    var elmt13 = document.getElementById("Testing site in charge Name");
    var elmt14 = document.getElementById("Testing site in charge Phone");
    var elmt15 = document.getElementById("Facility in charge Name");
    var elmt16 = document.getElementById("Facility in charge Phone");
    var elmt17 = document.getElementById("Facility Name");
    var elmt19 = document.getElementById("Time");

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

    if (c != "" && reg.test(c) == false)
    {
        elmt3.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Invalid Email Address for "' + elmt3.id + '"');
        return false;
    }

    if (d != "" && reg.test(d) == false)
    {
        elmt4.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Invalid Email Address for "' + elmt4.id + '"');
        return false;
    }

    if (e != "" && reg.test(e) == false)
    {
        elmt5.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Invalid Email Address for "' + elmt5.id + '"');
        return false;
    }

    if (f == null || f == "") {
        elmt7.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt7.id + "''");
        return false;
    }


    if (g == null || g == "") {
        elmt8.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt8.id + "''");
        return false;
    }


    if (h == null || h == "") {
        elmt18.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt18.id + "''");
        return false;
    }

    if (i == null || i == "") {
        elmt9.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt9.id + "''");
        return false;
    }

    if (j == null || j == "") {
        elmt10.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt10.id + "''");
        return false;
    }

    if (k == null || k == "") {
        elmt11.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt11.id + "''");
        return false;
    }

    if (q == null || q == "") {
        elmt17.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt17.id + "''");
        return false;
    }

    if (l == null || l == "") {
        elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt12.id + "''");
        return false;
    }

    if (s == null || s == "") {
        elmt19.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please complete the ''" + elmt19.id + "''");
        return false;
    }

    if (s == 'Days') {

        if (isNaN(l)) {
            elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
            alert("The number of days must be a number between 1 and 366.");
            return false;
        } else {
            if (l <= 0 || l > 366) {
                elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
                alert("The number of days must be between 1 and 366.");
                return false;
            }
        }
    }

    if (s == 'Weeks') {
        if (isNaN(l)) {
            elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
            alert("The number of weeks must be a number between 1 and 52.");
            return false;
        } else {
            if (l <= 0 || l > 52) {
                elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
                alert("The number of weeks must be between 1 and 52.");
                return false;
            }
        }
    }

    if (s == 'Months') {
        if (isNaN(l)) {
            elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
            alert("The number of months must be a number between 1 and 12.");
            return false;
        } else {
            if (l <= 0 || l > 12) {
                elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
                alert("The number of months must be between 1 and 12.");
                return false;
            }

        }
    }
    
     if (s == 'Years') {
        if (isNaN(l)) {
            elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
            alert("The number of Years must be a number.");
            return false;
        } 
    }


    if (m == null || m == "") {
        elmt13.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt13.id + "''");
        return false;
    }

    if (n == null || n == "") {
        elmt14.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt14.id + "''");
        return false;
    }

    if (o == null || o == "") {
        elmt15.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt15.id + "''");
        return false;
    }

    if (p == null || p == "") {
        elmt16.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt16.id + "''");
        return false;
    }


}


function emptyInput(input) {

    input.style.boxShadow = 'none';
}

function setTime() {

    if (time) {
        document.getElementById(time).selected = "true";
    }

}
setTime();










