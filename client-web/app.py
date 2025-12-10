from flask import Flask, render_template, redirect, request
from gateway_client import GatewayClient

app = Flask(__name__)
gw = GatewayClient()
gw.connect()

@app.route("/")
def index():
    resp = gw.list_devices()
    devices = resp.devices
    return render_template("index.html", devices=devices)

@app.route("/device/<device_id>")
def device(device_id):
    all_devices = gw.list_devices().devices
    dev = next((d for d in all_devices if d.id == device_id), None)
    return render_template("device.html", device=dev)

@app.post("/power/<device_id>")
def toggle(device_id):
    state = request.form.get("state") == "on"
    gw.set_power(device_id, state)
    return redirect(f"/device/{device_id}")

@app.post("/config/<device_id>")
def update_config(device_id):
    new_cfg = request.form.get("config")
    gw.set_config(device_id, new_cfg)
    return redirect(f"/device/{device_id}")

if __name__ == "__main__":
    app.run(port=8000, debug=True)
