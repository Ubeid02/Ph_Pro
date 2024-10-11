<?php
include '../koneksi.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Logika untuk menangani permintaan AJAX
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $sql2 = mysqli_query($conn, "SELECT * FROM ph_pro ORDER BY id DESC LIMIT 1");
    $data2 = mysqli_fetch_array($sql2);
    echo $data2['valuess'];
    exit;
}

// Mendapatkan data terbaru
$sql2 = mysqli_query($conn, "SELECT * FROM ph_pro ORDER BY id DESC LIMIT 1");
$data2 = mysqli_fetch_array($sql2);

$valuess = $data2['valuess'];
$dates = $data2['dates'];
$timeses = $data2['timeses'];

// Menghitung akurasi
$sql_accuracy = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(CASE WHEN valuess BETWEEN 6 AND 8 THEN 1 ELSE 0 END) as correct FROM ph_pro");
$data_accuracy = mysqli_fetch_array($sql_accuracy);

$total = $data_accuracy['total'];
$correct = $data_accuracy['correct'];
$accuracy = $total ? ($correct / $total) * 100 : 0;

// Menghitung rata-rata pH per jam
$sql_avg = mysqli_query($conn, "SELECT HOUR(timeses) as hour, AVG(valuess) as avg_ph FROM ph_pro GROUP BY HOUR(timeses)");
$hourly_ph = [];
while ($row = mysqli_fetch_assoc($sql_avg)) {
    $hourly_ph[] = ['hour' => $row['hour'], 'avg_ph' => $row['avg_ph']];
}

// Menghitung rata-rata pH harian
$sql_daily_avg = mysqli_query($conn, "SELECT DATE(dates) as date, AVG(valuess) as avg_ph FROM ph_pro GROUP BY DATE(dates) ORDER BY date DESC");
$daily_ph = [];
while ($row = mysqli_fetch_assoc($sql_daily_avg)) {
    $daily_ph[] = ['date' => $row['date'], 'avg_ph' => $row['avg_ph']];
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>pH Pro</title>
    <link rel="icon" href="../img/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="content bg-secondary text-white" style="--bs-bg-opacity: .2;">
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="title" href="">pH Pro</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="text-white fs-5 fw-semibold" aria-current="page" href="#">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="text-white fs-5 fw-semibold" href="../about/about.html">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="text-white fs-5 fw-semibold" href="../landingPage/landingPage.html">Home</a>
                        <li class="nav-user">
                            <a class="nav-link user" href="#">
                                <img src="../img/Groupprofile.png" alt="">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="container">
            <!-- Konten di sisi kiri -->
            <div class="leftSide">
                <div class="device">
                    <div class="dropdown">
                        <label class="Device" for="">
                            <select name="device" id="">
                                <option value="1">device 1</option>
                                <option value="2">device 2</option>
                                <option value="3">device 3</option>
                                <option value="4">device 4</option>
                                <option value="5">device 5</option>
                            </select>
                        </label>
                    </div>
    
                    <div class="image">
                        <img src="../img/microcontroller.png" alt="">
                    </div>
    
                    <div class="radioCheck">
                        <div class="form-check form-check-inline Disconnected">
                            <label class="form-check-label" for="Disconnected">
                                <input class="form-check-input" type="radio" name="Connection" id="Disconnected">
                                <span class="radio-btn"></span>
                                <span class="text">Disconnected</span>
                            </label>
                        </div>
                        <div class="form-check form-check-inline Connected">
                            <label class="form-check-label" for="Connected">
                                <input class="form-check-input" type="radio" name="Connection" id="Connected" checked>
                                <span class="radio-btn"></span>
                                <span class="text">Connected</span>
                            </label>
                        </div>
                    </div>
                </div>
        
                <div class="accuracy">
                    <span class="subtitle"><h3>ACCURACY</h3></span>
                    <div class="icon">
                        <img src="../img/accuracy.png" alt="Accuracy Icon">
                    </div>
                    <div class="accValue">
                        <span class="value"><h1><?php echo round($accuracy, 2) ?>%</h1></span>
                    </div>
                </div>
    
                <div class="waterPh">
                    <span class="subtitle"><h3>WATER pH</h3></span>
                    <div class="icon">
                        <img src="../img/value.png" alt="Value Icon">
                    </div>
                    <div class="phValue">
                        <span class="value"><h1 id="phValue"><?php echo $valuess ?> pH</h1></span>
                    </div>
                </div>
            </div>
            
            <!-- Konten di sisi kanan -->
            <div class="rightSide">
                <div class="trendsPh">
                    <span class="subtitle"><h3>pH TRENDS (Hours)</h3></span>
                    <div class="graph">
                        <canvas id="waterPHChart"></canvas>
                    </div>
                </div>

                <div class="average">
                    <span class="subtitle"><h3>AVERAGE pH (Days)</h3></span>
                    <div class="tableAVG">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sorting Date">Date</th>
                                    <th class="sorting Value">Average pH Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daily_ph as $daily) { ?>
                                    <tr>
                                        <td class="date"><?php echo $daily['date']; ?></td>
                                        <td class="ph"><?php echo round($daily['avg_ph'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('waterPHChart').getContext('2d');
        const waterPHChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($hourly_ph, 'hour')); ?>, // Label jam
                datasets: [{
                    label: 'Average pH per Hour',
                    data: <?php echo json_encode(array_column($hourly_ph, 'avg_ph')); ?>, // Data rata-rata pH
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 14,
                        beginAtZero: true,
                        ticks: {
                            autoSkip: true,
                            font: {
                                size: 10,
                            }
                        },
                        title: {
                            display: true,
                            text: 'pH'
                        }
                    },
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            autoSkip: true,
                            maxRotation: 0,
                            font: {
                                size: 10,
                            }
                        },
                        title: {
                            display: true,
                            text: 'Hour'
                        }
                    }
                }
            }
        });

        function updatePHValue() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("phValue").innerText = this.responseText + " pH"; // Hanya update nilai tanpa mengubah elemen lain
                }
            };
            xhttp.open("GET", "", true); // Hapus URL get_latest_ph.php karena permintaan AJAX ditangani oleh file yang sama
            xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest"); // Tambahkan header untuk menandai permintaan AJAX
            xhttp.send();
        }

        // Panggil fungsi update setiap 5 detik (misalnya)
        setInterval(updatePHValue, 5000); // 5000 milidetik = 5 detik
    </script>
</body>
</html>
