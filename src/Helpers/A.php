<?php

declare(strict_types=1);


namespace Assurrussa\GridView\Helpers;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class A
 * Работа с масивамми
 *
 * @package Assurrussa\AmiCMS\Components\Helpers
 */
class A
{

    public static $pathDivider = '.';

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param               $array
     * @param callable|null $callback
     * @param null          $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if(is_null($callback)) {
            if(empty($array)) {
                return $default;
            }

            foreach($array as $item) {
                return $item;
            }
        }

        foreach($array as $key => $value) {
            if(call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Evaluates the value of the specified attribute for the given object or array.
     * The attribute name can be given in a path syntax. For example, if the attribute
     * is "author.firstName", this method will return the value of "$object->author->firstName"
     * or "$array['author']['firstName']".
     * A default value (passed as the last parameter) will be returned if the attribute does
     * not exist or is broken in the middle (e.g. $object->author is null).
     *
     * Anonymous function could also be used for attribute calculation as follows:
     * <code>
     * $taskClosedSecondsAgo=self::value($closedTask,function($model) {
     *    return time()-$model->closed_at;
     * });
     * </code>
     * Your anonymous function should receive one argument, which is the object, the current
     * value is calculated from.
     *
     * @param mixed $object       This can be either an object or an array.
     * @param mixed $attribute    the attribute name (use dot to concatenate multiple attributes)
     *                            or anonymous function (PHP 5.3+). Remember that functions created by "create_function"
     *                            are not supported by this method. Also note that numeric value is meaningless when
     *                            first parameter is object typed.
     * @param mixed $defaultValue the default value to return when the attribute does not exist.
     * @return mixed the attribute value.
     */
    public static function value($object, $attribute, $defaultValue = null)
    {
        if(is_scalar($attribute)) {
            foreach(explode(self::$pathDivider, $attribute) as $name) {
                if(isset($object->$name)) {
                    $object = $object->$name;
                } elseif(isset($object[$name])) {
                    $object = $object[$name];
                } else {
                    return $defaultValue;
                }
            }
            return $object;
        } elseif(is_callable($attribute)) {
            if($attribute instanceof Closure) {
                $attribute = Closure::bind($attribute, $object);
            }
            return call_user_func($attribute, $object);
        } else {
            return null;
        }
    }


    /**
     * Return string containing a string representation of all the items,
     * with the $divider string between each element.
     * <code>
     * $str=A::join([1,2,3],',','["','"]');
     * // $str='["1"],["2"],["3"]'
     * $str=A::join(['a'=>[1,2,3]],',');
     * // array flatten $str='1,2,3'
     * $str=A::join([$user1,$user2,$user3],',');
     * // for objects joined id attribute $str='id1,id2,id3'
     * </code>
     *
     * @param array|Arrayable $array
     * @param string|array    $divider string value of divider OR array [$divider,$prefix,$postfix]
     * @param string          $prefix  add to start of value
     * @param string          $postfix add to end of  value
     * @return array
     * @uses array_walk()
     */
    public static function join($array, $divider = ',', $prefix = null, $postfix = null)
    {
        if(is_array($divider)) {
            list($divider, $prefix, $postfix) = $divider;
        }
        array_walk($array, function (&$value) use ($divider, $prefix, $postfix) {
            if(is_array($value)) {
                $value = self::join(self::createFlat($value), $divider, $prefix, $postfix);
            } else {
                if(is_object($value)) {
                    if(isset($value->id)) {
                        $value = $value->id;
                    } elseif($value instanceof \Carbon\Carbon) {
                        $value = $value->toDateTimeString();
                    } else {
                        $value = null;
                    }
                }
                $value = $prefix . $value . $postfix;
            }

        });
        return join($divider, $array);
    }

    /**
     * Return flatten a multi-dimensional array into a single level.
     *
     * <code>
     * //[1,11,22,2]
     * A::createFlat([1,'second_level'=>[11,22],2]);
     * </code>
     *
     * @param  array|Arrayable $array
     * @return array
     * @uses array_walk_recursive()
     */
    public static function createFlat($array)
    {
        $return = [];
        array_walk_recursive($array, function ($item) use (&$return) {
            $return[] = $item;
        });
        return $return;
    }
}