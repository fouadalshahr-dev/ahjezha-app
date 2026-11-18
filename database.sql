-- 1. جدول الملاعب (يتضمن رمز الشام كاش الخاص بكل ملعب)
ALTER TABLE stadiums ADD COLUMN cham_cash_account VARCHAR(100) NOT NULL;

-- 2. جدول الحجوزات (يتضمن رقم الهاتف السوري، طريقة الدفع شام كاش حصراً، ورقم عملية التحويل)
ALTER TABLE bookings ADD COLUMN syrian_phone VARCHAR(15) NOT NULL;
ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cham_cash';
ALTER TABLE bookings ADD COLUMN cham_transaction_id VARCHAR(100) NOT NULL;
