-- Pode ser reaplicada: preserva valores já configurados no painel.
SET NAMES utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES
('hero_title', 'Qualidade que *aquece*, confiança que *dura*.'),
('hero_description', 'Soluções em metal com excelência em cada detalhe.')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
