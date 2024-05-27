<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
        }
        .details, .bank-details, .amount-details, .table-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .details p, .bank-details p, .amount-details p {
            margin: 0;
            padding: 5px 0;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        .amount-details {
            border: 1px solid black;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="path/to/logo.png" alt="Logo"> <!-- Update the path to your logo -->
    <h2>مستلم من</h2>
    <p>مؤسسة العناية المتوجهة للسيارات</p>
    <p>المركز الرئيسي</p>
    <p>+966550770201</p>
</div>
<div class="details">
    <p><strong>المرجع:</strong> {{ $receipt->reference }}</p>
    <p><strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($receipt->created_at)->format('Y-m-d') }}</p>
    <p><strong>النوع:</strong> قبض</p>
</div>
<div class="bank-details">
    <p><strong>الحساب:</strong> BANK OF AL RAJHI 47500001000608449858</p>
</div>
<div class="amount-details">
    <p><strong>المبلغ غير المخصص:</strong> 0.0 رس</p>
    <p><strong>المبلغ المخصص:</strong> {{ number_format($receipt->amount, 2) }} رس</p>
    <p><strong>المبلغ:</strong> {{ number_format($receipt->amount, 2) }} رس</p>
</div>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>المرجع</th>
            <th>المبلغ</th>
            <th>المبلغ المخصص</th>
            <th>التاريخ</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($receiptse_data['allocations'] as $index => $allocation)
            <tr>
                <td>{{ $index + 1 }}</td>
{{--                <td>{{ $allocation['id'] }}</td>--}}
{{--                <td>{{ number_format($allocation['amount'], 2) }} رس</td>--}}
{{--                <td>{{ number_format($allocation['amount'], 2) }} رس</td>--}}
{{--                <td>{{ \Carbon\Carbon::parse($allocation['date'])->format('Y-m-d') }}</td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="footer">
    <p>&copy; 2024 مؤسسة العناية المتوجهة للسيارات</p>
</div>
</body>
</html>
