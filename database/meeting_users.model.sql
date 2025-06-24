CREATE TABLE IF NOT EXISTS meeting_users (
    meeting_id INTEGER NOT NULL REFERENCES meetings (id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    role VARCHAR(50) NOT NULL,
    PRIMARY KEY (meeting_id, user_id)
);