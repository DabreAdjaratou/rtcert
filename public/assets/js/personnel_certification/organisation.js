function validateForm()
{
    var a = document.forms["form"]["training_organization_name"].value;
    var b = document.forms["form"]["type_organization"].value;

    var elmt = document.getElementById("Training Organization Name");
    var elmt2 = document.getElementById("Type of Organization");


    if (a == null || a == "")
    {

        elmt.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt.id + "''");

        return false;
    } else if (b == null || b == "") {


        elmt2.style.boxShadow = "2px 2px 10px rgba(200, 0, 0, 0.85)"
        alert("Please enter the ''" + elmt2.id + "''");
        return false;
    }

}


function inputOrgaName() {
    var input = document.getElementById("Training Organization Name");
    input.style.boxShadow = 'none';
}
function inputOrgaType() {
    var input = document.getElementById("Type of Organization");
    input.style.boxShadow = 'none';
}