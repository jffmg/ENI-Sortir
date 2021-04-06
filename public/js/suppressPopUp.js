
function showSuppressPopUp(event){
    event.preventDefault();
    document.getElementById("suppress-participants").style.display = "block";
}

function hideSuppressPopUp() {
    document.getElementById("suppress-participants").style.display = "none";
}

function suppressParticipants(){
    console.log('Suppress Participants function');

    const form = document.getElementById("form-manage-participants");

    const input = document.createElement("input");
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', 'delete');
    input.setAttribute('value', 'OK');
    form.appendChild(input);

    form.submit();

    document.getElementById("suppress-participants").style.display = "none";
}

