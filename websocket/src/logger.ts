import * as fs from 'fs';
import * as path from 'path';

const appLogDir: string = path.resolve(__dirname, '../logs/app.log');
const errorLogDir: string = path.resolve(__dirname, '../logs/error.log');
class Logger {
    private static write(file: string, message: string) {
        const timestamp: string = new Date().toISOString();
        const log: string = `[${timestamp}] ${message}\n`;
        fs.appendFileSync(file, log);
    }

    public static info(message: string) {
        this.write(appLogDir, message);
    }

    public static error(message: string) {
        this.write(errorLogDir, message);
    }
}

export default Logger;

