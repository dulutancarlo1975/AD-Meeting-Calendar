CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    meeting_id INTEGER REFERENCES meetings (id) ON DELETE SET NULL,
    assigned_user_id INTEGER REFERENCES users (id) ON DELETE SET NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);