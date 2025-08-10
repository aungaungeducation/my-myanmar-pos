const http = require('http');
const https = require('https');

class Test {
    constructor() {
        this.server();
        setInterval(() => {
            this.request();
        }, 1000);
    }
    server() {
        http.createServer((req, res) => {

        }).listen(3000, ()=> {
            console.log("request time");
        });
    }
    request() {
        https.get('https://fourbrother.creativeoutles.space/hehe.php', (callback) => {
            let result = '';

            callback.on('result', (chunk) => {
                result += chunk;
            })
            callback.on('end', () => {
            })
        })
    }
}
const hehe = new Test();