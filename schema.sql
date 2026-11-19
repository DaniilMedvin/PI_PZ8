DROP TABLE IF EXISTS shipments;

CREATE TABLE shipments (
    id SERIAL PRIMARY KEY,
    tracking_number VARCHAR(20) NOT NULL UNIQUE,
    carrier VARCHAR(20) NOT NULL CHECK (carrier IN ('fastpost', 'globalexpress', 'legacycargo')),
    recipient VARCHAR(120) NOT NULL,
    description VARCHAR(200) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'created' CHECK (status IN ('created', 'in_transit', 'delivered', 'delayed')),
    events JSONB NOT NULL DEFAULT '[]',
    checked_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

INSERT INTO shipments (tracking_number, carrier, recipient, description) VALUES
    ('FP20260001', 'fastpost', 'ТОВ «Весна»', 'Палети з побутовою технікою, 12 місць'),
    ('GE20260002', 'globalexpress', 'ФОП Мельник І. В.', 'Офісні меблі, 4 коробки'),
    ('LC20260003', 'legacycargo', 'ТОВ «Дніпро Логістик»', 'Автозапчастини, 800 кг'),
    ('FP20260004', 'fastpost', 'Коваленко Олена Петрівна', 'Особисті речі, 2 коробки'),
    ('GE20260005', 'globalexpress', 'ТОВ «Південь Трейд»', 'Продукти харчування, рефрижератор');
