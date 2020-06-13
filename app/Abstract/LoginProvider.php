<?php

abstract class Abstract_LoginProvider
{
    public abstract function login();

    protected static string $actionName;
    protected static string $friendlyName;
    protected static string $formFileName;

    public static function getActionName()
    {
        $c = get_called_class();
        return $c::$actionName;
    }

    public static function getFormFileName()
    {
        $c = get_called_class();
        return $c::$formFileName;
    }

    public static function getName()
    {
        $c = get_called_class();
        return $c::$friendlyName;
    }
}
