<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expense Report</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            position: relative;
        }
        .header {
            text-align: center;
            position: relative;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #333;
        }
        .header h2 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-top: 0;
        }
        .decorative-pattern {
            position: absolute;
            top: 20px;
            left: 20px;
            opacity: 0.1;
        }
        .watermark {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            font-size: 150px;
            opacity: 0.05;
            z-index: -1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #555;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .total-label {
            text-align: right;
        }
        .section-title {
            font-size: 18px;
            margin: 30px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .receipt-number {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .company-info {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Decorative elements -->
        <div class="decorative-pattern">
            • • • • •<br>
            • • • • •<br>
            • • • • •<br>
            • • • • •<br>
            • • • • •
        </div>
        <div class="watermark">AC</div>
        
        <!-- Header -->
        <div class="header">
           <h1>
            ASCENDCORP
    </h1>            
            <h2>{{ date('F Y') }}</h2>
            <h2>{{ $expenses->first()->currency ?? 'KES' }}</h2>
            <h1>INCOMES & EXPENSES</h1>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <strong>AscendCorp</strong><br>
            123 Business Avenue, Nairobi, Kenya<br>
            Email: info@ascendcorp.com | Phone: +254 700 000000
        </div>
        
        <!-- Receipt Number -->
        <div class="receipt-number">
            <strong>Report Number:</strong> ASCRP-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}-{{ date('y') }}<br>
            <strong>Generated On:</strong> {{ date('Y-m-d H:i:s') }}
        </div>
        
        <!-- Expenses Table -->
        <h3 class="section-title">EXPENSE</h3>
        <table>
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>EXPENSE</th>
                    <th>CATEGORY</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($expenses as $expense)
                    @php $totalAmount += $expense->amount; @endphp
                    <tr>
                        <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->category->name ?? 'Uncategorized' }}</td>
                        <td>{{ $expense->currency }} {{ number_format($expense->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="total-label">TOTAL</td>
                    <td>{{ $expenses->first()->currency ?? 'KES' }} {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <p>This is an official expense report generated by Tryascend Corporate solution .</p>
            <p>© {{ date('Y') }} Tryascend Corporate solution. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
