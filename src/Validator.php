<?php declare(strict_types=1);

namespace Nicolasvac\Fyltr;

use Nicolasvac\Fyltr\Exceptions\RuleInternalException;
use Nicolasvac\Fyltr\Exceptions\ValidationKeyFoundMultipleTimes;
use Nicolasvac\Fyltr\Rules\Rule;
use Nicolasvac\Fyltr\Rules\RuleResultStatus;
use Nicolasvac\Fyltr\Rules\RuleReturnsNumber;
use Nicolasvac\Fyltr\Rules\RuleWithTranslations;
use Nicolasvac\Fyltr\Translations\DefaultTranslationProvider;
use Nicolasvac\Fyltr\Translations\TranslationProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Validator implements MiddlewareInterface
{
    /** @var array<string, mixed> A combination of key value inputs. */
    private array $inputs;

    /** @var array<string, string> A combination of key-value for input files. The value is the file content. */
    private array $files;

    /** @var array<string, array{
     *     class: string,
     *     args: array<string>
    } The data for running the validators.
     */
    private array $validators;

    /** @var ValidationResult|null The cached validation result object. */
    private ValidationResult|null $result;

    /** @var TranslationProvider|null The default translations for the provider of messages. */
    public static TranslationProvider|null $defaultTranslationProvider = null;

    /** @var TranslationProvider The translations for the provider of messages. */
    private TranslationProvider $translationProvider;

    /**
     * @throws ValidationKeyFoundMultipleTimes
     */
    public function __construct(array|ServerRequestInterface $inputs, array $validators, TranslationProvider|null $translationsProvider = null)
    {
        if (self::$defaultTranslationProvider === null) {
            self::$defaultTranslationProvider = new DefaultTranslationProvider();
        }

        $this->translationProvider = $translationsProvider ?? self::$defaultTranslationProvider;

        $this->initializeInputs(inputs: $inputs);
        $this->initializeValidators(validators: $validators);
    }

    /**
     * @throws ValidationKeyFoundMultipleTimes
     */
    private function initializeInputs(array|ServerRequestInterface $inputs): void
    {
        // Reset or initialize the properties for a clean state.
        $this->inputs = [];
        $this->files = [];
        $this->result = null;

        if ($inputs instanceof ServerRequestInterface) {
            $this->inputs = $inputs->getParsedBody();

            if (count($inputs->getQueryParams()) > 0) {
                foreach ($inputs->getQueryParams() as $key => $value) {
                    if (isset($this->inputs[$key])) {
                        throw new ValidationKeyFoundMultipleTimes(key: $key);
                    }

                    $this->inputs[$key] = $value;
                }
            }

            if (count($inputs->getUploadedFiles()) > 0) {
                foreach ($inputs->getUploadedFiles() as $key => $value) {
                    if (isset($this->files[$key])) {
                        throw new ValidationKeyFoundMultipleTimes(key: $key);
                    }

                    //TODO: Handle files
                }
            }
        } else {
            if (count($inputs) > 0) {
                foreach ($inputs as $key => $value) {
                    if (isset($this->inputs[$key])) {
                        throw new ValidationKeyFoundMultipleTimes(key: $key);
                    }

                    $this->inputs[$key] = $value;
                }
            }
        }
    }

    private function initializeValidators(array $validators): void
    {
        $this->validators = $validators;
        $this->result = null;
    }

    /**
     * @throws RuleInternalException
     */
    private function runValidation(): ValidationResult
    {
        // Check if we have a cached result object available
        if ($this->result !== null) {
            return $this->result;
        }

        $temporaryValidationResult = [
            'errors' => [],
            'dataBag' => [],
        ];

        // Validate

        foreach ($this->validators as $keyToValidate => $rulesForTheKey) {
            foreach ($rulesForTheKey as $rawRule) {
                /** @var Rule $rule */
                $rule = new $rawRule['class'];

                if ($rule instanceof RuleWithTranslations) {
                    $ruleTranslations = $this->translationProvider->ruleTranslations(rule: $rule);

                    if (count($ruleTranslations) === 0) {
                        throw new RuleInternalException("No translations provided for rule {$rawRule['class']}");
                    }

                    $rule->setTranslations(messages: $ruleTranslations);
                }

                $valueToValidate = $this->inputs[$keyToValidate] ?? null;

                $result = $rule->validate(key: $keyToValidate, value: $valueToValidate, args: $rawRule['args'] ?? []);

                if ($result->status === RuleResultStatus::Successful) {
                    // The rule was successful, so we can save the data for this key.
                    $temporaryValidationResult['dataBag'][$keyToValidate][ValidationResultDataTypes::Raw->value] = $valueToValidate;

                    if ($rule instanceof RuleReturnsNumber) {
                        $temporaryValidationResult['dataBag'][$keyToValidate][ValidationResultDataTypes::Number->value] = $rule->getValidatedNumber();
                    }
                } else {
                    // Save the errors for this key.
                    if (!isset($temporaryValidationResult['errors'][$keyToValidate])) {
                        $temporaryValidationResult['errors'][$keyToValidate] = [];
                    }

                    $temporaryValidationResult['errors'][$keyToValidate] = array_merge(
                        $temporaryValidationResult['errors'][$keyToValidate],
                        $result->errors
                    );

                    // If the rule tells us to stop the validation for this key, we must exit the key cycle
                    // so the other rules won't be executed.
                    if ($result->status === RuleResultStatus::FailedAndStopped) {
                        break;
                    }
                }
            }
        }

        return new ValidationResult(
            successful: count($temporaryValidationResult['errors']) === 0,
            errors: $temporaryValidationResult['errors'],
            dataBag: $temporaryValidationResult['dataBag'],
        );
    }

    /**
     * This method allows you to use again this object, by resetting all states
     * and making a new fresh validation based on the input data.
     *
     * If you don't pass any inputs or validators, the respective ones passed in the constructor will be used.
     * If you don't pass any translation provider, the default one will be used.
     *
     *
     * @throws ValidationKeyFoundMultipleTimes|RuleInternalException
     */
    public function validate(
        array|ServerRequestInterface $inputs = [],
        array                        $validators = [],
        TranslationProvider|null     $translationProvider = null
    ): ValidationResult
    {
        if (count($inputs) > 0) {
            $this->initializeInputs(inputs: $inputs);
        }

        if (count($validators) > 0) {
            $this->initializeValidators(validators: $validators);
        }

        if ($translationProvider !== null) {
            $this->translationProvider = $translationProvider;
        }

        return $this->runValidation();
    }

    /**
     * Allows you to add a validator to a PSR-15 compliant middleware system,
     * and you can use it in your request processing with the attribute "validation".
     *
     * If you set the attribute "Fyltr-TranslationsProvider" in the request,
     * the validator will use the translations from that provider.
     *
     *
     * @throws ValidationKeyFoundMultipleTimes|RuleInternalException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var TranslationProvider $translationProvider */
        $translationProvider = $request->getAttribute(
            name: 'Fyltr-TranslationsProvider',
            default: $this->translationProvider,
        );

        return $handler->handle(
            request: $request->withAttribute(
                name: 'validation',
                value: $this->validate(inputs: $request, translationProvider: $translationProvider)
            )
        );
    }

    /**
     * This allows you to rapidly check for a single value instead of initializing as an array.
     *
     * @throws ValidationKeyFoundMultipleTimes|RuleInternalException
     *
     */
    public static function inline(mixed $value, array $validators): ValidationResult
    {
        return (new self(['value' => $value], ['value' => $validators]))->validate();
    }

    /**
     * @throws ValidationKeyFoundMultipleTimes|RuleInternalException
     */
    public function __invoke(): ValidationResult
    {
        return $this->validate();
    }
}
