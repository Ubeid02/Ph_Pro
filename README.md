# Aquarium pH Monitoring and Control System

This project is a **pH Monitoring and Control System** designed for aquariums to maintain optimal water pH levels. The system uses a water pH sensor integrated with an **Arduino** and **ESP8266** to monitor real-time pH values and regulate the pH by automatically adjusting it with acidic or basic solutions. The data is displayed on a **web interface**, which allows users to track pH levels in real-time, view daily and weekly averages, and monitor the system’s performance.

## 📜 Features

- **Real-time pH Monitoring**: Displays current pH values of the aquarium on a web interface.
- **Automated pH Adjustment**: Two DC motors are activated to inject either acidic or basic solutions when pH deviates from the desired range.
- **Historical Data**: View average pH levels daily and weekly on the dashboard.
- **Web-based Interface**: Provides a responsive web dashboard to monitor pH levels and system status remotely.
- **Data Logging**: Logs sensor data in a database, allowing historical analysis and troubleshooting.

## 🛠️ System Components

1. **Arduino**:
   - Equipped with a **pH sensor** to read water pH levels.
   - Controls **two DC motors** for adjusting pH levels. One motor adds acidic solution, and the other adds a basic solution when needed.
   - Sends sensor data via **UART (RX/TX)** to the ESP8266.

2. **ESP8266**:
   - Receives data from Arduino through UART communication.
   - Sends the data to a database via **HTTPS protocol** for real-time monitoring on the web interface.

3. **pH Sensor**: Measures the pH level of the water.
4. **DC Motors**: Operates to add acidic or basic solutions to adjust the water pH when it goes beyond the threshold.
5. **Web Dashboard**: Displays real-time and historical pH data and alerts for any abnormalities.

## 🚧 Why Use Arduino and ESP8266?

Initially, we attempted to use only the **ESP8266** for reading pH values, but encountered significant calibration issues. The calibration process was unreliable, and accurate sensor readings were difficult to achieve. After extensive testing, we found that the ESP8266 alone struggled to maintain calibration accuracy. Therefore, we opted to use **Arduino** to handle the sensor readings due to its more consistent and reliable performance in this regard. The ESP8266 is then utilized solely for wireless communication and sending data to the database via HTTPS.

## 🚀 Getting Started

### Prerequisites

- **Arduino IDE** for programming the Arduino and ESP8266.
- **pH sensor** for water quality measurement.
- **DC motors** for injecting solutions.
- **Web server** and **database** (e.g., MySQL or Firebase) for logging and displaying data.

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Ubeid02/Ph_Pro.git
   
2. Install necessary libraries for Arduino and ESP8266:
  - Install the pH sensor library in the Arduino IDE.
  - Install the ESP8266WiFi library for connecting to Wi-Fi and HTTPS.

3. Upload the Arduino code:
  - Ensure the pH sensor and motors are correctly wired to the Arduino.
  - Flash the code to both the Arduino and ESP8266.
  - Set up your web server and database to receive and display the sensor data.

## ⚙️ System Flow
1. Sensor Reading: The pH sensor connected to the Arduino continuously monitors the water pH levels.
2. UART Communication: The Arduino sends the sensor data to the ESP8266 through UART communication (TX/RX pins).
3. Data Transmission: The ESP8266 forwards the data to the web server via HTTPS.
4. Automated Control: If the pH value is outside the predefined range, the Arduino triggers the corresponding DC motor to inject either acidic or basic solution into the water.
5. Web Interface: The pH data is displayed on the web interface, allowing real-time monitoring and viewing of historical data.

## 🔧 Calibration and Setup Notes
  - During initial tests with the ESP8266, we faced significant issues with pH sensor calibration, resulting in inaccurate readings. After switching to the Arduino for sensor readings, calibration became stable, and accurate measurements were consistently achieved.
  - The ESP8266 is used strictly for communication, leaving the sensor data processing to the more reliable Arduino.
