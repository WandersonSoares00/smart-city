
CREATE TABLE devices (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL,
  type VARCHAR(50) NOT NULL,
  location VARCHAR(100),
  ip VARCHAR(45),
  port INT,
  current_state VARCHAR(50),
  state_value VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

---
--- CREATE TABLE device_states (
---   id BIGINT AUTO_INCREMENT PRIMARY KEY,
---   device_id BIGINT NOT NULL,
---   state VARCHAR(50),
---   value VARCHAR(100),
---   recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
---   FOREIGN KEY (device_id) REFERENCES devices(id)
--- );
--- 

ALTER TABLE devices ADD UNIQUE INDEX unique_name (name);