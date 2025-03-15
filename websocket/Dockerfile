FROM node:23-alpine

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN mkdir -p /usr/src/app/logs && chmod -R 777 /usr/src/app/logs

RUN npm run build

EXPOSE 3000

CMD ["npx", "ts-node", "src/server.ts"]
