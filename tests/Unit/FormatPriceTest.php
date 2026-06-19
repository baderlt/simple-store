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
