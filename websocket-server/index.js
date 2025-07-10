const express = require("express");
const http = require("http");
const {Server} = require("socket.io");

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*",
    },
});

const onlineUsers = new Map();

io.on('connection', (socket) => {
    const userId = socket.handshake.query.user_id;

    if (userId) {
        onlineUsers.set(userId, socket);

        socket.on('disconnect', () => {
            onlineUsers.delete(userId);
        });
    }
});

app.use(express.json());
app.post('/notify', (req, res) => {
    const {notifications} = req.body;

    Object.entries(notifications).forEach(([user_id, flightIds]) => {
        const socket = onlineUsers.get(user_id.toString());

        if (socket) {
            socket.emit("notification", flightIds)
        }
    });

    res.json({success: true});
});

app.get('/online-users', (req, res) => {
    res.json({
        users: Array.from(onlineUsers.keys())
    });
});

server.listen(3000, () => {
    console.log("WebSocket server in ascolto su porta 3000");
});
