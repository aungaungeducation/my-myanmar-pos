const http = require('http');
const mysql = require('mysql');

class User {
    constructor() {
        this.connection = mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '',
            database: 'kosai'
        });

        this.connection.connect((err) => {
            if (err) {
                this.status = "connection failed";
                console.log("DB Error");
                return;
            }
            this.status = "connection success";
            console.log("Connected to DB");
        });

        http.createServer((req, res) => {
            res.writeHead(200, { "Content-Type": "application/json" });
            this.getalluser((users) => {
                res.end(JSON.stringify({
                    status: true,
                    users: users,
                    passowrd: "that is password"
                }))
            });
        }).listen(3000, () => {
            console.log("Server running at http://localhost:3000");
        });
    }

    getalluser(users) {
        this.connection.query("SELECT * FROM userauth", (err, result) => {
            if(err) throw err;
            users(result);
        });
    }
}

const user = new User();
