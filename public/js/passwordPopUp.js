
function showPasswordPopUp(){
    document.getElementById("reset-password").style.display = "block";

    hideSentEmailMessage();
}

function hidePasswordPopUp() {
    document.getElementById("reset-password").style.display = "none";
}

function showSentEmailMessage(){
    document.getElementById("email-sent").style.display = "block";
}

function hideSentEmailMessage() {
    document.getElementById("email-sent").style.display = "none";
}

function resetPassword(){
    const email = document.getElementById("reset-password-email").value;

    console.log('email:' + email);

    if (email) {
        const req = new XMLHttpRequest();

        req.open("GET", "/ENI-Sortir/public/password/ajax/" + email, true);

        req.onload = onEmailSent;

        req.send();
    }
    else {
        alert('Veuillez saisir un email');
    }
}

function onEmailSent() {
    console.log('status', this.status);
    // if success
    if (this.status === 200) {
        console.log(this.responseText);

        const email = document.getElementById("reset-password-email").value;

        hidePasswordPopUp();

        const emailSent = document.getElementById("email-sent");
        if (emailSent) {
            emailSent.innerText = "Un email a été envoyé à " + email;

            showSentEmailMessage();
        };
    }
    else {
        alert('Une erreur s\'est produite. Merci de réessayer.');
    }

}
