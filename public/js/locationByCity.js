
// todo comment faire pour afficher au chargement les infos de la ville 1 - ou alors n'avoir aucune ville sélectionnée par défaut au chargement
function getLocations() {
    // get the input from the event-city select
    let inputCity = document.getElementById("event-city");
    // create an instance to send requests to server
    let req = new XMLHttpRequest();
    // create the request
    req.open("GET", "/ENI-Sortir/public/event/add/ajax/" + inputCity.value, true);
    // method called when response is received
    req.onload = insertLocations;
    // send the request
    req.send();
}

function insertLocations() {
    console.log('status', this.status);
    console.log('responseText', this.responseText);
    // if success
    if (this.status === 200) {

        // this.responseText gets content returned by server
        const locations = JSON.parse(this.responseText);
        console.log(locations);

        let selectLocation = document.getElementById("event-location");
        selectLocation.innerHTML = "";
        let zipCodeElement = document.getElementById("event-zipcode");
        zipCodeElement.innerHTML = "";

        if (!locations && locations.length == 0) {
            selectLocation.innerHTML = "<p>Pas de lieu associé à cette ville.</p>"
            zipCodeElement.innerHTML = "<p>Pas de code postal pour ce lieu.</p>"
        }
        else {
            for (const location of locations) {
                const option = document.createElement("option");
                option.value = location.id;
                option.innerHTML = location.name;
                selectLocation.appendChild(option);
                zipCodeElement.innerHTML = location.zipcode;
            }
        }
    }
}

function getInfos(){
    // get the input from the event-locations select
    let inputLocation = document.getElementById("event-location");
    // create an instance to send requests to server
    let req = new XMLHttpRequest();
    // create the request
    req.open("GET", "/ENI-Sortir/public/event/add/ajax/location/" + inputLocation.value, true);
    // method called when response is received
    req.onload = insertInfos;
    // send the request
    req.send();
}

function insertInfos() {
    console.log('status', this.status);
    console.log('responseText', this.responseText);
    // if success
    if (this.status === 200) {
        // this.responseText gets content returned by server
        const infos = JSON.parse(this.responseText);
        console.log(infos);

        let street = document.getElementById("event-street");
        street.innerHTML = "";
        let latitude = document.getElementById("event-latitude");
        latitude.innerHTML = "";
        let longitude = document.getElementById("event-longitude");
        longitude.innerHTML = "";

        if (!infos) {
            street.innerHTML = "<p>Pas d'adresse pour ce lieu.</p>"
            latitude.innerHTML = "<p>Pas de latitude pour ce lieu.</p>"
            longitude.innerHTML = "<p>Pas de longitude pour ce lieu.</p>"
        } else {

            street.innerHTML = infos.street;
            latitude.innerHTML = infos.latitude;
            longitude.innerHTML = infos.longitude;
        }
    }
}
