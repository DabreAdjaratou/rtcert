function validateForm() {
    var a = document.forms["form"]["district"].value;
    var b = document.forms["form"]["facility_name"].value;
    var elmt = document.getElementById("District");
    var elmt2 = document.getElementById("Facility Name");
    
   
    if (a == null || a == "")
    {
        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt.id + "''");
        return false;
    }

    if (b == null || b == "")
    {
        elmt2.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt2.id + "''");
        return false;
    }

}

function emptyInput(input) {

    input.style.boxShadow = 'none';
}


