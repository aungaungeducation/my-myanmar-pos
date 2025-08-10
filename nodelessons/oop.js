const https = require('https');

const timestamp = Math.floor(Date.now() / 1000); // current time in seconds

const postData = JSON.stringify({
  method: "login",
  params: {
    username: "admin",
    time: timestamp.toString(),
    encry: true,
    pwd: "U2FsdGVkX1/szL3qqMZfDOUnRRfPq8lkSVuf+lU+eGg=" // this must be updated if encryption depends on timestamp
  }
});

const options = {
  hostname: '192.168.110.1',
  port: 443,
  path: '/cgi-bin/luci/api/auth',
  method: 'POST',
  rejectUnauthorized: false, // for self-signed SSL
  headers: {
    'Host': '192.168.110.1',
    'Cookie': '__APP_LANG__=en',
    'Content-Type': 'application/json',
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
    'Accept': '*/*',
    'Origin': 'https://192.168.110.1',
    'Referer': `https://192.168.110.1/cgi-bin/luci/?stamp=${timestamp}`,
    'Content-Length': Buffer.byteLength(postData)
  }
};

const req = https.request(options, (res) => {
  let data = '';

  res.on('data', (chunk) => {
    data += chunk;
  });

  res.on('end', () => {
    console.log('Response:', data);
  });
});

req.on('error', (e) => {
  console.error(`Request error: ${e.message}`);
});

req.write(postData);
req.end();
