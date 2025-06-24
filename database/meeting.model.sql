CREATE TABLE IF NOT EXISTS meetings (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    scheduled_at TIMESTAMP NOT NULL,
    duration_minutes INTEGER,
    location VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);