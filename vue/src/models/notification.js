export class Notification {
    constructor({ id, type, type_id, message, timestamp, read = 0, persisted = false }) {
        this.id = id;
        this.type = type;
        this.type_id = type_id;
        this.message = message;
        this.timestamp = timestamp;
        this.read = read;
        this.persisted = persisted;
    }

    static fromPersisted(row) {
        return new Notification({
            id: row.id,
            type: row.type,
            type_id: row.type_id,
            message: row.message,
            timestamp: row.created_at,
            read: row.read,
            persisted: true
        });
    }

    static fromRealtime(e) {
        return new Notification({
            id: e.payload.notification_id,
            type: e.payload.type,
            type_id: e.payload.type_id,
            message: e.payload.message,
            timestamp: new Date().toISOString(),
            read: 0,
            persisted: false
        });
    }

    markAsRead() {
        this.read = 1;
    }
}
