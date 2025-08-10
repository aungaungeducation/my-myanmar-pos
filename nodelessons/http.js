const http = require('http');
const https = require('https');

const server = http.createServer((req, res) => {
    if (req.url === '/') {
        // HTTPS GET request to external PHP API
        https.get('https://fourbrother.creativeoutles.space/hehe.php', (apiRes) => {
            let data = '';

            apiRes.on('data', (chunk) => {
                data += chunk;
            });

            apiRes.on('end', () => {
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(data); // send the result from PHP API to browser
            });
        }).on('error', (err) => {
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end('Error fetching API: ' + err.message);
        });
    } else {
        res.writeHead(404);
        res.end('Not Found');
    }
});

server.listen(3000, () => {
    console.log('Server running at http://localhost:3000');
});
