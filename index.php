<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Leafleat JS Reza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            width: 100%;
            height: 400px;
        }

        #container {
            display: flex;
        }

        #table-container {
            padding: 10px;
            overflow-y: auto;
            height: 600px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="alert alert-primary text-center">WEB GIS REZA</h1>
        <div class="row">
            <!-- Table Section -->
            <div class="col-md-5" id="table-container">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Longitude</th>
                            <th>Latitude</th>
                            <th>Luas</th>
                            <th>Jumlah Penduduk</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Connect to Database
                        $conn = new mysqli("localhost", "root", "", "latihan");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "SELECT * FROM penduduk";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['kecamatan']}</td>
                                    <td>{$row['longitude']}</td>
                                    <td>{$row['latitude']}</td>
                                    <td>{$row['luas']}</td>
                                    <td align='right'>{$row['jumlah_penduduk']}</td>
                                    <td>
                                        <a href='?delete_id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a> |
                                        <a href='update.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to edit this item?\");'>Edit</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak Ada Data</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Map Section -->
            <div class="col-md-7" id="map-container">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Initialize Map
        var map = L.map("map").setView([-6.9887273, 110.4242706], 13);

        // Base Map Layers
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        var rupabumiindonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Badan Informasi Geospasial'
        });

        // Control Layers
        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": Esri_WorldImagery,
            "Rupa Bumi Indonesia": rupabumiindonesia,
        };
        L.control.layers(baseMaps).addTo(map);

        // PHP-Generated Markers
        <?php
        $conn = new mysqli("localhost", "root", "", "latihan");
        $sql = "SELECT * FROM penduduk";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lat = $row['latitude'];
                $lng = $row['longitude'];
                $info = htmlspecialchars($row['kecamatan']);
                echo "L.marker([$lat, $lng]).addTo(map).bindPopup('<b>Kecamatan:</b> $info');";
            }
        }
        $conn->close();
        ?>

        // Scale Control
        L.control.scale({ position: "bottomright", imperial: false }).addTo(map);

        // Geolocation
        map.locate({ setView: true, maxZoom: 16 });

        map.on("locationfound", function (e) {
            var radius = e.accuracy / 2;
            L.marker(e.latlng).addTo(map).bindPopup("Anda berada dalam radius " + radius + " meter dari titik ini").openPopup();
            L.circle(e.latlng, radius).addTo(map);
        });

        map.on("locationerror", function (e) {
            alert(e.message);
        });

        // Watermark
        L.Control.Watermark = L.Control.extend({
            onAdd: function (map) {
                var img = L.DomUtil.create('img');
                img.src = 'Data/svugm.png'; // Ensure this path is correct
                img.style.width = '200px';
                return img;
            }
        });
        L.control.watermark({ position: 'bottomleft' }).addTo(map);
    </script>
</body>

</html>
