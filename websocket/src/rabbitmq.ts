import * as amqp from 'amqplib';
import { config } from './config';
import Logger from './logger';

const RABBITMQ_URL: string = config.RABBITMQ_URL;
const QUEUE_NAME: string = 'jackpot_queue';

const connectWithRetry = async (callback: (message: any) => void) => {
    const maxRetries = 5;
    let attempt = 0;

    while (attempt < maxRetries) {
        try {
            Logger.info(`Attempt ${attempt + 1} to connect to RabbitMQ...`);
            const connection = await amqp.connect(RABBITMQ_URL);
            const channel = await connection.createChannel();
            await channel.assertQueue(QUEUE_NAME, {
                durable: true,
                arguments: { 'x-max-length': 1 },
            });

            channel.consume(QUEUE_NAME, (msg) => {
                if (msg) {
                    const messageContent = msg.content.toString();
                    const data = JSON.parse(messageContent);
                    callback(data);
                    channel.ack(msg);
                }
            }, {exclusive: true});

            Logger.info(`Connected and listening on queue: ${QUEUE_NAME}`);
            return;
        } catch (error) {
            Logger.error(`Connection attempt ${attempt + 1} failed. Retrying...`)
            attempt++;
            await new Promise((resolve) => setTimeout(resolve, 2000));
        }
    }

    Logger.error('Failed to connect to RabbitMQ.');
};

export const consumeFromQueue = (callback: (message: any) => void) => {
    connectWithRetry(callback);
};