import socket
import messages_pb2

class GatewayClient:
    def __init__(self, host="127.0.0.1", port=5000):
        self.host = host
        self.port = port
        self.sock = None

    def connect(self):
        if self.sock:
            return
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.sock.connect((self.host, self.port))

    def _send(self, req):
        data = req.SerializeToString()
        self.sock.sendall(len(data).to_bytes(4, "big") + data)

        resp_len = int.from_bytes(self.sock.recv(4), "big")
        resp_bytes = self.sock.recv(resp_len)

        resp = messages_pb2.Response()
        resp.ParseFromString(resp_bytes)
        return resp

    def list_devices(self):
        req = messages_pb2.Request(type=messages_pb2.Request.LIST_DEVICES)
        return self._send(req)

    def set_power(self, device_id, state):
        req = messages_pb2.Request(
            type=messages_pb2.Request.SET_POWER,
            device_id=device_id,
            power_state=state
        )
        return self._send(req)

    def set_config(self, device_id, cfg):
        req = messages_pb2.Request(
            type=messages_pb2.Request.SET_CONFIG,
            device_id=device_id,
            new_config=cfg
        )
        return self._send(req)
