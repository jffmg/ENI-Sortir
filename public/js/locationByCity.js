
function getLocations() {
    // get the input from the event-city select
    let inputCity = document.getElementById("event-city");
    // create an instance to send requests to server
    let req = new XMLHttpRequest();
    // create the request
    req.open("GET", "/ENI-Sortir/public/event/add/ajax" + inputCity.value, true);
    // method called when response is received
    req.onload = insertLocations();
    // send the request
    req.send();
}

function insertLocations() {
    // if success
    if (this.status === 200) {
        // this.responseText gets content returned by server
        let locations = this.responseText.split("|");
        let selectLocation = document.getElementById("event-location");
        selectLocation.innerHTML = "";
        if (locations[0] === "") {
            selectLocation.innerHTML = "<p>Pas de lieu associé à cette ville</p>"
        } else {
            for (l of locations) {
                let optionLocation = document.createElement("option");
                optionLocation.value = l.id;
                optionLocation.innerHTML = l.name;
                selectLocation.appendChild(optionLocation);
            }
        }
    }
}
