<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تقرير الذكاءات المتعددة - مشروع مسار</title>
  <style>
    /* DomPDF / WKHTMLTOPDF‑friendly stylesheet */
    @page { margin: 28px; }
    html, body { padding: 0; margin: 0; }
    body {
      font-family: DejaVu Sans, Amiri, "Noto Naskh Arabic", sans-serif;
      color: #222; line-height: 1.55;
      -webkit-text-size-adjust: 100%;
    }

    .header { text-align: center; margin-bottom: 12px; }
    .brand  { font-size: 20px; font-weight: 800; color: #e67e22; }
    .title  { font-size: 18px; font-weight: 800; margin-top: 6px; }
    .meta   { font-size: 12px; color: #555; margin-top: 4px; }

    .box    { border: 1px solid #ddd; border-radius: 8px; padding: 14px; margin-top: 12px; }
    .avg    { font-size: 16px; font-weight: 800; color: #2c3e50; text-align: center; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
    th, td { border: 1px solid #ddd; padding: 8px; font-size: 13px; text-align: center; vertical-align: middle; }
    th { background: #f7f7f7; font-weight: 700; }
    tbody tr { page-break-inside: avoid; }

    .muted  { color: #777; font-size: 12px; text-align: center; margin-top: 6px; }
    .footer { text-align: center; font-size: 11px; color: #666; margin-top: 12px; }

    /* رقم العمود بعرض ثابت حتى لا ينكسر السطر */
    .col-idx   { width: 42px; }
    .col-name  { width: auto; }
    .col-score { width: 120px; }
  </style>
</head>
<body>
  <div class="header">
    <div class="brand">مشروع مسار</div>
    <div class="title">تقرير الذكاءات المتعددة</div>
    <div class="meta">
      الطالب: <strong>{{ $student->full_name ?? '—' }}</strong>
      | نوع الاختبار: <strong>{{ $testLabel ?? '—' }}</strong>
      | التاريخ: <strong>{{ $generatedAt ?? date('Y-m-d H:i') }}</strong>
    </div>
  </div>

  @isset($overallAvg)
    <div class="box">
      <div class="avg">المعدل العام للنِّسب: {{ number_format((float)$overallAvg, 0) }}%</div>
      @empty($percents)
        <div class="muted">لا توجد نسب متاحة للعرض حالياً.</div>
      @endempty
    </div>
  @endisset

  @if(!empty($percents))
    <div class="box">
      <table>
        <thead>
          <tr>
            <th class="col-idx">#</th>
            <th class="col-name">نوع الذكاء</th>
            <th class="col-score">النسبة %</th>
          </tr>
        </thead>
        <tbody>
          @php $i = 1; @endphp
          @foreach($percents as $typeId => $percent)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $intelligenceTypes[$typeId]->name ?? ('ID '.$typeId) }}</td>
              <td>{{ number_format((float)$percent, 0) }}%</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <div class="footer">
    © {{ date('Y') }} مشروع مسار — هذا التقرير تم توليده آلياً للاستخدام الإرشادي.
  </div>
</body>
</html>