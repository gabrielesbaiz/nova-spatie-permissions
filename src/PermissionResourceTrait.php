<?php

namespace Gabrielesbaiz\NovaSpatiePermissions;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Spatie\Permission\PermissionRegistrar;

trait PermissionResourceTrait
{
    public static function getModel()
    {
        return app(PermissionRegistrar::class)->getPermissionClass();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     *
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        $roleResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass());

        return [
            ID::make()->sortable(),

            Text::make(__('nova-spatie-permissions::lang.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.permissions'))
                ->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Text::make(__('nova-spatie-permissions::lang.display_name'), function () {
                return __('nova-spatie-permissions::lang.display_names.' . $this->name);
            })->canSee(function () {
                return is_array(__('nova-spatie-permissions::lang.display_names'));
            }),

            Select::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            DateTime::make(__('nova-spatie-permissions::lang.created_at'), 'created_at')->exceptOnForms(),

            DateTime::make(__('nova-spatie-permissions::lang.updated_at'), 'updated_at')->exceptOnForms(),

            BelongsToMany::make($roleResource::label(), 'roles', $roleResource)->searchable(),

            // MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
        ];
    }
}
