const express = require('express');
const app = express();

const gateway = require('./gateway.client');
const cors = require('cors');
const routes = require('./devices.routes');

app.use(cors({ origin: 'http://localhost:4000' }));
app.use(express.json());
app.use('/api', routes(gateway));

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
    console.log('API REST rodando em http://localhost:' + PORT);
});
