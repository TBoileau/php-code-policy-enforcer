# 🛠️ PHP Code Policy Enforcer

## 🎯 Goals

**PHP Code Policy Enforcer** is a tool inspired by the functionality of PHPArkitect, designed to enhance and maintain
the quality and structure of your PHP applications. It focuses on automating enforcement of coding standards and
architectural rules.

### ✨ Features

- **Dependency Checking:** Similar to Deptrac, it checks for and manages dependencies within the application. 📦
- **Detailed Analysis:** Analyzes the implementation of classes, methods, properties, method parameters, and their types
  to ensure compliance with defined standards. 🔍

## 📦 Installation

To install the **PHP Code Policy Enforcer** package, run the following command in your project directory:

```bash
composer require tboileau/php-code-policy-enforcer
```

## ⚙️ Configuration

To configure the **PHP Code Policy Enforcer**, create a configuration file named `php-code-policy-enforcer.php` (or a
custom name) in your project.

### Exemple

```php
<?php

return CodePolicy::in(__DIR__ . '/src')
    ->add(
        that(residesIn('App\Tests'))
            ->should(not(isSubclassOf(TestCase::class)))
            ->because('each class must be a test class')
    )
    ->add(
        that(
            residesIn('App'),
            orX(
                containsMethods(5),
                not(isFinal()),
                andX(
                    isEnum(),
                    uses(Xyzzy::class),
                    not(implementsInterface(Bar::class))
                )
            )
        )->should(
            hasMethod('test'),
            orX(
                not(isAbstract()),
                xorX(
                    not(isInterface()),
                    hasMethod(
                        'test',
                        andX(
                            containsParameters(greaterThan(1)),
                            hasParameter('foo'),
                            orX(
                                hasParameter('bar'),
                                hasParameter('baz')
                            )
                        )
                    )
                )
            )
        )
        ->because('this is a dumb example')
    );
```

### Configuration Details

- **Set Analysis Directory:** Use `CodePolicy::in()` with a directory path, e.g., `CodePolicy::in(__DIR__ . '/src')`.
- **Add Rules:** Use the `add` method to define rules. You can add multiple rules for different checks.
- **Filtering Classes:** Utilize the `that` function to specify validators that will filter the classes to be analyzed.
- **Define Validators:** Use the `should` method to determine the validators that will assess the filtered classes.
- **Explain Rules:** Apply the `because` method to provide a reason for each rule, explaining why it is implemented.

## 🚀 Usage

To use the **PHP Code Policy Enforcer**, simply run the following command in your terminal:

```bash
php vendor/bin/php-code-policy-enforcer check --config=php-code-policy-enforcer.php
```

This command will execute the code policy checks based on the rules defined in your `php-code-policy-enforcer.php`
configuration file.

## 🧐 Validators

Below is a table listing the available validators, organized by category, with descriptions of their utility and how
they can be combined with expressions or comparison operators for detailed validation logic.

### Classes

| Validator             | Utility                                                                                                                            |
|-----------------------|------------------------------------------------------------------------------------------------------------------------------------|
| `containsMethods`     | Checks if a class contains a specified number of methods. Can be combined with comparison operators like `equalTo`, `greaterThan`. |
| `containsProperties`  | Checks if a class contains a specified number of properties. Accepts comparison operators.                                         |
| `dependsOn`           | Checks if a class depends on specific namespaces.                                                                                  |
| `hasAttribute`        | Checks if a class has a specific attribute.                                                                                        |
| `hasConstant`         | Checks if a class has a specific constant.                                                                                         |
| `hasConstructor`      | Checks if a class has a constructor.                                                                                               |
| `hasMethod`           | Checks if a class has a specific method. Can be nested with further method-specific validators.                                    |
| `hasProperty`         | Checks if a class has a specific property. Can be nested with further property-specific validators.                                |
| `implementsInterface` | Checks if a class implements a specific interface.                                                                                 |
| `isAbstract`          | Checks if a class is abstract.                                                                                                     |
| `isAnonymous`         | Checks if a class is anonymous.                                                                                                    |
| `methods`             | Applies an operator or validator to all methods in a class.                                                                        |
| `properties`          | Applies an operator or validator to all properties in a class.                                                                     |
| `inNamespace`         | Checks if a class resides within a specific namespace.                                                                             |
| `isCloneable`         | Checks if a class is cloneable.                                                                                                    |
| `isCountable`         | Checks if an instance of a class can be counted with `count()`.                                                                    |
| `isEnum`              | Checks if a class is an enumeration.                                                                                               |
| `isFinal`             | Checks if a class is declared as final.                                                                                            |
| `isInterface`         | Checks if a class is an interface.                                                                                                 |
| `isInstantiable`      | Checks if a class is instantiable.                                                                                                 |
| `isInternal`          | Checks if a class is defined internally by PHP or its extensions.                                                                  |
| `isInvokable`         | Checks if a class has an implemented `__invoke` method.                                                                            |
| `isIterable`          | Checks if a class is iterable.                                                                                                     |
| `isIterateable`       | Checks if a class can be iterated.                                                                                                 |
| `isReadOnly`          | Checks if a class is read-only.                                                                                                    |
| `isSubclassOf`        | Checks if a class is a subclass of another class.                                                                                  |
| `isTrait`             | Checks if a class is actually a trait.                                                                                             |
| `isUserDefined`       | Checks if a class is user-defined, as opposed to being built-in.                                                                   |
| `matchWith`           | Checks if the class name matches a given regular expression.                                                                       |
| `residesIn`           | Checks if a class resides within one or more specific namespaces.                                                                  |

### Methods

| Validator            | Utility                                                                                                  |
|----------------------|----------------------------------------------------------------------------------------------------------|
| `containsParameters` | Checks if a method contains a specified number of parameters. Can be combined with comparison operators. |
| `hasParameter`       | Checks if a method has a specific parameter. Can be nested with further parameter-specific validators.   |
| `hasReturnType`      | Checks if a method has a specific return type.                                                           |
| `isAbstract`         | Checks if a method is abstract.                                                                          |
| `isFinal`            | Checks if a method is final.                                                                             |
| `isPrivate`          | Checks if a method is private.                                                                           |
| `isProtected`        | Checks if a method is protected.                                                                         |
| `isPublic`           | Checks if a method is public.                                                                            |
| `isStatic`           | Checks if a method is static.                                                                            |
| `matchWith`          | Checks if the method name matches a given regular expression.                                            |

### Parameters

| Validator             | Utility                                                                                         |
|-----------------------|-------------------------------------------------------------------------------------------------|
| `hasDefaultValue`     | Checks if a parameter has a default value.                                                      |
| `isOptional`          | Checks if a parameter is optional.                                                              |
| `isPassedByReference` | Checks if a parameter is passed by reference.                                                   |
| `isVariadic`          | Checks if a parameter is variadic.                                                              |
| `hasIntersectionType` | Checks if a parameter is an intersection of types. Can be nested with type-specific validators. |
| `hasUnionType`        | Checks if a parameter is a union of types. Can be nested with type-specific validators.         |
| `hasNamedType`        | Checks if a parameter has a specific named type.                                                |
| `matchWith`           | Checks if the parameter name matches a given regular expression.                                |

### Types

| Validator    | Utility                                                       |
|--------------|---------------------------------------------------------------|
| `is`         | Allows adding a validator or operator if the type is a class. |
| `isClass`    | Checks if a type is a specific class.                         |
| `isStatic`   | Checks if a type is static.                                   |
| `isSelf`     | Checks if a type refers to 'self'.                            |
| `isParent`   | Checks if a type refers to 'parent'.                          |
| `isTrue`     | Checks if a type is boolean true.                             |
| `isNever`    | Checks if a type is 'never' (used in return types).           |
| `isMixed`    | Checks if a type is mixed.                                    |
| `isIterable` | Checks if a type is iterable.                                 |
| `isFalse`    | Checks if a type is boolean false.                            |
| `isNull`     | Checks if a type is null.                                     |
| `isCallable` | Checks if a type is callable.                                 |
| `isBuiltIn`  | Checks if a type is built-in by PHP.                          |

### Logical Operators

| Operator | Utility                                           |
|----------|---------------------------------------------------|
| `andX`   | Combines multiple expressions with a logical AND. |
| `orX`    | Combines multiple expressions with a logical OR.  |
| `xorX`   | Combines multiple expressions with a logical XOR. |
| `not`    | Negates an expression.                            |

### Comparison Operators

| Operator             | Description                                                          |
|----------------------|----------------------------------------------------------------------|
| `equalTo`            | Checks if the value equals the specified value.                      |
| `greaterThan`        | Checks if the value is greater than the specified value.             |
| `greaterThanOrEqual` | Checks if the value is greater than or equal to the specified value. |
| `lessThan`           | Checks if the value is less than the specified value.                |
| `lessThanOrEqual`    | Checks if the value is less than or equal to the specified value.    |

This setup allows for highly flexible and powerful validations based on specific criteria tailored to the structure and
requirements of your PHP classes, methods, properties, and parameters. Validators can be combined with logical and
comparison operators to create complex validation rules.

## 🛠 Extending

### Creating Custom Validators

To extend the capabilities of the PHP Code Policy Enforcer by creating custom validators, you will need to understand
the structure and composition of the existing validators. A validator in this library typically involves creating a
function that returns a `ConditionalExpression`. This expression will contain the logic required to validate specific
aspects of your codebase, such as class attributes, methods, properties, etc.

#### Basic Structure of a Validator

Here is a basic outline of what a custom validator function looks like:

```php
use ReflectionClass; // Or other relevant Reflection types.
use TBoileau\PhpCodePolicyEnforcer\Expression\ConditionalExpression;

function myCustomValidator(): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'myCustomValidator', // A unique name for the validator.
        validator: static fn (ReflectionClass $value): bool => {
            // Insert your custom validation logic here.
            return true; // Return true if the validation passes.
        },
        message: 'descriptive error message if the validation fails'
    );
}
```

#### Example: Creating a Validator to Check Class Name Length

Suppose you want to create a validator to check if the names of classes exceed a certain length. You would use the
following approach:

```php
function classNameLength(int $maxLength): ConditionalExpression
{
    return new ConditionalExpression(
        name: 'classNameLength',
        validator: static fn (ReflectionClass $value): bool => strlen($value->getName()) <= $maxLength,
        parameters: ['maxLength' => $maxLength],
        message: 'Class name should not exceed {{ maxLength }} characters.'
    );
}
```

#### Integrating Custom Validators

Once you have created your custom validator, you can integrate it into your code policy configurations just like any
other validator:

```php
use TBoileau\PhpCodePolicyEnforcer\CodePolicy;

return CodePolicy::in(__DIR__ . '/src')
    ->add(
        that(residesIn('MyApp'))
            ->should(classNameLength(10))
            ->because('We prefer shorter class names for simplicity.')
    );

```

### Best Practices

When creating custom validators, consider the following best practices:

* **Reusability**: Design validators that can be reused across different projects or parts of the same project.
* **Clarity**: Name your validators clearly and provide meaningful error messages to help developers understand what
  rules they violated.
* **Performance**: Ensure that your validators do not introduce significant performance overhead, especially if they
  need to handle large codebases.

By following these guidelines, you can extend the PHP Code Policy Enforcer effectively and tailor it to meet the
specific needs of your projects.

