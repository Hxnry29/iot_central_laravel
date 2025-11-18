import os
import sys
import json
import time
import asyncio
import random
import configparser
import requests
from datetime import datetime

# ============================================
# CONFIGURACIÓN DESDE ARCHIVO .ini (OPCIONAL)
# ============================================

config = configparser.ConfigParser()
# config_path = os.path.join(os.path.dirname(__file__), "device.ini")

# if os.path.exists(config_path):
#     config.read(config_path)
# else:
#     print("⚠️  Archivo device.ini no encontrado. Usando configuración por defecto.\n")


# ============================================
# PARÁMETROS DEL DISPOSITIVO
# ============================================

API_URL = config.get("DEVICE", "API_URL", fallback="http://127.0.0.1:8000/api/device-data")
DEVICE_ID = config.get("DEVICE", "DEVICE_ID", fallback="Sensor_001")
DEVICE_TOKEN = config.get("DEVICE", "DEVICE_TOKEN", fallback="E0MPptmCKd8uu1El")

SEND_INTERVAL = config.getint("DEVICE", "SEND_INTERVAL", fallback=10)


# ============================================
# FUNCIÓN GENERAL PARA ENVIAR DATOS
# ============================================

def send_telemetry(data: dict):
    """
    Envía cualquier payload al endpoint API.
    """
    payload = {
        "device_id": DEVICE_ID,
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        "unit": "°C",
        "type": "temperature",
        
    }

    payload.update(data)   # Agregar datos del usuario

    headers = {
        "Content-Type": "application/json",
        "X-DEVICE-TOKEN": DEVICE_TOKEN,
    }

    try:
        response = requests.post(API_URL, data=json.dumps(payload), headers=headers)

        print(f"📤 Enviado: {json.dumps(payload)}")
        print(f"📥 Respuesta {response.status_code}: {response.text}\n")

    except Exception as e:
        print(f"❌ Error enviando datos: {e}")


# ============================================
# SIMULADOR DE TELEMETRÍA (OPCIONAL)
# ============================================

async def telemetry_loop():
    """
    Simula envío de datos de sensores de forma periódica.
    Modifica este payload para ajustarlo a tu plataforma.
    """
    while True:
        data = {
            "temperature": round(random.uniform(20, 40), 1),
            # "humidity": random.randint(30, 80),
            # "co2": random.randint(300, 900),
            # "smoke": random.randint(0, 350),
            # "pm25": random.randint(5, 200),
        }

        send_telemetry(data)

        await asyncio.sleep(SEND_INTERVAL)


# ============================================
# MAIN
# ============================================

async def main():
    print("🚀 Cliente IoT conectado a tu plataforma.\n")
    print(f"Dispositivo: {DEVICE_ID}")
    print(f"Enviando datos cada: {SEND_INTERVAL}s\n")

    await telemetry_loop()


if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🛑 Finalizado por el usuario.")
