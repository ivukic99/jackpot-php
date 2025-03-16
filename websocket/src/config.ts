import * as dotenv from 'dotenv';

dotenv.config();
export const config = {
    WS_PORT: Number(process.env.WS_PORT) || 3000,
    TCP_PORT: Number(process.env.TCP_PORT) || 4000,
    REDIS_HOST: process.env.REDIS_HOST || 'redis',
    REDIS_PORT: Number(process.env.REDIS_PORT) || 6379,
    RABBITMQ_USER: process.env.RABBITMQ_USER || 'admin',
    RABBITMQ_PASSWORD: process.env.RABBITMQ_PASSWORD || 'admin',
    RABBITMQ_URL: process.env.RABBITMQ_URL || 'amqp://admin:admin@rabbitmq:5672',
};