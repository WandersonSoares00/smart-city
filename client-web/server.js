const express = require('express');
const path = require('path');

const app = express();

app.get('/env.js', (req, res) => {
    res.type('application/javascript');
    res.send(`
        window.API_URL = "${process.env.API_URL || ''}";
    `);
});

app.use(express.static(path.join(__dirname, 'public')));

app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log('╔═══════════════════════════════════════════════╗');
    console.log('║   🏙️  Smart City - Cliente Web              ║');
    console.log('╚═══════════════════════════════════════════════╝');
    console.log(`  🌐 Interface Web: http://localhost:${PORT}`);
    console.log('╚═══════════════════════════════════════════════╝\n');
});
