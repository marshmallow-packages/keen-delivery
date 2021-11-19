<?php

namespace Marshmallow\KeenDelivery\Nova;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;

/**
 * This class handles the behaviour of the
 * nova resource for this model.
 *
 * @category Product
 * @package  Product
 * @author   Stef van Esch <stef@marshmallow.dev>
 * @license  MIT Licence
 * @link     https://marshmallow.dev
 */
class KeenDelivery extends Resource
{
    public static $model = 'Marshmallow\KeenDelivery\Models\KeenDelivery';

    public static $title = 'product_name';

    public static $search = [
        //
    ];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    /**
     * Fields for this resource. Extended by the fields
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of fields
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            //
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    /**
     * Cards for this resource. Extended by the cards
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of cards
     */
    public function cards(Request $request)
    {
        return [];
    }


    /**
     * Filters for this resource. Extended by the filters
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of filters
     */
    public function filters(Request $request)
    {
        return [];
    }


    /**
     * Lenses for this resource. Extended by the lenses
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of lenses
     */
    public function lenses(Request $request)
    {
        return [];
    }


    /**
     * Actions for this resource. Extended by the actions
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of actions
     */
    public function actions(Request $request)
    {
        return [];
    }
}
