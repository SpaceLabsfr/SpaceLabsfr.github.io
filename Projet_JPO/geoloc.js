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
        document.getElementById("result").textContent = JSON.stringify(snapshot.val());

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

function show() {

    if (get_location() != false) {

        // Where you want to render the map.
        var element = document.getElementById('osm-map');

        // Create Leaflet map on map element.
        var map = L.map(element);

        // Add OSM tile layer to the Leaflet map.
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        values = get_location()["Kévin"];

        const latitude = values["latitude"];
        const longitude = values["longitude"];

        const latitude_ITII = 49.1082;
        const longitude_ITII = 1.4977;

        console.log(latitude);
        console.log(longitude);

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
    }
}