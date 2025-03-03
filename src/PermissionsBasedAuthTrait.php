<?php

namespace Gabrielesbaiz\NovaSpatiePermissions;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Auth\Access\AuthorizationException;

trait PermissionsBasedAuthTrait
{
    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return true;
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return true;
        }

        return static::hasPermissionsTo($request, 'viewAny');
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return static::hasPermissionsTo($request, 'create');
    }

    public static function hasPermissionsTo(Request $request, $ability)
    {
        if ($request->GetRequestUri() == config('nova.path') . '/login' || $request->GetRequestUri() == config('nova.path') . '/password/reset') {
            return true;
        }

        if (isset(static::$permissionsForAbilities[$ability])) {
            return $request->user()->can(static::$permissionsForAbilities[$ability]);
        }

        if (isset(static::$permissionsForAbilities['all'])) {
            return $request->user()->can(static::$permissionsForAbilities['all']);
        }

        return false;
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function authorizeToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return;
        }

        return $this->authorizeTo($request, 'viewAny');
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param Request $request
     *
     * @throws AuthorizationException
     */
    public function authorizeToView(Request $request)
    {
        return $this->authorizeTo($request, 'view') && $this->authorizeToViewAny($request);
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param NovaRequest                                $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAdd(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'add' . class_basename($model);

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param NovaRequest                                $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAttachAny(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attachAny' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param NovaRequest                                $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAttach(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attach' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param NovaRequest                                $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string                                     $relationship
     *
     * @return bool
     */
    public function authorizedToDetach(NovaRequest $request, $model, $relationship)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'detach' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param Request $request
     * @param string  $ability
     *
     * @throws AuthorizationException
     */
    public function authorizeTo(Request $request, $ability)
    {
        throw_unless($this->authorizedTo($request, $ability), AuthorizationException::class);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param Request $request
     * @param string  $ability
     *
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        return static::authorizable() ? static::hasPermissionsTo($request, $ability) : true;
    }
}
