import requests
import time
import json
import random
from datetime import datetime


#Fácil mi papacho, no haga sino leer que está todo explicadito ahh buenito

# --- CONFIGURACIÓN GENERAL DEL DISPOSITIVO ---

# AJUSTAR API
API_URL = "http://127.0.0.1:8000/api/device-data"

# DATOS DEL DISPOSITIVO (MODIFICAR PARA CADA NUEVO DISPOSITIVO)
DEVICE_ID = "temp1"
DEVICE_TOKEN = "pltZjBRBQN533Wn1"
# Variables para cada dispositivo
DEVICE_TYPE = "temperature" # Ejemplo: "humidity", "pressure", "light"
DEVICE_UNIT = "°C"          # Ejemplo: "%", "hPa", "lux"

# Establecer valores Mínimos y Máximos
VAL_MIN = 10
VAL_MAX = 80

# INTERVALO DE ENVÍO EN SEGUNDOS
INTERVALO = 5

# --- FIN DE LA CONFIGURACIÓN ---


def send_data(value):
    """
    Prepara y envía los datos del dispositivo a la API.
    """
    #Parámetros
    payload = {
        "device_id": DEVICE_ID,
        "type": DEVICE_TYPE,
        "value": value,
        "unit": DEVICE_UNIT,
        "timestamp": datetime.now().isoformat()
    }

    headers = {
        "Content-Type": "application/json",
        "X-DEVICE-TOKEN": DEVICE_TOKEN
    }

    print(f"📤 ENVIANDO PAYLOAD: {json.dumps(payload, indent=2)}")
    
    try:
        response = requests.post(API_URL, data=json.dumps(payload), headers=headers)
        print(f"📥 RESPUESTA: {response.status_code} - {response.text}")
        
        # Verificar si se guardó correctamente
        if response.status_code == 201:
            response_data = response.json()
            if response_data.get('stored'):
                print("✅ Dato guardado correctamente en la base de datos")
            else:
                print("❌ Error: El dato no se guardó")
        else:
            print(f"❌ Error HTTP: {response.status_code}")
            
    except Exception as e:
        print(f"❌ ERROR de conexión: {e}")
    
    print("-" * 50)

if __name__ == "__main__":
    print("📡 Dispositivo enviando datos... Presiona CTRL + C para detener.\n")

    try:
        while True:
            # GENERAR VALORES ALEATORIOS ENTRE LOS RANGOS DEFINIDOS
            value = round(random.uniform(VAL_MIN, VAL_MAX), 2)
            
            # ENVÍOS INDIVIDUALES
            send_data(value)
            
            time.sleep(INTERVALO)

    except KeyboardInterrupt:
        print("\n🛑 Dispositivo apagado manualmente.")
