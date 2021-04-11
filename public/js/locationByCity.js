function getLocations() {
    // get the input from the event-city select
    let inputCity = document.getElementById("event-city");

    // initialize fields to empty
    let zipCode = document.getElementById("event-zipcode");
    zipCode.innerHTML = "";
    let selectLocation = document.getElementById("event-location");
    selectLocation.innerHTML = "";
    let street = document.getElementById("event-street");
    street.innerHTML = "";
    let latitude = document.getElementById("event-latitude");
    latitude.innerHTML = "";
    let longitude = document.getElementById("event-longitude");
    longitude.innerHTML = "";
    let plusBtnElement = document.getElementById("plusBtn");
    plusBtnElement.style.display = "none";
    let hiddenCityInput = document.getElementById('hidden-city');


    // if a city is selected, send its id to controller and call insertion of associated locations
    if (inputCity.value !== "") {
        //display add location button (+)
        plusBtnElement.style.display = "flex";
        hiddenCityInput.value = inputCity.value;
        console.log(hiddenCityInput.value);
        // create an instance to send requests to server
        let req = new XMLHttpRequest();
        // create the request
        req.open("GET", "/ENI-Sortir/public/event/add/ajax/" + inputCity.value, true);
        // method called when response is received
        req.onload = insertLocations;
        // send the request
        req.send();
    }
}

function insertLocations() {
    console.log('status', this.status);
    console.log('responseText', this.responseText);
    // if success
    if (this.status === 200) {

        // this.responseText gets content returned by server
        const locations = JSON.parse(this.responseText);
        console.log(locations);

        // initialize fields to empty
        let selectLocation = document.getElementById("event-location");
        selectLocation.innerHTML = "";
        let zipCodeElement = document.getElementById("event-zipcode");
        zipCodeElement.innerHTML = "";
        let street = document.getElementById("event-street");
        street.innerHTML = "";
        let latitude = document.getElementById("event-latitude");
        latitude.innerHTML = "";
        let longitude = document.getElementById("event-longitude");
        longitude.innerHTML = "";


        if (!locations && locations.length === 0) {
            selectLocation.innerHTML = "<p>Pas de lieu associé à cette ville.</p>"
            zipCodeElement.innerHTML = "<p>Pas de code postal pour ce lieu.</p>"
        }
        // if a city is selected and location infos found in base, display infos
        else {
            const option = document.createElement("option");
            option.value = "";
            option.innerHTML = "Sélectionnez un lieu";
            selectLocation.appendChild(option);
            let eventLocationId = document.getElementById('event-location-id');
            for (const location of locations) {
                if (location.id === eventLocationId) {

                } else {
                    const option = document.createElement("option");
                    option.value = location.id;
                    option.innerHTML = location.name;
                    selectLocation.appendChild(option);
                }
                zipCodeElement.innerHTML = location.zipcode;
            }
        }
    }
}

function getInfos() {
    // get the input from the event-locations select
    let inputLocation = document.getElementById("event-location");

    // initializing fields to empty
    let zipCode = document.getElementById("event-zipcode");
    //zipCode.innerHTML = "";
    let street = document.getElementById("event-street");
    street.innerHTML = "";
    let latitude = document.getElementById("event-latitude");
    latitude.innerHTML = "";
    let longitude = document.getElementById("event-longitude");
    longitude.innerHTML = "";

    if (inputLocation.value !== "") {
        // create an instance to send requests to server
        let req = new XMLHttpRequest();
        // create the request
        req.open("GET", "/ENI-Sortir/public/event/add/ajax/location/" + inputLocation.value, true);
        // method called when response is received
        req.onload = insertInfos;
        // send the request
        req.send();
    }
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

function showAddLocation(event) {

    event.preventDefault();
    document.getElementById('hidden-city').value = document.getElementById('event-city').value;
    document.getElementById("collapsible-content").style.display = "block";
    document.getElementById("location_name").attributes.required = "true";
    document.getElementById("location_street").attributes.required = "true";
    document.getElementById("location_latitude").attributes.required = "true";
    document.getElementById("location_longitude").attributes.required = "true";

}

function hideAddLocation() {
    document.getElementById("collapsible-content").style.display = "none";
    document.getElementById("location_name").attributes.required = "false";
    document.getElementById("location_street").attributes.required = "false";
    document.getElementById("location_latitude").attributes.required = "false";
    document.getElementById("location_longitude").attributes.required = "false";
}
