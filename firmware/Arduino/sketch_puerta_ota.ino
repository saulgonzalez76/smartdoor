
/*Copyright (C) 2018  
 * by: Saul Gonzalez
 * email: saulgonzalez76@gmail.com
*/
#include <ESP8266WebServer.h>
#include "WiFiManagerSmartDoor.h"         
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <SoftwareSerial.h>
#include "WiFiClientSecure.h"
#include <ESP8266HTTPClient.h>
#include <ESP8266httpUpdate.h>


const int FW_VERSION = 27;   //<---------------  IMPORTANTE: Modificar la version antes de compilar y subir !!!
const int TIPO_SMART = 1;   // 1 = smartdoor, 2 = smartbrake

// servidor local de prueba
/*
const char* firmwareURL = "https://test.firmware.smartdoor.mx"; 
const char* url = "https://test.esp8266.smartdoor.mx"; 
const char* SERVER = "test.imagen.smartdoor.mx";
*/

// servidor de produccion
const char* firmwareURL = "https://firmware.smartdoor.mx"; 
const char* url = "https://esp8266.smartdoor.mx"; 
const char* SERVER = "imagen.smartdoor.mx";


const int PORT = 443;
const int PORT_CAM = 80; // puerto para imagen de camara ip

// AP defaults
const char* def_ssidWifi = "Smartdoor";
const char* def_wifiKey = "smartdoor";

String idesp = "";
long hora = millis();
long inicio_lectura = 0;
const int tiempoUpdateCheck = 1800000; // 1800000; tiempo para cada revision de software, 1 hora ( sec * 1000 )

//--- VARIABLES PARA TOMAR LA FOTO DE LA CAMARA
const String PATH = "/subir_archivos.php";
const String BOUNDARY = "----smartdoormxsmartdoormx";
const int TIMEOUT = 20000;

String imgurl = "";
String imgname = "";
//-------

long horaAbrir = millis();
const int tiempoAbrirCheck = 1000; // tiempo de espera para verificar si tiene que abrir ( sec * 1000 )

long horaResetWifi = millis();
const int tiempoResetWifi = 30000; // cada 30 seg verifico si quiere resetear el wifi

// Auxiliar variables to store the current output state
String output5State = "off";
String tmpkey = "6A4946F2B25FF41C952F3EC7EEA1D";

const int switch1 = 2;  // pin para sensor limit
int switch1_pos = 0;    // posicion inicial

WiFiClientSecure client;

String wifi_pass = "";

/*
Proxima version
  test para ver si tiene acceso a la camara ip
  agregar parametro para ver cuantas imagenes tomar de la camara
  
*/

void checkForUpdates() {
  if ((WiFi.status() == WL_CONNECTED)) {
  //  Serial.println( "Checando updates de firmware." );
    WiFiClientSecure clientFirmware;
    clientFirmware.setInsecure();
    clientFirmware.connect(firmwareURL, PORT);
    HTTPClient http;
    http.begin(clientFirmware, firmwareURL); //HTTP
    http.addHeader("Content-Type", "application/json");
    int httpCode = http.POST("{\"id\":\"" + idesp + "\",\"version\":\"" + FW_VERSION + "\"}");
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        const String& payload = http.getString();
        String urlFIRMWARE = payload;      
        urlFIRMWARE.replace("\n","");
        if (urlFIRMWARE != "") {
          //Serial.println("actualizando firmware " + urlFIRMWARE);
          t_httpUpdate_return ret = ESPhttpUpdate.update(clientFirmware, urlFIRMWARE );
  //        switch(ret) {
  //          case HTTP_UPDATE_FAILED:
              //Serial.println("error de firmware update 1");
  //            break;
  //          case HTTP_UPDATE_NO_UPDATES:
              //Serial.println("error de firmware update 2");
  //            break;
  //        }
        }
      }
    } 
    http.end();
  }
}

void checkAbrirPuerta() {
  if ((WiFi.status() == WL_CONNECTED)) {
    HTTPClient http;
    http.begin(client, url); //HTTP
    http.addHeader("Content-Type", "application/json");
    int httpCode = http.POST("{\"codigo\":\"\",\"id\":\"" + idesp + "\",\"abre\":\"1\",\"tiposmart\":\"" + TIPO_SMART + "\",\"wifi\":\"" + WiFi.RSSI() + "\",\"ssid\":\"" + WiFi.SSID() + "\",\"ssidpass\":\"" + wifi_pass + "\"}");
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        const String& payload = http.getString();
        String statusPuerta = payload;      
        statusPuerta.replace("\n","");
        if (statusPuerta != "") {
          if (getValue(statusPuerta, ';', 0).toInt() > 0){            
            //Serial.println(statusPuerta);
            int tiempo = getValue(statusPuerta, ';', 2).toInt();
            int activar_pin = getValue(statusPuerta, ';', 1).toInt();
            int idregistro = getValue(statusPuerta, ';', 4).toInt();
            imgurl = getValue(statusPuerta, ';', 3);
            imgname = getValue(statusPuerta, ';', 5);
            //Serial.println(imgurl);
            //Serial.println(imgname);
            digitalWrite(activar_pin, LOW);
            delay(tiempo);
            digitalWrite(activar_pin, HIGH);
            if (imgurl != "") {
              // eventualmente guardare la imagen de la camara aqui
              tomaFoto(idregistro);
            }
          }      
        }
      }
    } 
    http.end();  
  }
}

void checkResetWifi() {
  if ((WiFi.status() == WL_CONNECTED)) {
    HTTPClient http;
    http.begin(client, url); //HTTP
    http.addHeader("Content-Type", "application/json");
    int httpCode = http.POST("{\"codigo\":\"\",\"id\":\"" + idesp + "\",\"abre\":\"4\"}");
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        const String& payload = http.getString();
        String statusPuerta = payload;      
        statusPuerta.replace("\n","");
        if (statusPuerta != "") {
          if (statusPuerta.toInt() > 0){
            WiFiManager wifiManager;
            wifiManager.resetSettings();
            ESP.restart();
          }
        }
      }
    } 
    http.end();
  }
}

void setStatusPuerta(int status){
  if ((WiFi.status() == WL_CONNECTED)) {
    // cambiar la hora del switch y enviar el status al servidor  
    HTTPClient http;
    http.begin(client, url); //HTTP
    http.addHeader("Content-Type", "application/json");
    int httpCode = http.POST("{\"status\":\"" + (String) status + "\",\"id\":\"" + idesp + "\",\"abre\":\"3\"}");
    http.end();
    if (httpCode > 0) {
      if (httpCode != HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        switch1_pos = status;    
      }
    } else {
      switch1_pos = status;
    }
  }
}


String getValue(String data, char separator, int index)
{
    int found = 0;
    int strIndex[] = { 0, -1 };
    int maxIndex = data.length() - 1;

    for (int i = 0; i <= maxIndex && found <= index; i++) {
        if (data.charAt(i) == separator || i == maxIndex) {
            found++;
            strIndex[0] = strIndex[1] + 1;
            strIndex[1] = (i == maxIndex) ? i+1 : i;
        }
    }
    return found > index ? data.substring(strIndex[0], strIndex[1]) : "";
}

String abrePuerta(String codigoqr,int abrir){
//  Serial.println("abrir puerta");
  if ((WiFi.status() == WL_CONNECTED)) {
    HTTPClient http;
    http.begin(client, url); //HTTP
    http.addHeader("Content-Type", "application/json");
    int httpCode = http.POST("{\"codigo\":\"" + codigoqr + "\",\"id\":\"" + idesp + "\",\"abre\":\"" + abrir + "\"}");
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        const String& payload = http.getString();
        String strdatos = payload;
        //Serial.println("abrePuerta");
        //Serial.println(strdatos);
        strdatos.replace("\n","");        
        if (strdatos != ""){                
          int tiempo = getValue(strdatos, ';', 1).toInt();
          int activar_pin = getValue(strdatos, ';', 0).toInt();
          int idregistro = getValue(strdatos, ';', 3).toInt();
          imgurl = getValue(strdatos, ';', 2);
          imgname = getValue(strdatos, ';', 4);
          digitalWrite(activar_pin, LOW);
          delay(tiempo);
          digitalWrite(activar_pin, HIGH);
          if (imgurl != "") {
            // eventualmente guardare la imagen de la camara aqui
            tomaFoto(idregistro);
          }
        }
      }
    } 
    http.end();
  }
//  Serial.println("abrir puerta fin ...");
}


String header_post(size_t length)
{
  String  data;
    data =  F("POST ");
    data += PATH;
    data += F(" HTTP/1.1\r\n");
    data += F("Host: ");
    data += SERVER;
    data += F("\r\n");
    data += F("Connection: keep-alive\r\n");
    data += F("content-length: ");
    data += String(length);
    data += F("\r\n");
    data += F("User-Agent: PostmanRuntime/6.4.1\r\n");
    data += F("Content-Type: multipart/form-data; boundary=");
    data += BOUNDARY;
    data += F("\r\n");
    data += F("Accept: */*\r\n");
    data += F("Origin: http://");
    data += SERVER;
    data += F("\r\n");
    data += F("Accept-Encoding: gzip, deflate\r\n");
    data += F("Cache-Control: no-cache\r\n");
    data += F("\r\n");
    return(data);
}
String body(String content , String message)
{
  String data;
  data = "--";
  data += BOUNDARY;
  data += "\r\n";
  if(content=="imageFile")
  {
    data += "Content-Disposition: form-data; name=\"imageFile\"; filename=\"picture.jpg\"\r\n";
    data += "Content-Type: image/jpeg\r\n";
    data += "\r\n";
  }
  else
  {
    data += "Content-Disposition: form-data; name=\"" + content +"\"\r\n";
    data += "\r\n";
    data += message;
    data += "\r\n";
  }
   return(data);
}

String tomaFoto(int idregistro) {  
  //Serial.println("tomaFoto");
  if (((WiFi.status() == WL_CONNECTED)) && (imgurl != "")) {    
    String getAll;
    String getBody;
    int indice = 0;
    WiFiClient clientFoto;
    String archurl = getValue(imgurl, ',', indice);
    String archname = getValue(imgname, ',', indice);
    while (archurl != ""){

      
    
    
    //clientFoto.setInsecure();
    clientFoto.connect(archurl, PORT_CAM);
    HTTPClient http;
    //Serial.println(archurl);
    if (http.begin(clientFoto,archurl)){
      for (int v=0;v<5;v++){
        delay(100);
        //Serial.println("enviando post");
        int httpCode = http.GET();
        //Serial.println(httpCode); 
        if(httpCode > 0) {
          if(httpCode == HTTP_CODE_OK) {          
            int len = http.getSize();
            uint8_t buff[128] = { 0 };
            //Serial.println("Connection successful!");    
              String bodyTxt =  body("idregistro",(String) idregistro);
              bodyTxt +=  body("camara",archname);
              String bodyPic =  body("imageFile","1");
              String bodyEnd =  String("--")+BOUNDARY+String("--\r\n");
              size_t allLen = bodyTxt.length()+bodyPic.length()+len+bodyEnd.length();
              String headerTxt =  header_post(allLen);
            
              WiFiClient client_post;
              if (!client_post.connect(SERVER,PORT_CAM)) 
               {
                //Serial.println("Error de conexion en post");
                return("connection failed");   
               }
            //  client_post.connect(SERVER,PORT);   
              client_post.print(headerTxt+bodyTxt+bodyPic);
              WiFiClient * stream = http.getStreamPtr();
              while(http.connected() && (len > 0 || len == -1)) {
                //Serial.print(".");
                size_t size = stream->available();
                if(size) {
                  int c = stream->readBytes(buff, ((size > sizeof(buff)) ? sizeof(buff) : size));
                  client_post.write(buff,c);
                  if(len > 0) { len -= c; }
                }
                delay(1);
              }
              client_post.print("\r\n"+bodyEnd);
              //Serial.println("fin de post");
             
          }
        } else {
          Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
        }
  
  
  
      }
      http.end();
      //Serial.println("get cerrado");
    } else {
      Serial.printf("[HTTP} Unable to connect\n");
    }

    indice ++;
    archurl = getValue(imgurl, ',', indice);
    archname = getValue(imgname, ',', indice);
    }
    
  }
}

void setup() {
  Serial.begin(115200);
  //Serial.println("Iniciando...");

  idesp = WiFi.macAddress();
  // Initialize the output variables as outputs
//  pinMode(output5, OUTPUT); 
  pinMode(0, OUTPUT);
//  pinMode(1, OUTPUT); if used it will not output to monitor
  pinMode(2, OUTPUT);
//  pinMode(1, OUTPUT);
  // Set outputs to LOW
//  digitalWrite(output5, LOW);
  digitalWrite(0, HIGH);  
//  digitalWrite(1, HIGH);  if used it will not output to monitor
  digitalWrite(2, LOW);  
//  digitalWrite(1, LOW);  

  // WiFiManager
  // Local intialization. Once its business is done, there is no need to keep it around
  WiFiManager wifiManager;
  
  // Uncomment and run it once, if you want to erase all the stored information
  //wifiManager.resetSettings();
  
  // set custom ip for portal
  //wifiManager.setAPConfig(IPAddress(10,0,1,1), IPAddress(10,0,1,1), IPAddress(255,255,255,0));

  // fetches ssid and pass from eeprom and tries to connect
  // if it does not connect it starts an access point with the specified name
  // here  "AutoConnectAP"
  // and goes into a blocking loop awaiting configuration
  wifiManager.setConfigPortalTimeout(180);
  wifiManager.autoConnect(def_ssidWifi, def_wifiKey);
  // or use this for auto generated name ESP + ChipID
  //wifiManager.autoConnect();
  
  // if you get here you have connected to the WiFi
//  Serial.println("Connected.");
//  Serial.println(idesp);
  //server.begin(); 
  wifi_pass = wifiManager.getWiFiPass(true);
  
  clientConnect();
}

void clientConnect() {
  if ((WiFi.status() == WL_CONNECTED)) {
    //Serial.println("Conectando a servidor");
    client.setInsecure();
    client.connect(url, PORT);
  }
}

void loop(){
      if (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        WiFi.begin();
        delay(5000);
        clientConnect();
      }

        if (digitalRead(switch1) == HIGH) { 
          if (switch1_pos == 0){
            setStatusPuerta(0);
          }
          switch1_pos = 1;   
        } else { 
          // si esta abierta enviar codigo al servidor y cambiar la hora para no enviar cada vez
          if (switch1_pos == 1){
            setStatusPuerta(1);
          }
          switch1_pos = 0; 
        } 
    
      if (Serial.available()) {
        byte incomingData;
        String data = "";
        while(Serial.available() > 0) {
            delay(100);
            if (inicio_lectura == 0) {
              inicio_lectura = millis();
            }
            if ((millis() - inicio_lectura) > 5000){ inicio_lectura = 0; Serial.println("inibido, saliendo..."); break; }
            incomingData = Serial.read();
            data = data + " " + String(incomingData);
        }
        inicio_lectura = 0;
        if (data != ""){
          abrePuerta(data,0);
        }
      }
    
      if ((millis() - horaAbrir) > tiempoAbrirCheck){ 
        horaAbrir = millis();
        checkAbrirPuerta();
      }
    
      if ((millis() - horaResetWifi) > tiempoResetWifi){ 
        horaResetWifi = millis();
        checkResetWifi();
      }
      
      if ((millis() - hora) > tiempoUpdateCheck){ 
        hora = millis();
        checkForUpdates();
      }
}
