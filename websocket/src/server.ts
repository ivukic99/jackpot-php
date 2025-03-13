import * as net from 'net';
import {Server} from 'socket.io';
import Redis from 'ioredis';
import * as dotenv from 'dotenv';
interface IData {
    total: number
}

dotenv.config();

const WS_PORT: number = Number(process.env.WS_PORT) ?? 3000;
const TCP_PORT: number = Number(process.env.TCP_PORT) ?? 4000;
const REDIS_HOST: string = process.env.REDIS_HOST ?? 'localhost';
const REDIS_PORT: number = Number(process.env.REDIS_PORT) ?? 6379;

const redis = new Redis({
    host: REDIS_HOST,
    port: REDIS_PORT
})

const io = new Server(WS_PORT, {
    cors: { origin: "*" },
});

let lastUpdate: number = 0;

console.log(`Websocket started on port ${WS_PORT}`);

io.on('connection', async (socket) => {
    console.log(`Client connecting: ${socket.id}`);
    const jackpot_total = await redis.get('jackpot_total') ?? 0;
    socket.emit("jackpot_update", Number(jackpot_total));
});

const tcpServer = net.createServer((socket) => {
    console.log("API connection with TCP");

    socket.on('data', async (data: Buffer) => {
        try {
            const stringData: string = data.toString().trim();
            const { total }: IData = JSON.parse(stringData);
            const now: number = Date.now();

            if (now - lastUpdate >= 5000) {
                    lastUpdate = now;
                    await redis.set('jackpot_total', total.toString());

                io.emit("jackpot_update", total);
            }
        } catch (error) {
            console.log('Invalid JSON format.')
        }

    });

    socket.on("end", () => console.log("API stop connection!"));
});

tcpServer.listen(TCP_PORT, () => {
    console.log(`TCP server listening on port ${TCP_PORT}`);
})