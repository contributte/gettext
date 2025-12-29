<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Test _setlocale
Toolkit::test(function (): void {
	putenv('LC_ALL=');
	// _setlocale defaults to a locale name from environment variable LANG.
	putenv('LANG=sr_RS');
	Assert::same('sr_RS', _setlocale(LC_MESSAGES, 0));
});

// Test _setlocale system
Toolkit::test(function (): void {
	putenv('LC_ALL=');
	// For an existing locale, it never needs emulation.
	putenv('LANG=C');
	_setlocale(LC_MESSAGES, '');
	Assert::same(0, locale_emulation());
});

// Test _setlocale emulation
Toolkit::test(function (): void {
	putenv('LC_ALL=');
	// If we set it to a non-existent locale, it still works, but uses emulation.
	_setlocale(LC_MESSAGES, 'xxx_XXX');
	Assert::same('xxx_XXX', _setlocale(LC_MESSAGES, 0));
	Assert::same(1, locale_emulation());
});

// Test get_list_of_locales
Toolkit::test(function (): void {
	// For a locale containing country code, we prefer
	// full locale name, but if that's not found, fall back
	// to the language only locale name.
	Assert::same(['sr_RS', 'sr'], get_list_of_locales('sr_RS'));

	// If language code is used, it's the only thing returned.
	Assert::same(['sr'], get_list_of_locales('sr'));

	// There is support for language and charset only.
	Assert::same(['sr.UTF-8', 'sr'], get_list_of_locales('sr.UTF-8'));

	// It can also split out character set from the full locale name.
	Assert::same(['sr_RS.UTF-8', 'sr_RS', 'sr'], get_list_of_locales('sr_RS.UTF-8'));

	// There is support for @modifier in locale names as well.
	Assert::same(
		['sr_RS.UTF-8@latin', 'sr_RS@latin', 'sr@latin', 'sr_RS.UTF-8', 'sr_RS', 'sr'],
		get_list_of_locales('sr_RS.UTF-8@latin')
	);

	// We can pass in only language and modifier.
	Assert::same(['sr@latin', 'sr'], get_list_of_locales('sr@latin'));

	// If locale name is not following the regular POSIX pattern,
	// it's used verbatim.
	Assert::same(['something'], get_list_of_locales('something'));

	// Passing in an empty string returns an empty array.
	Assert::same([], get_list_of_locales(''));
});
