import * as net from 'net';
import {Server} from 'socket.io';
import Redis from 'ioredis';
import { consumeFromQueue } from './rabbitmq'
import { config } from './config';
import Logger from './logger';

interface IData {
    total: number
}

const WS_PORT: number = config.WS_PORT;
const TCP_PORT: number = config.TCP_PORT;
const REDIS_HOST: string = config.REDIS_HOST;
const REDIS_PORT: number = config.REDIS_PORT;

const redis = new Redis({
    host: REDIS_HOST,
    port: REDIS_PORT
})

const io = new Server(WS_PORT, {
    cors: { origin: '*' },
});

let lastUpdate: number = 0;

Logger.info(`Websocket started on port ${WS_PORT}`)

io.on('connection', async (socket) => {
    Logger.info(`Client connecting: ${socket.id}`);
    const jackpot_total = await redis.get('jackpot_total') ?? 0;
    socket.emit('jackpot_update', Number(jackpot_total));
});

const tcpServer = net.createServer((socket) => {
    Logger.info("API connection with TCP");

    socket.on('data', async (data: Buffer) => {
        try {
            const stringData: string = data.toString().trim();
            const { total }: IData = JSON.parse(stringData);
            const now: number = Date.now();

            if (now - lastUpdate >= 5000) {
                lastUpdate = now;
                await redis.set('jackpot_total', total.toString());

                io.emit('jackpot_update', total);
            }
        } catch (error) {
            Logger.error('Invalid JSON format.');
        }

    });

    socket.on("end", () => Logger.info('API stop connection!'));
});

tcpServer.listen(TCP_PORT, () => {
    Logger.info(`TCP server listening on port ${TCP_PORT}`)
})

const handleDataFromQueue = async (data: IData) => {
    const now: number = Date.now();

    if (now - lastUpdate >= 5000) {
        lastUpdate = now;
        await redis.set('jackpot_total', data.total.toString());

        io.emit("jackpot_update", data.total);
    }
}
consumeFromQueue(handleDataFromQueue);
