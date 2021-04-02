
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

        if (!locations && locations.length == 0) {
            selectLocation.innerHTML = "<p>Pas de lieu associé à cette ville</p>"
        }
        else {
            for (const location of locations) {
                const option = document.createElement("option");
                option.value = location.id;
                option.innerHTML = location.name;
                selectLocation.appendChild(option);
            }
        }
    }
}
