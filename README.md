# Aegisora Required Rule Guardian

[![Latest Version](https://img.shields.io/packagist/v/aegisora/required-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/required-rule-guardian)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/required-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/required-rule-guardian)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Required Rule Guardian provides a simple shortcut for ensuring a value is present (not `null`) using `aegisora/guardian` and `aegisora/required-rule`.

It is designed for cases where you want to quickly check whether a value is required without manually creating validation pipelines.

This package is built on top of:

- [aegisora/guardian](https://github.com/Aegisora/guardian)
- [aegisora/required-rule](https://github.com/Aegisora/required-rule)

---

## ✨ Features
- 🔹 Simple shortcut API for `RequiredRule`
- 🔹 Validates whether a value is present (not `null`)
- 🔹 Uses `aegisora/guardian` internally
- 🔹 Uses `aegisora/required-rule` internally
- 🔹 Supports custom validation exceptions
- 🔹 Fully compatible with the Aegisora ecosystem
- 🔹 Ready to use out of the box

---

## 📦 Installation

```shell
composer require aegisora/required-rule-guardian
```

---

## 🚀 Core Concept

This package wraps the common validation flow:

```php
$guardian->check($value, RequiredRule::create(), new InvalidValueException());
```

into a dedicated shortcut class:

```php
$requiredRuleGuardian->check($value, new InvalidValueException());
```

Instead of manually creating `RequiredRule` and passing it to `Guardian`, you can use `RequiredRuleGuardian` directly.

---

## 🏗️ Basic Usage

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\RequiredRule\RequiredRuleGuardian;

$guardian = new Guardian();

$requiredRuleGuardian = new RequiredRuleGuardian($guardian);

try {
    $requiredRuleGuardian->check('foo');
    // value is present
} catch (GuardianValidationException $exception) {
    // value is null
}
```

---

## 🧩 Usage with Custom Exception

You may provide your own exception for validation failure.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\RequiredRule\RequiredRuleGuardian;
use App\Exceptions\InvalidValueException;

$guardian = new Guardian();

$requiredRuleGuardian = new RequiredRuleGuardian($guardian);

$requiredRuleGuardian->check(null, new InvalidValueException());
```

If the value is `null`, the provided exception will be thrown.

This is useful when validation errors should have domain-specific meaning.

---

## 🧪 Example in Application Service

```php
use Aegisora\RuleGuardians\RequiredRule\RequiredRuleGuardian;
use App\Exceptions\InvalidValueException;

final class UserService
{
    private RequiredRuleGuardian $requiredRuleGuardian;

    public function __construct(
        RequiredRuleGuardian $requiredRuleGuardian
    ) {
        $this->requiredRuleGuardian = $requiredRuleGuardian;
    }

    /**
     * @param mixed $value
     */
    public function process($value): void
    {
        $this->requiredRuleGuardian->check($value, new InvalidValueException());

        // business logic for a present value
    }
}
```

---

## 🚨 Exceptions

This package does not define its own exception types. All errors are raised by the underlying `aegisora/guardian` package.

Both exceptions extend the abstract base class
`Aegisora\Guardian\Exceptions\GuardianException`,
so you can catch every validation error with a single `catch`:

```php
use Aegisora\Guardian\Exceptions\GuardianException;

try {
    $requiredRuleGuardian->check($value);
} catch (GuardianException $exception) {
    // handles GuardianValidationException and GuardianExecutingRuleException
}
```

### `GuardianValidationException`

Thrown when validation fails and no custom exception is provided.

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;

try {
    $requiredRuleGuardian->check(null);
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "required_rule"
}
```

### `GuardianExecutingRuleException`

Thrown when the underlying rule execution fails.

`Aegisora\Guardian\Exceptions\GuardianExecutingRuleException`

---

## 🧩 API

### `RequiredRuleGuardian::check()`

```php
/**
 * @param mixed $value
 */
public function check(
    $value,
    ?\Throwable $exception = null
): void
```

Parameters:
- `$value` *(mixed)* — value to validate; considered valid when it is not `null`
- `$exception` *(?\Throwable, default `null`)* — optional custom exception thrown on validation failure

Returns `void`. The method communicates results through exceptions only — it returns nothing on success and throws on failure:
- `GuardianValidationException` — validation failed and no custom exception was provided
- `GuardianExecutingRuleException` — the underlying rule failed to execute
- the provided custom exception — validation failed and a custom exception was passed

Example:

```php
$requiredRuleGuardian->check('foo');
```

With custom exception:

```php
$requiredRuleGuardian->check(null, new InvalidValueException());
```

---

## 🏛️ Architecture

This package is a small shortcut layer over the Aegisora validation pipeline.

Flow:
1. `RequiredRuleGuardian::check()` is called
2. `RequiredRule::create()` is created
3. `Guardian` executes the rule
4. If validation succeeds, execution continues normally
5. If validation fails, custom exception or `GuardianValidationException` is thrown
6. If rule execution fails, `GuardianExecutingRuleException` is thrown

Internal flow:

```text
Value → RequiredRuleGuardian → Guardian → RequiredRule → Result → Exception
```

---

## 🔗 Related Packages

- [aegisora/guardian](https://github.com/Aegisora/guardian) — validation execution orchestrator
- [aegisora/required-rule](https://github.com/Aegisora/required-rule) — rule-based required value validation
- [aegisora/rule-contract](https://github.com/Aegisora/rule-contract) — base rule contract and validation result architecture

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the LICENSE for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated!. See the CONTRIBUTING for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
