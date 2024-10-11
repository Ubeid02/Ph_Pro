// Include libraries
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <SoftwareSerial.h>

// Network SSID
const char* ssid = "xxxxxxx";
const char* pass = "xxxxxx";

// IP Server laptop
const char* host = "192.xxx.xx.xxx";

// Software serial for communication with Arduino
SoftwareSerial mySerial(D7, D8); // RX, TX

void setup() {
  // Initialize serial communication
  Serial.begin(9600);
  mySerial.begin(9600);

  // Connect to WiFi
  WiFi.hostname("NodeMCU");
  WiFi.begin(ssid, pass);

  // Check WiFi connection
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }

  Serial.println("Connected to WiFi");
}

void loop() {
  // Connect to server (web server)
  WiFiClient client;
  const int httpPort = 80;

  // Test connection to server
  if (!client.connect(host, httpPort)) {
    Serial.println("Connection to server failed");
    return;
  }
  Serial.println("Connected to server");

  // If data is available from Arduino
  if (mySerial.available()) {
    String receivedData = mySerial.readStringUntil('\n');
    Serial.println("Received data: " + receivedData);

    // Parse pH value and accuracy
    int commaIndex = receivedData.indexOf(',');
    if (commaIndex != -1) {
      String pHValue = receivedData.substring(0, commaIndex);
      String accuracyValue = receivedData.substring(commaIndex + 1);

      // Create data to send to server
      String postData = "valuess=" + pHValue + "&accuracy=" + accuracyValue;

      // Send data to server
      HTTPClient http;
      String url = "http://" + String(host) + "/ph_pro/post_sensor.php";
      http.begin(client, url);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Specify content-type header

      // Read response
      int httpCode = http.POST(postData); // Send the request
      String payload = http.getString();  // Get the response payload

      Serial.println(httpCode); // Print HTTP return code
      Serial.println(payload);  // Print request response payload
      http.end();
    } else {
      Serial.println("Error: Invalid data received");
    }
  }

  // delay(1000); // Wait 60 seconds before the next measurement
}