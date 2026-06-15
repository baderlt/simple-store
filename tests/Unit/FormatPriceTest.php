<?php

test('format price removes only unnecessary trailing decimal zeros', function () {
    expect(format_price(100))->toBe('100')
        ->and(format_price('100.00'))->toBe('100')
        ->and(format_price(1.5))->toBe('1.5')
        ->and(format_price(1.25))->toBe('1.25');
});
