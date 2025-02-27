<?php
// API anahtarınızı buraya ekleyin
$apiKey = ""; // OpenWeatherMap API anahtarınızı buraya yazın
$defaultCity = "İstanbul"; // Varsayılan şehir

// Formdan şehir bilgisi alınıyor
$city = isset($_POST['city']) ? $_POST['city'] : $defaultCity;

// API'den veri çekme
function getWeatherData($city, $apiKey) {
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&units=metric&lang=tr&appid=" . $apiKey;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Forecast verisi için
function getForecastData($city, $apiKey) {
    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($city) . "&units=metric&lang=tr&appid=" . $apiKey;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

$weatherData = getWeatherData($city, $apiKey);
$forecastData = getForecastData($city, $apiKey);

// Hava durumuna göre arka plan sınıfı belirleme
$backgroundClass = "clear-day";
if (isset($weatherData['weather'][0]['main'])) {
    $weatherMain = strtolower($weatherData['weather'][0]['main']);
    
    if (strpos($weatherMain, 'cloud') !== false) {
        $backgroundClass = "cloudy";
    } elseif (strpos($weatherMain, 'rain') !== false) {
        $backgroundClass = "rainy";
    } elseif (strpos($weatherMain, 'snow') !== false) {
        $backgroundClass = "snowy";
    } elseif (strpos($weatherMain, 'thunder') !== false) {
        $backgroundClass = "stormy";
    } elseif (strpos($weatherMain, 'mist') !== false || strpos($weatherMain, 'fog') !== false) {
        $backgroundClass = "foggy";
    }
}

// Haftanın günleri Türkçe
$days = ["Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi", "Pazar"];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hava Durumu - <?php echo isset($weatherData['name']) ? $weatherData['name'] : 'Bulunamadı'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="/project/hava-durumu/favicon.png" rel="icon">
  <link href="/project/hava-durumu/favicon.png" rel="apple-touch-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #5da4d9, #fff);
            color: #333;
            transition: background 1.5s ease;
            overflow-x: hidden;
        }
        
        /* Arka plan animasyonları için */
        .weather-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        /* Güneşli gün animasyonu */
        .clear-day {
            background: linear-gradient(180deg, #87CEEB, #ADD8E6);
        }
        
        .clear-day::before {
            content: '';
            position: absolute;
            top: 50px;
            right: 100px;
            width: 80px;
            height: 80px;
            background: #FDB813;
            border-radius: 50%;
            box-shadow: 0 0 40px #FDB813;
            animation: sun-pulse 3s infinite alternate;
        }
        
        @keyframes sun-pulse {
            0% { transform: scale(1); box-shadow: 0 0 40px #FDB813; }
            100% { transform: scale(1.1); box-shadow: 0 0 60px #FDB813; }
        }
        
        /* Bulutlu gün animasyonu */
        .cloudy {
            background: linear-gradient(180deg, #6a85b6, #bac8e0);
        }
        
        .cloudy::before,
        .cloudy::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.8);
            animation: cloud-move 15s infinite linear;
        }
        
        .cloudy::before {
            width: 100px;
            height: 50px;
            top: 80px;
            left: -100px;
        }
        
        .cloudy::after {
            width: 160px;
            height: 80px;
            top: 120px;
            left: -160px;
            animation-delay: 3s;
        }
        
        @keyframes cloud-move {
            0% { left: -200px; }
            100% { left: 100%; }
        }
        
        /* Yağmurlu gün animasyonu */
        .rainy {
            background: linear-gradient(180deg, #4a6b8a, #84a0b5);
        }
        
        .rainy::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: transparent;
            animation: rain 1s infinite linear;
        }
        
        @keyframes rain {
            0% {
                background-image: 
                    radial-gradient(circle at 20% 10%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 50% 30%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 70% 15%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.6) 1px, transparent 1px);
                background-size: 100% 100%;
                background-position: 0 0;
            }
            100% {
                background-image: 
                    radial-gradient(circle at 20% 10%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 50% 30%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 70% 15%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.6) 1px, transparent 1px),
                    radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.6) 1px, transparent 1px);
                background-size: 100% 100%;
                background-position: 0 20px;
            }
        }
        
        /* Karlı gün animasyonu */
        .snowy {
            background: linear-gradient(180deg, #b3c5d7, #d6e5f3);
        }
        
        .snowy::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: transparent;
            animation: snow 10s infinite linear;
        }
        
        @keyframes snow {
            0% {
                background-image: 
                    radial-gradient(circle at 20% 10%, white 1px, transparent 1px),
                    radial-gradient(circle at 50% 30%, white 2px, transparent 2px),
                    radial-gradient(circle at 70% 15%, white 1px, transparent 1px),
                    radial-gradient(circle at 30% 40%, white 2px, transparent 2px),
                    radial-gradient(circle at 80% 50%, white 1px, transparent 1px);
                background-size: 100px 100px;
                background-position: 0 0;
            }
            100% {
                background-image: 
                    radial-gradient(circle at 20% 10%, white 1px, transparent 1px),
                    radial-gradient(circle at 50% 30%, white 2px, transparent 2px),
                    radial-gradient(circle at 70% 15%, white 1px, transparent 1px),
                    radial-gradient(circle at 30% 40%, white 2px, transparent 2px),
                    radial-gradient(circle at 80% 50%, white 1px, transparent 1px);
                background-size: 100px 100px;
                background-position: 50px 50px;
            }
        }
        
        /* Fırtınalı gün animasyonu */
        .stormy {
            background: linear-gradient(180deg, #2c3e50, #4a5c6b);
        }
        
        .stormy::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: transparent;
            animation: lightning 5s infinite;
        }
        
        @keyframes lightning {
            0%, 95%, 98% {
                background-color: transparent;
            }
            96%, 99% {
                background-color: rgba(255, 255, 255, 0.2);
            }
            97%, 100% {
                background-color: transparent;
            }
        }
        
        /* Sisli gün animasyonu */
        .foggy {
            background: linear-gradient(180deg, #93a5b7, #b7c4d1);
        }
        
        .foggy::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            filter: blur(10px);
            animation: fog-move 20s infinite alternate;
        }
        
        @keyframes fog-move {
            0% { transform: translateX(-5%) translateY(0); }
            100% { transform: translateX(5%) translateY(0); }
        }
        
        /* Ana içerik */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 1.8rem;
            color: #2c3e50;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
        }
        
        .search-form input {
            padding: 10px 15px;
            border: none;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            outline: none;
            font-size: 1rem;
        }
        
        .search-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            background: #3498db;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1rem;
        }
        
        .search-form button:hover {
            background: #2980b9;
        }
        
        .current-weather {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .weather-main {
            flex: 1;
            min-width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .temp {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .weather-description {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #34495e;
        }
        
        .weather-icon {
            width: 50px;
            height: 50px;
        }
        
        .weather-details {
            flex: 1;
            min-width: 250px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-item i {
            color: #3498db;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        
        .forecast {
            margin-top: 30px;
        }
        
        .forecast h2 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .forecast-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .forecast-card {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .forecast-card:hover {
            transform: translateY(-5px);
        }
        
        .forecast-card .day {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .forecast-card .forecast-temp {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 10px 0;
            color: #2c3e50;
        }
        
        .forecast-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .search-form {
                width: 100%;
            }
            
            .search-form input {
                flex: 1;
            }
            
            .current-weather {
                flex-direction: column;
                text-align: center;
            }
            
            .weather-details {
                grid-template-columns: 1fr;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="weather-background <?php echo $backgroundClass; ?>"></div>
    
    <div class="container">
        <header>
            <h1>Hava Durumu</h1>
            <form method="post" class="search-form">
                <input type="text" name="city" placeholder="Şehir adı girin..." value="<?php echo $city; ?>" required>
                <button type="submit"><i class="fas fa-search"></i> Ara</button>
            </form>
        </header>
        
        <main>
            <?php if (isset($weatherData['cod']) && $weatherData['cod'] == 200): ?>
                <div class="current-weather">
                    <div class="weather-main">
                        <div class="location">
                            <h2><?php echo $weatherData['name']; ?>, <?php echo isset($weatherData['sys']['country']) ? $weatherData['sys']['country'] : ''; ?></h2>
                            <p><?php echo date('d M Y, H:i'); ?></p>
                        </div>
                        <div class="temp"><?php echo round($weatherData['main']['temp']); ?>°C</div>
                        <div class="weather-description">
                            <img class="weather-icon" src="https://openweathermap.org/img/wn/<?php echo $weatherData['weather'][0]['icon']; ?>@2x.png" alt="<?php echo $weatherData['weather'][0]['description']; ?>">
                            <span><?php echo ucfirst($weatherData['weather'][0]['description']); ?></span>
                        </div>
                        <div class="temp-minmax">
                            <span>Min: <?php echo round($weatherData['main']['temp_min']); ?>°C</span> | 
                            <span>Max: <?php echo round($weatherData['main']['temp_max']); ?>°C</span>
                        </div>
                    </div>
                    
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div>
                                <p>Rüzgar</p>
                                <p><?php echo round($weatherData['wind']['speed']); ?> m/s</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div>
                                <p>Nem</p>
                                <p><?php echo $weatherData['main']['humidity']; ?>%</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-compress-alt"></i>
                            <div>
                                <p>Basınç</p>
                                <p><?php echo $weatherData['main']['pressure']; ?> hPa</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-eye"></i>
                            <div>
                                <p>Görüş Mesafesi</p>
                                <p><?php echo isset($weatherData['visibility']) ? round($weatherData['visibility'] / 1000, 1) : '-'; ?> km</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="forecast">
                    <h2>5 Günlük Tahmin</h2>
                    <div class="forecast-container">
                        <?php 
                        if (isset($forecastData['list'])) {
                            $processedDays = [];
                            $count = 0;
                            
                            foreach ($forecastData['list'] as $forecast) {
                                $date = new DateTime("@" . $forecast['dt']);
                                $day = $days[$date->format('w')];
                                
                                // Her gün için sadece bir tahmin göster (öğlen vakti)
                                if (!in_array($day, $processedDays) && $date->format('H') >= 12 && $date->format('H') <= 15) {
                                    $processedDays[] = $day;
                                    $count++;
                                    
                                    // 5 günden fazla gösterme
                                    if ($count > 5) break;
                        ?>
                        <div class="forecast-card">
                            <div class="day"><?php echo $day; ?></div>
                            <div class="date"><?php echo $date->format('d.m.Y'); ?></div>
                            <img class="forecast-icon" src="https://openweathermap.org/img/wn/<?php echo $forecast['weather'][0]['icon']; ?>@2x.png" alt="<?php echo $forecast['weather'][0]['description']; ?>">
                            <div class="forecast-description"><?php echo ucfirst($forecast['weather'][0]['description']); ?></div>
                            <div class="forecast-temp"><?php echo round($forecast['main']['temp']); ?>°C</div>
                            <div class="forecast-details">
                                <div><?php echo $forecast['main']['humidity']; ?>% Nem</div>
                                <div><?php echo round($forecast['wind']['speed']); ?> m/s Rüzgar</div>
                            </div>
                        </div>
                        <?php 
                                }
                            }
                        } 
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-message">
                    <h2>Hava durumu bilgisi bulunamadı</h2>
                    <p>Lütfen geçerli bir şehir adı girdiğinizden emin olun.</p>
                </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Hava Durumu Websitesi <br> Designed by <a href="https://www.ilkinheydarov.com.tr/" style="text-decoration: none; color: #34495e;" target="_blank">Mr İlkin</a></p>
        </footer>
    </div>
</body>
</html>
