<?php

namespace App\Support;

class UserPermissions
{
    public const VIEW_ALL_FORMS = 'view_all_forms';

    public const EDIT_ANY_FORM = 'edit_any_form';

    public const DELETE_ANY_FORM = 'delete_any_form';

    public const VIEW_ANY_RESPONSES = 'view_any_responses';

    public const EXPORT_ANY_RESPONSES = 'export_any_responses';

    public const DELETE_ANY_RESPONSES = 'delete_any_responses';

    /** @return list<string> */
    public static function keys(): array
    {
        return [
            self::VIEW_ALL_FORMS,
            self::EDIT_ANY_FORM,
            self::DELETE_ANY_FORM,
            self::VIEW_ANY_RESPONSES,
            self::EXPORT_ANY_RESPONSES,
            self::DELETE_ANY_RESPONSES,
        ];
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::VIEW_ALL_FORMS => __('app.permissions.view_all_forms'),
            self::EDIT_ANY_FORM => __('app.permissions.edit_any_form'),
            self::DELETE_ANY_FORM => __('app.permissions.delete_any_form'),
            self::VIEW_ANY_RESPONSES => __('app.permissions.view_any_responses'),
            self::EXPORT_ANY_RESPONSES => __('app.permissions.export_any_responses'),
            self::DELETE_ANY_RESPONSES => __('app.permissions.delete_any_responses'),
        ];
    }

    /** @return array<string, string> */
    public static function descriptions(): array
    {
        return [
            self::VIEW_ALL_FORMS => __('app.permissions.view_all_forms_desc'),
            self::EDIT_ANY_FORM => __('app.permissions.edit_any_form_desc'),
            self::DELETE_ANY_FORM => __('app.permissions.delete_any_form_desc'),
            self::VIEW_ANY_RESPONSES => __('app.permissions.view_any_responses_desc'),
            self::EXPORT_ANY_RESPONSES => __('app.permissions.export_any_responses_desc'),
            self::DELETE_ANY_RESPONSES => __('app.permissions.delete_any_responses_desc'),
        ];
    }

    /** @return array<string, string> */
    public static function groups(): array
    {
        return [
            self::VIEW_ALL_FORMS => 'forms',
            self::EDIT_ANY_FORM => 'forms',
            self::DELETE_ANY_FORM => 'forms',
            self::VIEW_ANY_RESPONSES => 'responses',
            self::EXPORT_ANY_RESPONSES => 'responses',
            self::DELETE_ANY_RESPONSES => 'responses',
        ];
    }
}
