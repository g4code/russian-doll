TITLE = [russian-doll]

test: unit-tests

unit-tests:
	@/bin/echo -e "${TITLE} testing suite started..." \
	&& vendor/bin/phpunit -c tests/unit/phpunit.xml --coverage-html tests/unit/coverage

.PHONY: test unit-tests