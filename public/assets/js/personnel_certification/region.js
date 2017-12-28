function validateForm() {
var a = document.forms["form"]["region_name"].value;
var elmt = document.getElementById("Region Name");
if (a == null || a == "")
    {
        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)";
        alert("Please enter the ''" + elmt.id + "''");
        return false;
    }
    
}

function emptyInput(input) {

    input.style.boxShadow = 'none';
}
