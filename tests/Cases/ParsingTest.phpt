<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

// Test extract_plural_forms_header_from_po_header
Toolkit::test(function (): void {
	$parser = new gettext_reader(null);

	// It defaults to a "Western-style" plural header.
	Assert::same(
		'nplurals=2; plural=n == 1 ? 0 : 1;',
		$parser->extract_plural_forms_header_from_po_header('')
	);

	// Extracting it from the middle of the header works.
	Assert::same(
		'nplurals=1; plural=0;',
		$parser->extract_plural_forms_header_from_po_header(
			"Content-type: text/html; charset=UTF-8\n"
			. "Plural-Forms: nplurals=1; plural=0;\n"
			. "Last-Translator: nobody\n"
		)
	);

	// It's also case-insensitive.
	Assert::same(
		'nplurals=1; plural=0;',
		$parser->extract_plural_forms_header_from_po_header(
			"PLURAL-forms: nplurals=1; plural=0;\n"
		)
	);

	// It falls back to default if it's not on a separate line.
	Assert::same(
		'nplurals=2; plural=n == 1 ? 0 : 1;',
		$parser->extract_plural_forms_header_from_po_header(
			'Content-type: text/html; charset=UTF-8' // note the missing \n here
			. "Plural-Forms: nplurals=1; plural=0;\n"
			. "Last-Translator: nobody\n"
		)
	);
});

// Test npgettext
Toolkit::test(function (): void {
	$parser = new gettext_reader(null);

	$result = $parser->npgettext(
		'context',
		"%d pig went to the market\n",
		"%d pigs went to the market\n",
		1
	);
	Assert::same("%d pig went to the market\n", $result);

	$result = $parser->npgettext(
		'context',
		"%d pig went to the market\n",
		"%d pigs went to the market\n",
		2
	);
	Assert::same("%d pigs went to the market\n", $result);
});
