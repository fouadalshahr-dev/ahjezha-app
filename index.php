<?php
// معالجة بيانات الحجز عند الإرسال
$response_message = "";
$response_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stadium_id = $_POST['stadium_id'] ?? '';
    $syrian_phone = trim($_POST['syrian_phone'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $stadium_cham_code = trim($_POST['stadium_cham_code'] ?? '');
    $cham_transaction_id = trim($_POST['cham_transaction_id'] ?? '');

    // التحقق من طريقة الدفع (الشام كاش حصراً)
    if ($payment_method !== 'cham_cash') {
        $response_message = "عذراً، طريقة الدفع مقتصرة حصراً عبر تطبيق الشام كاش.";
        $response_class = "error";
    }
    // التحقق من صحة رقم الهاتف السوري
    elseif (!preg_match('/^09[3-9][0-9]{7}$/', $syrian_phone)) {
        $response_message = "خطأ: يرجى إدخال رقم هاتف محمول سوري صحيح (مثال: 0912345678).";
        $response_class = "error";
    }
    // التحقق من إدخال رمز الملعب ورقم الحوالة
    elseif (empty($stadium_cham_code) || empty($cham_transaction_id)) {
        $response_message = "خطأ: يجب اختيار الملعب وإدخال رقم عملية التحويل الخاصة بالشام كاش.";
        $response_class = "error";
    } else {
        // حفظ الحجز في قاعدة البيانات (محاكاة نجاح العملية)
        $response_message = "تم استلام طلب حجز الملعب بنجاح، وتوثيق عملية الدفع عبر الشام كاش!";
        $response_class = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيق احجزها - حجز الملاعب والشام كاش</title>
    <style>
        body { font-family: Tahoma, sans-serif; background-color: #f0f4f8; margin: 0; padding: 20px; direction: rtl; }
        .container { max-width: 600px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: auto; }
        h2 { text-align: center; color: #1e3a8a; margin-bottom: 5px; }
        p.sub { text-align: center; color: #64748b; font-size: 14px; margin-top: 0; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #334155; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; background-color: #fff; }
        input:focus, select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        input[readonly], select[readonly] { background-color: #f8fafc; color: #475569; }
        small { display: block; margin-top: 5px; color: #64748b; font-size: 12px; }
        .btn { background-color: #16a34a; color: white; padding: 14px; border: none; border-radius: 6px; width: 100%; font-size: 16px; cursor: pointer; font-weight: bold; transition: background 0.2s; }
        .btn:hover { background-color: #15803d; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; text-align: center; font-weight: bold; font-size: 14px; }
        .alert.success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>

<div class="container">
    <h2>تطبيق «احجزها»</h2>
    <p class="sub">نظام حجز الملاعب الرياضية - الدفع الإلكتروني (الشام كاش حصراً)</p>

    <?php if (!empty($response_message)): ?>
        <div class="alert <?php echo $response_class; ?>">
            <?php echo $response_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <!-- اختيار الملعب -->
        <div class="form-group">
            <label for="stadium_select">اختر الملعب المطلوب</label>
            <select id="stadium_select" name="stadium_id" required onchange="updateStadiumData(this)">
                <option value="">-- اضغط لاختيار الملعب --</option>
                <option value="1" data-cham="CHAM-DAMASCUS-01">ملعب العشب الأخضر (دمشق)</option>
                <option value="2" data-cham="CHAM-ALEPPO-02">ملعب الشهباء (حلب)</option>
                <option value="3" data-cham="CHAM-LATAKIA-03">ملعب الزمرد (لاذقية)</option>
            </select>
        </div>

        <!-- رقم الهاتف السوري -->
        <div class="form-group">
            <label for="syrian_phone">رقم الهاتف السوري</label>
            <input type="tel" id="syrian_phone" name="syrian_phone" placeholder="09xxxxxxxx" pattern="^09[3-9][0-9]{7}$" required>
            <small>أدخل رقم هاتفك المحمول السوري (مثال: 0912345678)</small>
        </div>

        <!-- طريقة الدفع (مثبتة حصراً على الشام كاش) -->
        <div class="form-group">
            <label for="payment_method">طريقة الدفع المعتمدة</label>
            <select id="payment_method" name="payment_method" required>
                <option value="cham_cash" selected>الشام كاش (Cham Cash) - حصراً</option>
            </select>
        </div>

        <!-- رمز حساب الشام كاش الخاص بالملعب (يظهر تلقائياً) -->
        <div class="form-group">
            <label for="stadium_cham_code">رمز حساب الشام كاش الخاص بالملعب</label>
            <input type="text" id="stadium_cham_code" name="stadium_cham_code" readonly required placeholder="يظهر رمز الملعب هنا تلقائياً">
            <small>قم بتحويل قيمة الحجز عبر تطبيق شام كاش إلى هذا الرمز الخاص بالملعب.</small>
        </div>

        <!-- رقم عملية التحويل -->
        <div class="form-group">
            <label for="cham_transaction_id">رقم عملية التحويل (Transaction ID)</label>
            <input type="text" id="cham_transaction_id" name="cham_transaction_id" required placeholder="أدخل رقم الحوالة أو الإيصال من تطبيق شام كاش">
        </div>

        <button type="submit" class="btn">تأكيد الحجز وإتمام الدفع</button>
    </form>
</div>

<script>
function updateStadiumData(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const chamCode = selectedOption.getAttribute('data-cham') || '';
    document.getElementById('stadium_cham_code').value = chamCode;
}
</script>

</body>
</html>
