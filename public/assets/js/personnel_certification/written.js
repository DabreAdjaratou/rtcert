
//  -------------------------------------------------------  

function validateForm() {

    var numbers = /^[-+]?[0-9\.]+$/;

    var a = document.forms["form"]["exam_type"].value;
    var b = document.forms["form"]["provider_id"].value;
    var r = document.forms["form"]["exam_admin"].value;
    var c = document.forms["form"]["date"].value;
    var d = document.forms["form"]["qa_point"].value;
    var e = document.forms["form"]["rt_point"].value;
    var f = document.forms["form"]["safety_point"].value;
    var g = document.forms["form"]["specimen_point"].value;
    var h = document.forms["form"]["testing_algo_point"].value;
    var i = document.forms["form"]["report_keeping_point"].value;
    var j = document.forms["form"]["EQA_PT_points"].value;
    var k = document.forms["form"]["ethics_point"].value;
    var l = document.forms["form"]["inventory_point"].value;

    var elmt = document.getElementById("Type of Exam");
    var elmt2 = document.getElementById("Tester");
    var elmt3 = document.getElementById("Exam Admin By");
    var elmt4 = document.getElementById("date");
    var elmt5 = document.getElementById("QA");
    var elmt6 = document.getElementById("RT");
    var elmt7 = document.getElementById("Safety");
    var elmt8 = document.getElementById("Specimen");
    var elmt9 = document.getElementById("Testing Algo");
    var elmt10 = document.getElementById("Report Keeping");
    var elmt11 = document.getElementById("EQA_PT");
    var elmt12 = document.getElementById("Ethics");
    var elmt13 = document.getElementById("Inventory");

    if (a == null || a == "")
    {
        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the \"Number of Attempts\"");
        return false;
    }
    if (b == null || b == "") {
        elmt2.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt2.id + "''");
        return false;
    }
    if (r == null || r == "")
    {
        elmt3.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt3.id + "''");
        return false;
    }
    if (c == null || c == "") {
        elmt4.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt4.id + "''");
        return false;
    }

     if (d == null || d == "") {
        elmt5.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt5.id +'  (point)"');
        return false;
    }
    else if (d<0 || d>3 || numbers.test(d) == false)
    {
        elmt5.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt5.id + ' (point) " Must be a valid nunber between 0 and 3');
        return false;
    } 
    
     if (e == null || e == "") {
        elmt6.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt6.id + ' (point)"');
        return false;
    }
    else if (e<0 || e>3 || numbers.test(e) == false)
    {
        elmt6.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt6.id + ' (point) " Must be a valid nunber between 0 and 3');
        return false;
    } 
    
     if (f == null || f == "") {
        elmt7.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt7.id + ' (point)"');
        return false;
    }
    else if (f<0 || f>3 || numbers.test(f) == false)
    {
        elmt7.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt7.id + ' (point) " Must be a valid nunber between 0 and 3');
        return false;
    } 
    
     if (g == null || g == "") {
        elmt8.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt8.id + '(point)"');
        return false;
    }
    else if (g<0 || g>2 || numbers.test(g) == false)
    {
        elmt8.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt8.id + ' (point) " Must be a valid nunber between 0 and 2');
        return false;
    } 
    
     if (h == null || h == "") {
        elmt9.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt9.id + '(point)"');
        return false;
    }
    else if (h<0 || h>3 || numbers.test(h) == false)
    {
        elmt9.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt9.id + ' (point) " Must be a valid nunber between 0 and 3');
        return false;
    } 
    
     if (i == null || i == "") {
        elmt10.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt10.id +'(point)"');
        return false;
    }
    else if (i<0 || i>3 || numbers.test(i) == false)
    {
        elmt10.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt10.id + ' (point) " Must be a valid nunber between 0 and 3');
        return false;
    } 
    
     if (j == null || j == "") {
        elmt11.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt11.id + '(point)"');
        return false;
    }
    else if (j<0 || j>4 || numbers.test(j) == false)
    {
        elmt11.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt11.id + ' (point) " Must be a valid nunber between 0 and 4');
        return false;
    } 
    
     if (k == null || k == "") {
        elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt12.id + '(point)"');
        return false;
    }
    else if (k<0 || k>2 || numbers.test(k) == false)
    {
        elmt12.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt12.id + ' (point) " Must be a valid nunber between 0 and 2');
        return false;
    } 
    
     if (l == null || l == "") {
        elmt13.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('Please enter the "' + elmt13.id + '(point)"');
        return false;
    }
    else if (l<0 || l>2 || numbers.test(l) == false)
    {
        elmt13.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert('"'+elmt13.id + ' (point)" Must be a valid nunber between 0 and 2');
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


function setAttempt() {
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
                                window.location = "/practical-exam";
                            }
                        }
                    }
                    setAttempt();

                    function tester() {

                        var myAnchor = document.getElementById("Tester");
                        if (id && name) {

                            myAnchor.innerHTML = "<option value=" + id + ">" + name + "</option>";

                        }
                    }

                    tester();



