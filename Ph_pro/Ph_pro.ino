#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Arduino.h>

// Pin sensor pH
const int ph_Pin = A0;

// Variabel kalibrasi
float Po = 0;
float PH_step;
int nilai_analog_PH;
double TeganganPh;

// Nilai pH kalibrasi
float PH4 = 4.28;
float PH7 = 3.42;

// Pengaturan I2C LCD
const int lcd_address = 0x27;
const int lcd_rows = 2;
const int lcd_columns = 16;

LiquidCrystal_I2C lcd(lcd_address, lcd_rows, lcd_columns);

// Pin relay
const int relay1Pin = 6;
const int relay2Pin = 7;

void setup() {
  // Inisialisasi serial monitor
  Serial.begin(9600);

  // Inisialisasi LCD
  lcd.init();
  lcd.backlight();

  // Inisialisasi pin relay sebagai output
  pinMode(relay1Pin, OUTPUT);
  pinMode(relay2Pin, OUTPUT);

  // Matikan relay pada awal
  digitalWrite(relay1Pin, HIGH);
  digitalWrite(relay2Pin, HIGH);
}

void loop() {
  // Baca nilai analog sensor pH
  nilai_analog_PH = analogRead(ph_Pin);

  // Hitung tegangan pH
  TeganganPh = 5.0 / 1024.0 * nilai_analog_PH;

  // Hitung nilai pH
  PH_step = (PH4 - PH7) / 3.0;
  Po = 7.00 + ((PH7 - TeganganPh)
   / PH_step);

  // Tampilkan nilai pH di Serial Monitor dan LCD
  // Serial.print("Tegangan Ph: ");
  // Serial.print(TeganganPh, 3);
  // Serial.print(" pH: ");
  // Serial.println(Po, 2);

  lcd.setCursor(2, 0);
  lcd.print("Nilai pH Air: ");
  lcd.setCursor(6, 1);
  lcd.print(Po, 2);

  if (Po >= 0 && Po <= 5.90) {
    // Menyalakan MotorDC1 (Relay1 menyala, Relay2 mati)
    digitalWrite(relay1Pin, LOW);
    digitalWrite(relay2Pin, HIGH);
    delay(7000);
    digitalWrite(relay1Pin, HIGH);
  } else if (Po >= 6.00 && Po <= 7.00) {
    // Mematikan kedua motor (Relay1 mati, Relay2 mati)
    digitalWrite(relay1Pin, HIGH);
    digitalWrite(relay2Pin, HIGH);
  } else if (Po >= 7.10 && Po <= 14.00) {
    // Menyalakan MotorDC2 (Relay2 menyala, Relay1 mati)
    digitalWrite(relay1Pin, HIGH);
    digitalWrite(relay2Pin, LOW);
    delay(7000);
    digitalWrite(relay2Pin, HIGH);
  }

  float accuracy1 = 100 - ((fabs(Po - PH4) / PH4) * 100);
  float accuracy2 = 100 - ((fabs(Po - PH7) / PH7) * 100);
  float overall_accuracy = (accuracy1 + accuracy2) / 2;

  // Serial.print(" ");o
  // Serial.println(overall_accuracy);

  // Kirim nilai pH dan akurasi melalui komunikasi serial
  Serial.print(Po, 2);
  Serial.print(",");
  Serial.println(overall_accuracy);

  delay(60000); // Tunggu 10 detik sebelum pengukuran berikutnya
}