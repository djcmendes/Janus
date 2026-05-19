<?php

declare(strict_types=1);

/**
 * NECROCON — Janus reusable PHP CS Fixer ruleset.
 *
 * Usage in .php-cs-fixer.dist.php:
 *
 *   require_once __DIR__ . '/necrocon/php-cs-fixer/JanusRules.php';
 *
 *   return (new PhpCsFixer\Config())
 *       ->setRules(JanusRules::rules())
 *       ->setFinder(JanusRules::finder(__DIR__));
 */
final class JanusRules
{
    /**
     * Returns the full PSR-12 + PHP 8.x rule set used across all Janus projects.
     *
     * @return array<string, bool|array<string, mixed>>
     */
    public static function rules(): array
    {
        return [
            // ── Base standard ──────────────────────────────────────────────
            '@PSR12'                                         => true,
            '@PSR12:risky'                                   => true,
            '@PHP82Migration'                                => true,
            '@PHP82Migration:risky'                          => true,

            // ── Strict types ───────────────────────────────────────────────
            'declare_strict_types'                           => true,

            // ── Class style ────────────────────────────────────────────────
            'final_class'                                    => false, // enforced by architecture, not fixer
            'no_unneeded_final_method'                       => true,
            'self_accessor'                                  => true,
            'self_static_accessor'                           => true,

            // ── Imports ────────────────────────────────────────────────────
            'ordered_imports'                                => ['sort_algorithm' => 'alpha'],
            'no_unused_imports'                              => true,
            'global_namespace_import'                        => [
                'import_classes'   => true,
                'import_constants' => false,
                'import_functions' => false,
            ],
            'fully_qualified_strict_types'                   => true,

            // ── Strings ────────────────────────────────────────────────────
            'single_quote'                                   => ['strings_containing_single_quote_chars' => false],
            'explicit_string_variable'                       => true,
            'no_binary_string'                               => true,

            // ── Arrays ─────────────────────────────────────────────────────
            'array_syntax'                                   => ['syntax' => 'short'],
            'trailing_comma_in_multiline'                    => ['elements' => ['arrays', 'arguments', 'parameters', 'match']],
            'trim_array_spaces'                              => false,
            'no_whitespace_before_comma_in_array'            => true,
            'normalize_index_brace'                          => true,

            // ── Functions / methods ────────────────────────────────────────
            'void_return'                                    => true,
            'return_type_declaration'                        => ['space_before' => 'none'],
            'no_useless_return'                              => true,
            'simplified_null_return'                         => true,
            'nullable_type_declaration_for_default_null_value' => true,
            'nullable_type_declaration'                      => ['syntax' => 'union'],

            // ── Control flow ───────────────────────────────────────────────
            'no_useless_else'                                => true,
            'no_superfluous_elseif'                          => true,
            'yoda_style'                                     => false,
            'ternary_to_null_coalescing'                     => true,
            'modernize_strpos'                               => true,

            // ── PHPDoc ─────────────────────────────────────────────────────
            'phpdoc_order'                                   => true,
            'phpdoc_trim'                                    => true,
            'phpdoc_no_empty_return'                         => true,
            'phpdoc_scalar'                                  => true,
            'phpdoc_types'                                   => true,
            'no_empty_phpdoc'                                => true,
            'no_superfluous_phpdoc_tags'                     => ['allow_mixed' => true, 'remove_inheritdoc' => false],
            'phpdoc_align'                                   => ['align' => 'left'],

            // ── Whitespace / blank lines ───────────────────────────────────
            'no_extra_blank_lines'                           => [
                'tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default',
                             'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use'],
            ],
            'blank_line_before_statement'                    => [
                'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
            ],
            'class_attributes_separation'                    => ['elements' => ['method' => 'one', 'property' => 'one']],

            // ── Operators ──────────────────────────────────────────────────
            'binary_operator_spaces'                         => ['default' => 'single_space'],
            'unary_operator_spaces'                          => true,
            'concat_space'                                   => ['spacing' => 'one'],
            'not_operator_with_successor_space'              => false,

            // ── Casting ────────────────────────────────────────────────────
            'cast_spaces'                                    => ['space' => 'single'],
            'lowercase_cast'                                 => true,
            'short_scalar_cast'                              => true,
            'no_short_bool_cast'                             => true,

            // ── Enforces proper multi-line alignment for fluent interfaces
            'method_chaining_indentation'                    => true,

            // ── Ensures the operator -> goes onto the new line when split
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'new_line_for_chained_calls'
            ],
        ];
    }

    /**
     * Returns a Finder pre-configured for a standard Symfony project layout.
     */
    public static function finder(string $projectDir): \PhpCsFixer\Finder
    {
        return (new \PhpCsFixer\Finder())
            ->in($projectDir)
            ->exclude(['var', 'vendor', 'node_modules', 'necrocon'])
            ->notPath([
                'config/bundles.php',
                'config/reference.php',
            ])
            ->notName(['*.blade.php'])
            ->name('*.php');
    }
}
