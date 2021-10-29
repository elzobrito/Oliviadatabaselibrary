<?php
namespace OliviaDatabaseLibrary;

abstract class ADatabase
{
    public abstract static function getDB($driver = null);
}
