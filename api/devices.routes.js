const express = require('express');
const router = express.Router();

module.exports = ({ listDevices, sendCommand }) => {

    router.get('/devices', async (req, res) => {
        const devices = await listDevices();
        res.json({ success: true, devices });
    });

    router.post('/command', async (req, res) => {
        const { deviceName, action, value } = req.body;

        if (!deviceName || !action) {
            return res.status(400).json({
                success: false,
                error: 'deviceName e action são obrigatórios'
            });
        }

        const result = await sendCommand(deviceName, action, value || '');
        
        res.json({ success: true, result });
    });

    return router;
};
