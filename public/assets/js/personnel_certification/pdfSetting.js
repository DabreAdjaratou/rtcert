function validateForm() {
    var reg=/[0-9#{}~"\[\]/\\()|@$?;§!<>*+=$£µ_°¤%:<>]/;
    var header_text = document.getElementById("header_text").value;
    var text_lengh = header_text.length;

    if (text_lengh > 163) {
        alert("Warning ! the number of characters is limited to 163. ");
        return false;
    }
    
    if(reg.test(header_text)){
        alert("Warning ! numbers and following specials characters  # {} ~ \" \ [\] / \\ () | @ $ ?; §! <> * + = $ £ μ_ ° ¤%: <> can not make text of heading ");
        return false;
    }


}
validateForm();

function msgLogo() {
    if (msg_logo_left) {
        alert(msg_logo_left);
    }

    if (msg_logo_right) {
        alert(msg_logo_right);
    }

    if (msg_header_text) {
        alert(msg_header_text);
    }
}
msgLogo();

var header_text = document.getElementById("header_text");
header_text.addEventListener('keyup', characters_number)

function characters_number() {
    var header_text_value = document.getElementById("header_text").value;
    var text_lengh = header_text_value.length;
    document.getElementById("characters").innerHTML = "<p  class='btn btn-space btn-danger'>" + text_lengh + " charcters</p>";
}
;

