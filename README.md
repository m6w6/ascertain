ascertain
=========
[![Build Status](https://travis-ci.org/mike-php-net/ascertain.png?branch=master)](https://travis-ci.org/mike-php-net/ascertain)

Harmless validation.

```php
$user->assert()
	->that("name")
		->isNotNothing("a name is required")
		->isLen(4, "must be at least 4 characters long")
	->that("email")
		->isEmail("is not valid")
	->that("homepage")
		->when($user->hasHomepage())
		->isUrl("seems not to be a valid URL");

# ...

class User implements \ascertain\Testable
{
	use \ascertain\Validator;

	protected $id;
	protected $name;
	protected $email;
	protected $homepage;

	function hasHomepage() {
		return isset($this->homepage);
	}

	function export() {
		return array(
			"name"     => $this->name,
			"email"    => $this->email,
			"homepage" => $this->homepage.
		);
	}
}
```

