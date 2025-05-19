# Usages

## Complete standalone usage example

```php
$translationProvider = new class implements \Nicolasvac\Fyltr\Translations\TranslationProvider {
    public function ruleErrorMessage(Rule $rule): string
    {
        return match ($rule::class) {
            \Nicolasvac\Fyltr\Rules\RequiredRule::class => 'The field :key: is required by our example.',
        };
    }
}

$validator = new \Nicolasvac\Fyltr\Validator(
    inputs: [
        'name' => ''
    ],
    validators: [
        'name' => [\Nicolasvac\Fyltr\Rules\Rules::required()]
    ]
)

$result = $validator->validate();

if ($result->successful()) {
   echo 'Yeey! We passed.' 
} else {
   echo 'Oh no. We failed: ' . $result->errorsString(separator: '<br>');
}

// You can even reuse me and keep old inputs or validators without having to rewrite them!

$result = $validator->validate(inputs: ['name' => 'Example!']);

if ($result->successful()) {
   echo 'Yeey! We passed.' 
} else {
   echo 'Oh no. We failed: ' . $result->errorsString(separator: '<br>');
}
```
