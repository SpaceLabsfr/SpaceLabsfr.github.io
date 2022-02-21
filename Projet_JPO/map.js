const database = firebase.database();

/*
// Initialize Cloud Firestore through Firebase
const db = firebase.firestore();
db.settings({
    timestampsInSnapshots: true
});
*/

function get_location() {

    result = [];
    /*
    firebase.database().ref('/IPN/Coucou/longitude').set({
        test22: "test2!!"
    })
    */
    firebase.database().ref('/IPN').on('value', snapshot => {
        //console.log(typeof snapshot.val()["Coucou"]);
        //console.log(JSON.stringify(snapshot.val()));

        result = Object.assign({}, snapshot.val());
    });
    console.log(result);
    if (result != []) {
        return result;
    } else { return false; }
}
/*
var ref = firebase.database().ref('/IPN/Kévin/longitude');
ref.get().then((snapshot) => {
    if (snapshot.exists()) {
        console.log(snapshot.val());
    } else {
        console.log("No data available");
    }
}).catch((error) => {
    console.error(error);
});
*/

/*
 const countriesRef = db.collection("IPN");

 countriesRef.get()
     .then((snapshot) => {
         snapshot.docs.forEach(doc => {
             console.log(doc.data())
         })
     })
     .catch((error) => {
         console.log("Error getting countries:", error);
     });
*/
var element = document.getElementById('osm-map');
var map = L.map(element);
// To personalize :  https://leafletjs.com/examples/quick-start/ 

// Add OSM tile layer to the Leaflet map.
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);



var layerGroup = L.layerGroup().addTo(map);



function show() {

    // Where you want to render the map.

    console.log(map)

    update_location();


    /*
    if (latitude && longitude) {

        document.getElementById("bouton").style.display = 'none';
        element.style.display = 'block';
        // Target's GPS coordinates.
        var target = L.latLng(latitude_ITII, longitude_ITII);
        //var target2 = L.latLng(latitude_ITII - 0.0001, longitude_ITII + 0.0005);
        var target2 = L.latLng(latitude, longitude);

        // Set map's center to target with zoom 100.
        map.setView(target, 100);

        // To personalize :  https://leafletjs.com/examples/quick-start/ 

        // Place a marker on the same location.

        L.marker(target).addTo(map)
            .bindPopup('Stand PERF-NI')
            .openPopup();
        L.marker(target2).addTo(map)
            .bindPopup('Kévin')
            .openPopup();

    }
    */
}

function update_location() {



    // create markers

    // remove all the markers in one go
    layerGroup.clearLayers();



    console.log("Here:", L);


    document.getElementById("result").textContent = "Coordonnées : \n";

    const latitude_ITII = 49.1082 + Math.floor(Math.random() / 10);
    const longitude_ITII = 1.4977 + Math.floor(Math.random() / 10);
    var stand = L.latLng(latitude_ITII, longitude_ITII);

    values = get_location();

    Object.keys(values).forEach((person) => {
        console.log("here:", values[person]);
        if (values[person]["latitude"] && values[person]["longitude"]) {

            document.getElementById("bouton").style.display = 'none';
            element.style.display = 'block';

            // Target's GPS coordinates.
            var target = L.latLng(values[person]["latitude"], values[person]["longitude"]);


            // Place a marker on the location

            //L.marker(target).addTo(map)
            L.marker(target).addTo(layerGroup)
                .bindPopup(person)
                .openPopup();
        }

        values[person]["latitude"] = Math.round(values[person]["latitude"] * 100) / 100;
        values[person]["altitude"] = Math.round(values[person]["altitude"] * 100) / 100;
        values[person]["longitude"] = Math.round(values[person]["longitude"] * 100) / 100;

        document.getElementById("result").textContent += person + " : (alt:" + values[person]["altitude"] + ", lat:" + values[person]["latitude"] + ", long:" + values[person]["longitude"] + ")";

        // Set map's center to target with zoom 100.
        map.setView(stand, 100);

        L.marker(stand).addTo(map)
        L.marker(stand).addTo(layerGroup)
            .bindPopup('Stand PERF-NI')
            .openPopup();


    });

}


setInterval(function() {
    update_location();
    console.log("reset");
}, 5000);