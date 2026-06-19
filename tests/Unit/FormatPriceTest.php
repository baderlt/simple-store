<?php

test('format price removes only unnecessary trailing decimal zeros', function () {
    expect(format_price(100))->toBe('100')
        ->and(format_price('100.00'))->toBe('100')
        ->and(format_price(1.5))->toBe('1.5')
        ->and(format_price(1.25))->toBe('1.25');
});

test('working hours are split into structured day and time rows', function () {
    expect(working_hours_parts('Matin Lun-Dim: 12h00-15h00 | Soir Lun-Dim: 18h00-00h00'))
        ->toBe([
            ['days' => 'Matin Lun-Dim', 'hours' => '12h00-15h00'],
            ['days' => 'Soir Lun-Dim', 'hours' => '18h00-00h00'],
        ])
        ->and(working_hours_parts('Lun-Sam: 9h-20h، Dim: 10h-18h'))
        ->toBe([
            ['days' => 'Lun-Sam', 'hours' => '9h-20h'],
            ['days' => 'Dim', 'hours' => '10h-18h'],
        ]);
});

test('mixed Arabic and Latin text is emitted in isolated source-order segments', function () {
    $latinFirst = bidi_text('Miel de thym - عسل الزعتر - 500');
    $arabicFirst = bidi_text('املو بذور اليقطين (زريعة الكرعة) - 750g / بالعسل');

    expect($latinFirst)
        ->toContain('class="bidi-text" dir="ltr"')
        ->toContain('class="bidi-text-segment" dir="rtl"')
        ->toContain('class="bidi-text-separator" dir="ltr"')
        ->and(strpos($latinFirst, 'Miel de thym'))->toBeLessThan(strpos($latinFirst, 'عسل الزعتر'))
        ->and(strpos($latinFirst, 'عسل الزعتر'))->toBeLessThan(strpos($latinFirst, '500'));

    expect($arabicFirst)
        ->toContain('class="bidi-text" dir="rtl"')
        ->toContain('dir="ltr" style="unicode-bidi:isolate;">750g')
        ->and(strpos($arabicFirst, 'املو بذور اليقطين'))->toBeLessThan(strpos($arabicFirst, '750g'))
        ->and(strpos($arabicFirst, '750g'))->toBeLessThan(strpos($arabicFirst, 'بالعسل'));

    expect(bidi_text('<script>alert("x")</script>'))
        ->not->toContain('<script>')
        ->toContain('&lt;script&gt;');
});
